<?php

namespace Modules\Account\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Base\Http\Controllers\BaseController;
use Modules\Account\Classes\Invoice;

class InvoiceController extends BaseController
{

    public function fetchData(Request $request)
    {
        $result = [
            'module'  => 'account',
            'model'   => 'invoice',
            'status'  => 0,
            'total'   => 0,
            'error'   => 1,
            'records'    => [],
            'message' => 'No Records'
        ];

        $partner_id = $request->get('partner_id');

        try {
            $rates = DB::table('account_rate')->get();
            $gateways = DB::table('account_gateway')->get();
            $partner = DB::table('partner')->where('id', $partner_id)->first();
            $ledgers = DB::table('account_ledger AS l')->select('l.*')
                ->join('account_chart_of_account AS c', 'c.id', '=', 'l.chart_id')
                ->where('c.slug', 'income')->get();

            $ledger_list = collect();
            $ledger_list->push(['value' => '', 'label' => '--- Please Select ---']);
            foreach ($ledgers as $key => $ledger) {
                $ledger_list->push(['value' => $ledger->id, 'label' => $ledger->name]);
            }

            foreach ($gateways as $key => $gateway) {
                $gateways[$key]->paid_amount = 0.00;
            }

            $result['error'] = 0;
            $result['status'] = 1;
            $result['gateways'] = $gateways;
            $result['rates'] = $rates;
            $result['ledgers'] = $ledger_list;
            $result['partner'] = $partner;
            $result['message'] = 'Records Found Successfully.';
        } catch (\Throwable $th) {
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

        $result = [
            'module'  => 'account',
            'model'   => 'invoice',
            'status'  => 0,
            'total'   => 0,
            'error'   => 1,
            'records'    => [],
            'message' => 'No Records'
        ];

        $data = $request->all();

        $partner_id =  $data['partner_id'];
        $items =  $data['items'] ?? [];
        $status =  $data['status'];
        $title =  $data['title'] ?? 'Invoice #';
        $description =  $data['notation'];
        $gateways =  $data['gateways'];

        try {
            $invoice->generateInvoice($title, $partner_id, $items, $status, $gateways, $description);
        } catch (\Throwable $th) {
            throw $th;

            $result['error'] = 1;
            $result['status'] = 0;
            $result['message'] = 'Error While Adding new Generate Invoices.';
        }

        return response()->json($result);
    }
}
