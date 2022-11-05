<?php

namespace Modules\Account\Classes;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\Account\Classes\Invoice;
use Modules\Account\Entities\Gateway;
use Modules\Account\Entities\Payment as DBPayment;

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
            } catch (\Throwable$th) {
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
            } catch (\Throwable$th) {
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
            } catch (\Throwable$th) {
                throw $th;
            }
        }

        return false;
    }

    public function addPayment($partner_id, $title, $amount = 0.00, $gateway_id = '', $do_reconcile_invoices = false, $ledger_id = false, $invoice_id = false)
    {
        DB::beginTransaction();
        try {
            $this->makePayment($partner_id, $title, $amount, $gateway_id, $do_reconcile_invoices, $ledger_id, $invoice_id);
            DB::commit();
        } catch (\Throwable$th) {
            DB::rollback();
            throw $th;
        }
    }
    public function makePayment($partner_id, $title, $amount = 0.00, $gateway_id = '', $do_reconcile_invoices = false, $ledger_id = false, $invoice_id = false)
    {
        $invoice = new Invoice();

        $receipt_no = $code = rand(1000, 9999) . substr(str_shuffle("0123456789abcdefghijklmnopqrstvwxyz"), 0, 6);

        try {
            if ($amount > 0) {
                $data = [
                    'partner_id' => $partner_id,
                    'gateway_id' => $gateway_id,
                    'amount' => $amount,
                    'title' => $title,
                    'receipt_no' => $receipt_no,
                    'code' => $code,
                ];

                if ($ledger_id) {
                    $data['ledger_id'] = $ledger_id;
                } else {
                    $ledger_cls = new Ledger();

                    $ledger = $ledger_cls->getLedgerBySlug('cash');
                    $data['ledger_id'] = $ledger->id;
                }

                DBPayment::create();
            }

            if ($do_reconcile_invoices) {
                $invoice->reconcileInvoices($partner_id, $invoice_id);
            }
        } catch (\Throwable$th) {
            throw $th;
        }
    }
}
