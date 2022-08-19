<?php

namespace Modules\Account\Classes;

use Illuminate\Support\Facades\DB;
use Modules\Account\Classes\Payment;
use Modules\Account\Classes\Journal;

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

            DB::commit();

            $this->reconcileInvoices($partner_id);
        } catch (\Throwable $th) {
            throw $th;
            DB::rollback();
        }
    }
    public function postInvoice($invoice_id)
    {
        $transaction = new Transactions();

        $invoice = DB::table('account_invoice')->where('id', $invoice_id)->get();
        $ledger = DB::table('account_ledger')->where('slug', 'accounts_receivable')->get();
        $items = DB::table('account_invoice_item')->where('invoice_id', $invoice_id)->get();

        foreach ($items as $key => $item) {
            $amount = ($item->quantity) ? $item->price * $item->quantity : $item->price;
            $description = ($item->description) ? $item->description : "Item ID:$item->id";
            $partner_id = $invoice->partner_id;
            $left_ledger_id = $ledger->id;
            $right_ledger_id = $item->ledger_id;




            $item_rates = DB::table('account_invoice_item_rate')->where('invoice_item_id', $item->invoice_item_id)->get();
            foreach ($item_rates as $key => $item_rate) {
                $invoice = DB::table('account_invoice')->where('id', $item_rate->rate_id)->get();
                $amount = ($item_rate->value) ? $item_rate->value : 0;
                $description = ($item_rate->title) ? $item_rate->title : "Item ID:$item->id Rate ID:$rate->id";
                $partner_id = $invoice->partner_id;
                $left_ledger_id = $ledger->id;
                $right_ledger_id = $item_rate->ledger_id;
            }
            # code...
        }
    }
    public function reconcileInvoices($partner_id)
    {
        $journal = new Journal();
        $payments_total = 0;

        $payments =  DB::table('account_payment AS ap, ag.ledger_id')
            ->leftJoin('account_gateway AS ag', 'ag.id', '=', 'ap.gateway_id')
            ->where('ap.partner_id', $partner_id)
            ->where('ap.is_posted', false)
            ->where('ap.type', 'in')->get();

        foreach ($payments as $payment_key => $payment) {
            $payments_total = $payments_total + $payment->amount;
            $journal->journalEntry($payment->title, $payment->amount, $payment->partner_id, $payment->ledger_id);
        }

        $invoices =  DB::table('account_invoice')
            ->where('partner_id', $partner_id)
            ->where('status', 'pending')
            ->orWhere('status', 'partial')
            ->orderByDesc('id');


        foreach ($invoices as $payment_key => $invoice) {
            if ($invoice->is_posted = false) {
                $items = DB::table('account_invoice_item')->where('invoice_id', $invoice->id)->get();
                foreach ($items as $key => $item) {
                    $amount = ($item->quantity) ? $item->price * $item->quantity : $item->price;
                    $title = ($item->description) ? $item->description : "Item ID:$item->id";
                    $journal->journalEntry($title, $amount, $invoice->partner_id, $item->ledger_id);


                    $item_rates = DB::table('account_invoice_item_rate AS air, at.ledger_id AS rate_ledger_id, at.title AS rate_title')
                        ->leftJoin('account_rate AS at', 'at.id', '=', 'air.rate_id')
                        ->where('invoice_item_id', $item->invoice_item_id)->get();
                    foreach ($item_rates as $key => $item_rate) {
                        $amount = ($item_rate->value) ? $item_rate->value : 0;
                        $title =  ($item_rate->title) ? $item_rate->title : "Item ID:$item->id Rate:$item_rate->rate_title";
                        $journal->journalEntry($title, $amount, $invoice->partner_id, $item_rate->rate_ledger_id);
                    }
                }
            }

            DB::table('account_invoice')->where('invoice_id', $invoice->id)->update(['is_posted' => true]);
        }


        $accounts_receivable = DB::table('account_ledger')->where('slug', 'accounts_receivable')->get();
    }
}
