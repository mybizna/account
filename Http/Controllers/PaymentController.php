<?php

namespace Modules\Account\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Account\Classes\Invoice;
use Modules\Account\Classes\Payment;
use Modules\Base\Http\Controllers\BaseController;

class PaymentController extends BaseController
{

    public function payment(Request $request, $invoice_id)
    {

        $data = [];
        $invoice = new Invoice();
        $payment = new Payment();

        $data['invoice'] = $invoice->getInvoice($invoice_id);
        $data['gateways'] = $payment->getGateways(invoice:$data['invoice']);
        $data['user'] = $payment->getUser($data['invoice']->user_id);
        
        
        $data['request_sent'] = $request->get('request_sent', 0);
        $data['phone'] =  '';
        $data['gateway'] =  $data['gateways'][0];
        $data['gateway']->till_bill_no = '23232323';

        return view('account::payment', $data);

    }
}
