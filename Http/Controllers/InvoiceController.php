<?php

namespace Modules\Account\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Account\Classes\Invoice;
use Modules\Account\Classes\Payment;
use Modules\Base\Http\Controllers\BaseController;

class InvoiceController extends BaseController
{

    public function fetchData(Request $request)
    {
        $invoice = new Invoice();

        $result = [
            'module' => 'account',
            'model' => 'invoice',
            'status' => 0,
            'total' => 0,
            'error' => 1,
            'records' => [],
            'message' => 'No Records',
        ];

        $partner_id = $request->get('partner_id');
        $invoice_id = $request->get('id');

        try {
            $rates = DB::table('account_rate')->get();
            $gateways = DB::table('account_gateway')->where('is_hide_in_invoice', false)->get();
            $partner = false;
            if ($invoice_id) {
                $result['invoice'] = $invoice->getInvoice($invoice_id, true);
                $partner = DB::table('partner')->where('id', $result['invoice']->partner_id)->first();
            } else {
                $ledgers = DB::table('account_ledger AS l')->select('l.*')
                    ->join('account_chart_of_account AS c', 'c.id', '=', 'l.chart_id')
                    ->where('c.slug', 'income')->get();

                $ledger_list = collect();
                $ledger_list->push(['value' => '', 'label' => '--- Please Select ---']);
                foreach ($ledgers as $key => $ledger) {
                    $ledger_list->push(['value' => $ledger->id, 'label' => $ledger->name]);
                }

                $result['rates'] = $rates;
                $result['ledgers'] = $ledger_list;
            }

            if ($partner_id) {
                $partner = DB::table('partner')->where('id', $partner_id)->first();
            }

            foreach ($gateways as $key => $gateway) {
                $gateways[$key]->paid_amount = 0.00;
            }

            $result['error'] = 0;
            $result['status'] = 1;
            $result['gateways'] = $gateways;
            $result['partner'] = $partner;
            $result['message'] = 'Records Found Successfully.';
        } catch (\Throwable$th) {
            #throw $th;

            $result['error'] = 1;
            $result['status'] = 0;
            $result['message'] = 'Error While Loading Records.';
        }

        return response()->json($result);
    }

    public function saveData(Request $request)
    {

        $invoice = new Invoice();
        $payment = new Payment();

        $result = [
            'module' => 'account',
            'model' => 'invoice',
            'status' => 0,
            'total' => 0,
            'error' => 1,
            'records' => [],
            'message' => 'No Records',
        ];

        $data = $request->all();

        $partner_id = $data['partner_id'];
        $items = $data['items'] ?? [];
        $status = $data['status'];
        $title = $data['title'] ?? 'Invoice #';
        $description = $data['notation'];
        $gateways = $data['gateways'];

        try {
            foreach ($gateways as $item_key => $gateway) {
                if ($gateway['paid_amount'] && $status == 'draft') {
                    $status = 'pending';
                }

                $title = $gateway['title'] . " Payment. " . $gateway['reference'] . ' ' . $gateway['others'];

                if ($gateway['paid_amount']) {
                    $payment->makePayment($partner_id, $title, $gateway['paid_amount'], $gateway['id']);
                }
            }

            $invoice = $invoice->generateInvoice($title, $partner_id, $items, $status, $description);

        } catch (\Throwable$th) {
            throw $th;

            $result['error'] = 1;
            $result['status'] = 0;
            $result['message'] = 'Error While Adding new Generate Invoices.';
        }

        return response()->json($result);
    }

    public function reconcileInvoices(Request $request)
    {
        $invoice = new Invoice();

        $invoice->processInvoices();

        echo 'Invoice reconcilation Complete.';
    }
}
