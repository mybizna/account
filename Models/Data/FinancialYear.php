<?php

namespace Modules\Account\Models\Data;

use Illuminate\Support\Facades\DB;
use Modules\Base\Classes\Datasetter;

class FinancialYear
{
    /**
     * Set ordering of the Class to be migrated.
     *
     * @var int
     */
    public $ordering = 1;

    /**
     * Run the database seeds with system default records.
     *
     * @param Datasetter $datasetter
     *
     * @return void
     */
    public function data(Datasetter $datasetter): void
    {
        $record_count = DB::table('account_financial_year')->count();

        if (!$record_count) {
            DB::table('account_financial_year')->insert(
                [
                    "name" => "Financial Year " . date("Y"),
                    "description" => "Financial Year " . date("Y"),
                    "start_date" => date("Y") . '-01-01',
                    "end_date" => date("Y") . '-12-31',
                ]
            );
        }
    }
}
