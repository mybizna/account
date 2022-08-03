<?php

namespace Modules\Account\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Base\Http\Controllers\BaseController;

class InvoiceController extends BaseController
{

    public function recordselect(Request $request)
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
            $partner = DB::table('partner')->where('id', $partner_id)->first();
            $ledgers = DB::table('account_ledger AS l')
                ->join('account_chart_of_account as c', 'c.id', '=', 'l.chart_id')
                ->where('c.slug', 'income')->get();

            $result['error'] = 0;
            $result['status'] = 1;
            $result['rates'] = $rates;
            $result['ledgers'] = $ledgers;
            $result['partner'] = $partner;
            $result['message'] = 'Records Found Successfully.';
        } catch (\Throwable $th) {
            //throw $th;
        }

        return response()->json($result);
    }
}
