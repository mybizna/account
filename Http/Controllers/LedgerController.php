<?php

namespace Modules\Account\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Account\Classes\ChartOfAccount;
use Modules\Base\Http\Controllers\BaseController;

class LedgerController extends BaseController
{

    public function chartsummary(Request $request, $slug)
    {
        $chart_of_account = new ChartOfAccount();
        
        $data = $request->all();
        
        $result = $chart_of_account->getChartTotalBySlug($slug, $data);

        print_r($result); exit;

    }
    public function recordselect(Request $request)
    {
        $chart_of_account_id = $request->get('chart_of_account_id');

        $result = [
            'module' => 'account',
            'model' => 'ledger',
            'status' => 0,
            'total' => 0,
            'error' => 1,
            'records' => [],
            'message' => 'No Records',
        ];

        $query = DB::table('account_ledger');

        if ($chart_of_account_id) {
            $query->where('chart_id', $chart_of_account_id);
        } else {
            $query->where(DB::raw('1=-1'));
        }

        try {
            $records = $query->get();
            $list = collect();
            $list->push(['value' => '', 'label' => '--- Please Select ---']);

            foreach ($records as $key => $record) {
                $list->push(['value' => $record->id, 'label' => $record->name]);
            }

            $result['error'] = 0;
            $result['status'] = 1;
            $result['records'] = $list;
            $result['message'] = 'Records Found Successfully.';
        } catch (\Throwable$th) {
            //throw $th;
        }

        return response()->json($result);
    }
}
