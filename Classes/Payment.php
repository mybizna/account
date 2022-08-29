<?php

namespace Modules\Account\Classes;

use Illuminate\Support\Facades\DB;
use Modules\Account\Classes\Invoice;
use Modules\Account\Entities\Payment as DBPayment;

class Payment
{
    public function addPayment($partner_id, $title, $amount_paid = 0.00, $gateway_id = '', $do_reconcile_invoices = false)
    {
        DB::beginTransaction();
        try {
            $this->makePayment($partner_id, $title, $amount_paid, $gateway_id, $do_reconcile_invoices);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
    }
    public function makePayment($partner_id, $title, $amount_paid = 0.00, $gateway_id = '', $do_reconcile_invoices = false)
    {
        $invoice = new Invoice();

        $receipt_no = $code = rand(1000, 9999) . substr(str_shuffle("0123456789abcdefghijklmnopqrstvwxyz"), 0, 6);

        try {
            if ($amount_paid > 0) {
                DBPayment::create([
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
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
