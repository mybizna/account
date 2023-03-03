<?php

namespace Modules\Account\Classes;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Modules\Account\Classes\Gateway;
use Modules\Account\Classes\Invoice;
use Modules\Account\Entities\Payment as DBPayment;
use Modules\Core\Classes\Notification;
use Modules\Partner\Classes\Partner;

class Payment
{

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

    public function addPayment($partner_id, $title, $amount = 0.00, $gateway_id = '', $do_reconcile_invoices = false, $ledger_id = false, $invoice_id = false, $code = '')
    {
        $payment = '';

        $payment = $this->makePayment($partner_id, $title, $amount, $gateway_id, $do_reconcile_invoices, $ledger_id, $invoice_id, $code);

        return $payment;
    }
    public function makePayment($partner_id, $title, $amount = 0.00, $gateway_id = '', $do_reconcile_invoices = false, $ledger_id = false, $invoice_id = false, $code = '')
    {
        $payment = '';

        $invoice = new Invoice();
        $partner = new Partner();
        $notification = new Notification();
        $gateway_cls = new Gateway();

        $partner = $partner->getPartner($partner_id);
        $receipt_no = time() . strtoupper(substr(str_shuffle("0123456789abcdefghijklmnopqrstvwxyz"), 0, 5));
        $code = ($code != '') ? $code : $receipt_no;

        try {

            if ($amount > 0) {
                $data = [
                    'partner_id' => $partner_id,
                    'amount' => $amount,
                    'title' => $title,
                    'receipt_no' => $receipt_no,
                    'code' => $code,
                    'status' => 'paid',
                ];

                if ($ledger_id) {
                    $data['ledger_id'] = $ledger_id;
                } else {
                    $ledger_cls = new Ledger();
                    
                    $ledger = $ledger_cls->getLedgerBySlug('cash');
                    $data['ledger_id'] = $ledger->id;
                }
                
                if ($gateway_id) {
                    $data['gateway_id'] = $gateway_id;
                } else {
                    $gateway = $gateway_cls->getGatewayBySlug('cash');
                    $data['gateway_id'] = $gateway->id;
                }
                
                $payment = DBPayment::create($data);
                
                $gateway = $gateway_cls->getGatewayById($data['gateway_id']);
                $payment->gateway_title = $gateway->title;
                $payment->partner_name = $partner->first_name.' '.$partner->last_name .' '.$partner->email;
                
                $notification->send('account_payment_paid', $partner, $payment);
            }

            if ($do_reconcile_invoices) {
                $invoice->reconcileInvoices($partner_id, $invoice_id);
            }
        } catch (\Throwable$th) {
            throw $th;
        }

        return $payment;
    }

    public function getUser($user_id = '')
    {
        if ($user_id) {
            $user = User::where(['id' => $user_id])->first();
            return $user;
        }

        $user = Auth::user();

        return $user;
    }

    private function getClassName($module)
    {
        $classname = 'Modules\\' . ucfirst($module) . '\Classes\Gateway';

        if (class_exists($classname)) {
            return new $classname();
        } else {
            throw new \Exception("class $classname not found.", 1);

        };
    }
}
