<?php

namespace Modules\Account\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Account\Classes\Gateway;
use Modules\Account\Classes\Invoice;
use Modules\Account\Classes\Payment;
use Modules\Base\Http\Controllers\BaseController;

class PaymentController extends BaseController
{

    public function payment(Request $request, $invoice_id)
    {

        $invoice = new Invoice();
        $payment = new Payment();
        $gateway = new Gateway();

        $r_data = $request->all();

        $return_url = base64_decode($r_data['return_url']);
        $request->session()->put('return_url', $return_url);

        $invoice = $invoice->getInvoice($invoice_id);

        if ($invoice->status == 'paid') {
            return redirect($return_url);
        }

        $gateways = $gateway->getGateways(invoice:$invoice);
        $request_sent = $request->get('request_sent', 0);
        $phone = '';
        $gateway = $gateways[0];
        $gateway->till_bill_no = '23232323';

        $data = [
            'invoice' => $invoice,
            'gateways' => $gateways,
            'request_sent' => $request->get('request_sent', 0),
            'phone' => '',
            'gateway' => $gateway,
        ];

        

        return view('account::payment', $data);

    }
}
