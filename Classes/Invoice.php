<?php

namespace Modules\Account\Classes;

use Illuminate\Support\Facades\DB;
use Modules\Account\Classes\Payment;

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

    public function reconcileInvoices($partner_id)
    {

        $payments =  DB::table('account_payment')
            ->where('partner_id', $partner_id)
            ->where('is_reconciled', false)
            ->where('type', 'in');


        foreach ($payments as $payment_key => $payment) {
            $invoices =  DB::table('account_invoice')
                ->where('partner_id', $partner_id)
                ->where('status', 'pending')
                ->orWhere('status', 'partial')
                ->orderByDesc('id');

            foreach ($invoices as $payment_key => $invoice) {

            }
        }
    }
}
