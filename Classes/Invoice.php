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
        $wallet_total = 0;
        $receivable_total = 0;
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

        $wallet_id = $ledger->getLedgerId('wallet');
        $receivable_id = $ledger->getLedgerId('accounts_receivable');

        // Get wallet total
        $wallet_ledger = $ledger->getLedgerTotal($wallet_id);
        $wallet_total = $wallet_ledger['total'];


        // Get receivable total
        $receivable_ledger = $ledger->getLedgerTotal($receivable_id);
        $receivable_total = $receivable_ledger['total'];

        //Process receivable first
        if ($receivable_total) {
            $balance = $payments_total - $receivable_total;
            if ($balance <= 0) {
                $journal->journalEntry('Repayment of past debt', -1 * abs($payments_total), $partner_id, $receivable_id);
                $payments_total = 0;
            } else {
                $payments_total = $balance;
                $journal->journalEntry('Repayment of past debt', -1 * abs($receivable_total), $partner_id, $receivable_id);
            }
        }

        // process invoices
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

        if ($invoices_total) {
            $wallet_id = $ledger->getLedgerId('wallet');
            $receivable_id = $ledger->getLedgerId('accounts_receivable');

            if ($wallet_total) {
                $balance = $wallet_total - $invoices_total;
                if ($balance >= 0) {
                    $journal->journalEntry('Payment By wallet worth ' + abs($invoices_total), -1 * abs($invoices_total), $partner_id, $wallet_id);
                    $invoices_total = 0;
                } else {
                    $invoices_total = $invoices_total - $wallet_total;
                    $journal->journalEntry('Payment By wallet worth' + abs($wallet_total), -1 * abs($wallet_total), $partner_id, $wallet_id);
                }
            }

            $balance = $payments_total - $invoices_total;

            if ($balance > 0) {
                $journal->journalEntry('Credit to wallet worth ' + abs($balance), abs($balance), $partner_id, $wallet_id);
            } elseif ($balance < 0) {
                $journal->journalEntry('Partner Debt of ' + abs($balance), abs($balance), $partner_id, $receivable_id);
            }
        }
    }
}
