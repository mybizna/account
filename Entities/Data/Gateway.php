<?php

namespace Modules\Account\Entities\Data;

use Illuminate\Support\Facades\DB;
use Modules\Base\Classes\Datasetter;

class Gateway
{
    /**
     * Set ordering of the Class to be migrated.
     * @var int
     */
    public $ordering = 3;

    /**
     * Run the database seeds with system default records.
     * 
     * @param Datasetter $datasetter
     * 
     * @return void
     */
    public function data(Datasetter $datasetter): void
    {
        $ledger_id = DB::table('account_ledger')->where('slug', 'cash')->value('id');
        $datasetter->add_data('account', 'gateway', 'slug', [
            "title" => "Cash",
            "slug" => "cash",
            "ledger_id" => $ledger_id,
            "instruction" => "",
            "ordering" => 0,
            "is_default" => true,
            "is_hidden" => true,
            "is_hide_in_invoice" => false,
            "published" => true,
        ]);

        $ledger_id = DB::table('account_ledger')->where('slug', 'bank')->value('id');
        $datasetter->add_data('account', 'gateway', 'slug', [
            "title" => "Bank",
            "slug" => "bank",
            "ledger_id" => $ledger_id,
            "instruction" => "Enter Reference as cheque no. or bank transaction reference no.",
            "ordering" => 0,
            "is_default" => false,
            "is_hidden" => true,
            "is_hide_in_invoice" => false,
            "published" => true,
        ]);
    }
}
