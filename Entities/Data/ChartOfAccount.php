<?php

namespace Modules\Account\Entities\Data;

use Modules\Base\Classes\Datasetter;

class ChartOfAccount
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
        $datasetter->add_data('account', 'chart_of_account', 'slug', [
            "name" => "Asset",
            "slug" => "asset",
        ]);

        $datasetter->add_data('account', 'chart_of_account', 'slug', [
            "name" => "Liability",
            "slug" => "liability",
        ]);

        $datasetter->add_data('account', 'chart_of_account', 'slug', [
            "name" => "Equity",
            "slug" => "equity",
        ]);

        $datasetter->add_data('account', 'chart_of_account', 'slug', [
            "name" => "Income",
            "slug" => "income",
        ]);

        $datasetter->add_data('account', 'chart_of_account', 'slug', [
            "name" => "Expense",
            "slug" => "expense",
        ]);
    }
}
