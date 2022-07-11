<?php

namespace Modules\Account\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Base\Http\Controllers\BaseController;

class LedgerController extends BaseController
{

    public function recordselect(Request $request)
    {
        $type =  $request->get('type');

        $query = DB::table('account_ledger');

        if ($type == 'right') {
            $query->where('slug', 'asset')
                ->orWhere('slug', 'income');
        } else {
            $query->where('slug', 'liability')
                ->orWhere('slug', 'equity')
                ->orWhere('slug', 'expense');
        }

        //print($query->toSql()); exit;

        $records = $query->get();
        $list = collect();
        $list->push(['value' => '', 'label' => 'Please Select']);

        foreach ($records as $key => $record) {
            $list->push(['value' => $record->id, 'label' => $record->name]);
        }


        return response()->json($list);
    }
}
