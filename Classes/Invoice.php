<?php

namespace Modules\Account\Classes;

use Illuminate\Support\Facades\DB;
use Modules\Account\Classes\Payment;
use Modules\Account\Classes\Journal;
use Modules\Account\Classes\Ledger;

class Invoice
{
    public function generateInvoice($title, $partner_id, $items = [], $status = 'draft', $gateways = [], $description = "")
    {

        $ledger = new Ledger();
        $payment = new Payment();
        DB::beginTransaction();

        try {
            $invoice_id = DB::table('account_invoice')->insertGetId(
                [
                    'title' => $title,
                    'partner_id' => $partner_id,
                    'description' => $description,
                    'status' => $status,
                ]
            );

            $sales_revenue_id = $ledger->getLedgerId('sales_revenue');

            foreach ($items as $item_key => $item) {
                $invoice_item_id = DB::table('account_invoice_item')->insertGetId(
                    [
                        'title' => $item['title'],
                        'invoice_id' => $invoice_id,
                        'ledger_id' => $item['ledger_id'] ? $item['ledger_id'] : $sales_revenue_id,
                        'price' => $item['price'],
                        'amount' => $item['total'],
                        'quantity' => $item['quantity'],
                    ]
                );
                foreach ($item['rates'] as $rate_key => $rate) {
                    DB::table('account_invoice_item_rate')->insert(
                        [
                            'title' => $rate['title'],
                            'slug' => $rate['slug'],
                            'rate_id' => $rate['id'],
                            'invoice_item_id' => $invoice_item_id,
                            'method' => $rate['method'],
                            'value' => $rate['value'],
                            'params' => $rate['params'],
                            'ordering' => $rate['ordering'],
                            'on_total' => $rate['on_total'],
                        ]
                    );
                }
            }

            foreach ($gateways as $item_key => $gateway) {
                if ($gateway['paid_amount'] && $status == 'draft') {
                    $status = 'pending';
                }

                $title = $gateway['title'] . " Payment. " . $gateway['reference'] . ' ' . $gateway['others'];
                $payment->makePayment($partner_id, $title, $gateway['paid_amount'], $gateway['id']);
            }

            if ($description == 'Invoice #') {
                $description = "Invoice #$invoice_id.";
                DB::table('account_invoice')
                    ->where('id', $invoice_id)
                    ->update(
                        [
                            'description' => $description,
                            'status' => $status,
                        ]
                    );
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
        $tmp_payments_total = 0;
        $wallet_total = 0;
        $receivable_total = 0;
        $payments_title = '';

        // Add all payments to journal entry
        $payments =  DB::table('account_payment AS ap')
            ->select('ap.*', 'ag.ledger_id')
            ->leftJoin('account_gateway AS ag', 'ag.id', '=', 'ap.gateway_id')
            ->where('ap.partner_id', $partner_id)
            ->where('ap.is_posted', false)
            ->where('ap.type', 'in')
            ->get();

        foreach ($payments as $payment_key => $payment) {
            $payments_total = $payments_total + $payment->amount;
            $journal->journalEntry($payment->title, $payment->amount, $payment->partner_id, $payment->ledger_id);

            DB::table('account_payment')->where('id', $payment->id)->update(['is_posted' => true]);
        }

        $wallet_id = $ledger->getLedgerId('wallet');
        $receivable_id = $ledger->getLedgerId('accounts_receivable');

        // Get wallet total
        $wallet_ledger = $ledger->getLedgerTotal($wallet_id);
        $wallet_total = (isset($wallet_ledger['total'])) ? $wallet_ledger['total'] : 0;

        // Get receivable total
        $receivable_ledger = $ledger->getLedgerTotal($receivable_id);
        $receivable_total = (isset($receivable_ledger['total'])) ? $receivable_ledger['total'] : 0;

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

        $tmp_payments_total = $payments_total;

        // process invoices
        $invoices =  DB::table('account_invoice')
            ->where('partner_id', $partner_id)
            ->where('status', '<>', 'draft')
            ->where('is_posted', false)
            ->orderByDesc('id')->get();

        foreach ($invoices as $payment_key => $invoice) {
            $invoice_data = [];
            $single_invoice_total = 0;

            if ($invoice->is_posted == false) {
                $items = DB::table('account_invoice_item')->where('invoice_id', $invoice->id)->get();

                foreach ($items as $key => $item) {
                    $amount = $item_total = ($item->quantity) ? $item->price * $item->quantity : $item->price;
                    $title = ($item->title) ? $item->title : "Item ID:$item->id";
                    $journal->journalEntry($title, $amount, $invoice->partner_id, $item->ledger_id);

                    $item_rates = DB::table('account_invoice_item_rate AS air')
                        ->select('air.*', 'at.ledger_id AS rate_ledger_id', 'at.title AS rate_title', 'at.method AS rate_method')
                        ->leftJoin('account_rate AS at', 'at.id', '=', 'air.rate_id')
                        ->where('invoice_item_id', $item->id)
                        ->orderBy('method')
                        ->orderBy('ordering')
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

                    $single_invoice_total = $single_invoice_total + $item_total;
                }

                $invoice_data['is_posted'] = true;
                $invoice_data['total'] =  $single_invoice_total;
            } else {
                $single_invoice_total = $invoice->total;
            }


            if ($tmp_payments_total > $single_invoice_total) {
                $invoice_data['status'] = 'paid';
            } elseif ($tmp_payments_total > 0) {
                $invoice_data['status'] = 'partial';
            }

            $tmp_payments_total = $tmp_payments_total -  $single_invoice_total;

            if (!empty($invoice_data)) {
                DB::table('account_invoice')->where('id', $invoice->id)->update($invoice_data);
            }
        }

        if ($invoices_total) {
            $wallet_id = $ledger->getLedgerId('wallet');
            $receivable_id = $ledger->getLedgerId('accounts_receivable');

            if ($wallet_total) {
                $balance = $wallet_total - $invoices_total;
                if ($balance >= 0) {
                    $journal->journalEntry('Payment By wallet worth ' . abs($invoices_total), -1 * abs($invoices_total), $partner_id, $wallet_id);
                    $invoices_total = 0;
                } else {
                    $invoices_total = $invoices_total - $wallet_total;
                    $journal->journalEntry('Payment By wallet worth' . abs($wallet_total), -1 * abs($wallet_total), $partner_id, $wallet_id);
                }
            }

            $balance = $payments_total - $invoices_total;

            if ($balance > 0) {
                $journal->journalEntry('Credit to wallet worth ' . abs($balance), abs($balance), $partner_id, $wallet_id);
            } elseif ($balance < 0) {
                $journal->journalEntry('Partner Debt of ' . abs($balance), abs($balance), $partner_id, $receivable_id);
            }
        }
    }

    public function processInvoices()
    {
        $partners = DB::table('partner')->get();

        foreach ($partners as $partner_key => $partner) {
            $this->reconcileInvoices($partner->id);
        }
    }
}
