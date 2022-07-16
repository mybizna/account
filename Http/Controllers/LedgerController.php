<?php

namespace Modules\Account\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Base\Http\Controllers\BaseController;

class LedgerController extends BaseController
{

    public function recordselect(Request $request)
    {
        $chart_of_account_id =  $request->get('chart_of_account_id');

        $query = DB::table('account_ledger');

        if ($chart_of_account_id) {
            $query->where('chart_id', $chart_of_account_id);
        } else {
            $query->where(DB::raw('1=-1'));
        }

        $records = $query->get();
        $list = collect();
        $list->push(['value' => '', 'label' => '--- Please Select ---']);

        foreach ($records as $key => $record) {
            $list->push(['value' => $record->id, 'label' => $record->name]);
        }


        return response()->json($list);
    }
}
