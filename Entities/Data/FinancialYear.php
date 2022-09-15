<?php

namespace Modules\Account\Entities\Data;

use Modules\Base\Classes\Datasetter;
use Illuminate\Support\Facades\DB;

class FinancialYear
{

    public $ordering = 1;

    public function data(Datasetter $datasetter)
    {
        $record_count = DB::table('account_financial_year')->count();

        if (!$record_count) {
            DB::table('account_financial_year')->insert(
                [
                    "name" => "Financial Year " . date("Y"),
                    "description" => "Financial Year " . date("Y"),
                    "start_date" => date("Y") . '-01-01',
                    "end_date" => date("Y") . '-12-31'
                ]
            );
        }
    }
}
