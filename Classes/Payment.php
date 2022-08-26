<?php

namespace Modules\Account\Classes;

use Illuminate\Support\Facades\DB;
use Modules\Account\Classes\Invoice;

class Payment
{
    public function makePayment($partner_id, $title, $amount_paid = 0.00, $gateway_id = '', $do_reconcile_invoices = false)
    {
        $invoice = new Invoice();
        DB::beginTransaction();

        $receipt_no = $code = rand(1000, 9999) . substr(str_shuffle("0123456789abcdefghijklmnopqrstvwxyz"), 0, 6);

        try {
            if ($amount_paid > 0) {
                DB::table('account_payment')->insert([
                    'partner_id' => $partner_id,
                    'gateway_id' => $gateway_id,
                    'amount' => $amount_paid,
                    'title' => $title,
                    'receipt_no' => $receipt_no,
                    'code' => $code,
                ]);
            }

            if ($do_reconcile_invoices) {
                $invoice->reconcileInvoices($partner_id);
            }

            DB::commit();
        } catch (\Throwable $th) {
            throw $th;
            DB::rollback();
        }
    }
}
