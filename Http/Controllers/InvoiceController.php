<?php

namespace Modules\Account\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Base\Http\Controllers\BaseController;

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

            $gateway_list = collect();
            $gateway_list->push(['value' => '', 'label' => '--- Please Select ---']);
            foreach ($gateways as $key => $gateway) {
                $gateway_list->push(['value' => $gateway->id, 'label' => $gateway->title]);
            }

            $result['error'] = 0;
            $result['status'] = 1;
            $result['gateways'] = $gateway_list;
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

        $invoice_id = DB::table('account_invoice')->insertGetId(
            [
                'partner_id' => $data['partner_id'],
                'description' => $data['notation'] ?? 'Invoice #',
                'status' => 'draft',
            ]
        );

        foreach ($data['items'] as $item_key => $item) {
            $invoice_item_id = DB::table('account_invoice_item')->insertGetId(
                [
                    'invoice_id' => $invoice_id,
                    'lerger_id' => $item['lerger_id'],
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
    }
}
