<?php

namespace Modules\Account\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Base\Http\Controllers\BaseController;

class RateController extends BaseController
{

    public function recordselect(Request $request)
    {
        $result = [
            'module'  => 'account',
            'model'   => 'rate',
            'status'  => 0,
            'total'   => 0,
            'error'   => 1,
            'records'    => [],
            'message' => 'No Records'
        ];

        $query = DB::table('account_rate');

        try {
            $records = $query->get();

            $result['error'] = 0;
            $result['status'] = 1;
            $result['records'] = $records;
            $result['message'] = 'Records Found Successfully.';
        } catch (\Throwable $th) {
            //throw $th;
        }

        return response()->json($result);
    }
}
