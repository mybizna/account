<?php

namespace Modules\Account\Classes;

use Illuminate\Support\Facades\DB;
use Modules\Account\Classes\Payment;
use Modules\Account\Classes\Journal;
use Modules\Account\Classes\Ledger;

class Invoice
{
    public function generateInvoice($partner_id, $items = [], $description = 'Invoice #', $amount_paid = 0.00, $gateway_id = '')
    {

        $payment = new Payment();
        DB::beginTransaction();

        try {
            $status = ($amount_paid > 0) ? 'pending' : 'draft';
            $invoice_id = DB::table('account_invoice')->insertGetId(
                [
                    'partner_id' => $partner_id,
                    'description' => $description,
                    'status' => $status,
                ]
            );

            foreach ($items as $item_key => $item) {
                $invoice_item_id = DB::table('account_invoice_item')->insertGetId(
                    [
                        'invoice_id' => $invoice_id,
                        'ledger_id' => $item['ledger_id'],
                        'price' => $item['price'],
                        'amount' => $item['total'],
                        'description' => $item['title'],
                        'quantity' => $item['quantity'],
                    ]
                );
                foreach ($item['rates'] as $rate_key => $rate) {
                    DB::table('account_invoice_item_rate')->insert(
                        [
                            'invoice_item_id' => $invoice_item_id,
                            'rate_id' => $rate['id'],
                            'title' => $rate['title'],
                            'slug' => $rate['slug'],
                            'value' => $rate['value'],
                            'is_percent' => $rate['is_percent'],
                        ]
                    );
                }
            }

            if ($amount_paid > 0) {
                $description = "Invoice#$invoice_id Payment.";
                $payment->makePayment($partner_id, $description, $amount_paid, $gateway_id);
            }


            $this->reconcileInvoices($partner_id);

            DB::commit();
        } catch (\Throwable $th) {
            throw $th;
            DB::rollback();
        }
    }

    public function reconcileInvoices($partner_id)
    {
        $journal = new Journal();
        $ledger = new Ledger();
        $invoices_total = 0;
        $payments_total = 0;
        $deposit_overpayment_total = 0;
        $payments_title = '';

        // Add all payments to journal entry
        $payments =  DB::table('account_payment AS ap')
            ->select('ap.*', 'ag.ledger_id')
            ->leftJoin('account_gateway AS ag', 'ag.id', '=', 'ap.gateway_id')
            ->where('ap.partner_id', $partner_id)
            ->where('ap.is_posted', false)
            ->where('ap.type', 'in')->get();
        foreach ($payments as $payment_key => $payment) {
            $payments_total = $payments_total + $payment->amount;
            $journal->journalEntry($payment->title, $payment->amount, $payment->partner_id, $payment->ledger_id);
        }

        // Get overpayment and deposits
        $deposit_n_over_payment_id = $ledger->getLedgerId('deposit_n_over_payment');
        $deposit_overpayment_ledger = $ledger->getLedgerTotal($deposit_n_over_payment_id);
        $deposit_overpayment_total = $deposit_overpayment_ledger['total'];


        $invoices =  DB::table('account_invoice')
            ->where('partner_id', $partner_id)
            ->where('status', 'pending')
            ->orWhere('status', 'partial')
            ->orderByDesc('id');

        foreach ($invoices as $payment_key => $invoice) {
            if ($invoice->is_posted = false) {
                $items = DB::table('account_invoice_item')->where('invoice_id', $invoice->id)->get();
                foreach ($items as $key => $item) {
                    $amount = $item_total = ($item->quantity) ? $item->price * $item->quantity : $item->price;
                    $title = ($item->description) ? $item->description : "Item ID:$item->id";
                    $journal->journalEntry($title, $amount, $invoice->partner_id, $item->ledger_id);

                    $item_rates = DB::table('account_invoice_item_rate AS air')
                        ->select('air.*', 'at.ledger_id AS rate_ledger_id', 'at.title AS rate_title', 'at.method AS rate_method')
                        ->leftJoin('account_rate AS at', 'at.id', '=', 'air.rate_id')
                        ->where('invoice_item_id', $item->invoice_item_id)
                        ->orderBy('method')
                        ->get();
                    foreach ($item_rates as $item_key => $item_rate) {
                        $value = $calc_amount = ($item_rate->value) ? $item_rate->value : 0;
                        $title =  ($item_rate->title) ? $item_rate->title : "Item ID:$item->id Rate:$item_rate->rate_title";

                        if ($value != 0) {
                            if ($item_rate->rate_method == '-') {
                                $calc_amount = -1 * $value;
                            } elseif ($item_rate->rate_method == '-%') {
                                $calc_amount = -1 * $item_total * $value / 100;
                            } elseif ($item_rate->rate_method == '+%') {
                                $calc_amount =  $item_total * $value / 100;
                            }
                        }

                        $item_total = $item_total + $calc_amount;

                        $journal->journalEntry($title, $calc_amount, $invoice->partner_id, $item_rate->rate_ledger_id);
                    }
                    $invoices_total = $invoices_total + $item_total;
                }
            }

            DB::table('account_invoice')->where('invoice_id', $invoice->id)->update(['is_posted' => true]);
        }

        if ($deposit_overpayment_total) {
            if ($deposit_overpayment_total == $invoices_total) {
                $deposit_n_over_payment_id = $ledger->getLedgerId('deposit_n_over_payment');
                $journal->journalEntry('Payment By deposit & over payment'+ $payments_title, $deposit_overpayment_total, $invoice->partner_id, $deposit_n_over_payment_id);
            }
        }



        if ($payments_total == $invoices_total) {
            $accounts_receivable = $ledger->getLedgerId('accounts_receivable');
        }

        if ($payments_total > $invoices_total) {
            $journal->journalEntry('Payment from Wallet:' + $payments_title, $calc_amount, $invoice->partner_id, $item_rate->rate_ledger_id);

            $accounts_receivable = $ledger->getLedgerId('accounts_receivable');
        }
        if ($payments_total < $invoices_total) {
            $accounts_receivable = $ledger->getLedgerId('accounts_receivable');
        }
    }
}
