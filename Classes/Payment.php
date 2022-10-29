<?php

namespace Modules\Account\Classes;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Modules\Account\Classes\Invoice;
use Modules\Account\Entities\Payment as DBPayment;
use Modules\Account\Entities\Gateway;

class Payment
{
    public function getGateway($gateway_id)
    {
        if (Cache::has("account_payment_" . $gateway_id)) {
            $gateway = Cache::get("account_payment_" . $gateway_id);
            return $gateway;
        } else {
            try {
                $gateway = Gateway::where('id', $gateway_id)->first();

                Cache::put("account_payment_" . $gateway_id, $gateway);
                //code...
                return $gateway;
            } catch (\Throwable $th) {
                throw $th;
            }
        }

        return false;
    }

    public function getGatewayBySlug($gateway_slug)
    {
        if (Cache::has("account_payment_" . $gateway_slug)) {
            $gateway = Cache::get("account_payment_" . $gateway_slug);
            return $gateway;
        } else {
            try {
                $gateway = Gateway::where('slug', $gateway_slug)->first();

                Cache::put("account_payment_" . $gateway_slug, $gateway);
                //code...
                return $gateway;
            } catch (\Throwable $th) {
                throw $th;
            }
        }

        return false;
    }

    public function getPayment($payment_id)
    {
        if (Cache::has("account_payment_" . $payment_id)) {
            $payment = Cache::get("account_payment_" . $payment_id);
            return $payment;
        } else {
            try {
                $payment = DBPayment::where('al.id', $payment_id)->first();

                Cache::put("account_payment_" . $payment_id, $payment);
                //code...
                return $payment;
            } catch (\Throwable $th) {
                throw $th;
            }
        }

        return false;
    }

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
