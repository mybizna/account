<?php

namespace Modules\Account\Entities\Data;

use Modules\Base\Classes\Datasetter;
use Illuminate\Support\Facades\DB;

class Gateway
{

    public $ordering = 3;

    public function data(Datasetter $datasetter)
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
            "published" => true
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
            "published" => true
        ]);
    }
}
