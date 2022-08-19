<?php

namespace Modules\Account\Entities\Data;

use Modules\Base\Classes\Datasetter;
use Illuminate\Support\Facades\DB;

class Rate
{

    public $ordering = 7;

    public function data(Datasetter $datasetter)
    {
        $ledger_id = DB::table('account_ledger')->where('slug', 'sales_tax')->value('id');
        $datasetter->add_data('account', 'rate', 'slug', [
            "title" => "VAT",
            "slug" => "vat",
            "ledger_id" => $ledger_id,
            "value" => 16,
            "method" => '+',
            "published" => true
        ]);

        $ledger_id = DB::table('account_ledger')->where('slug', 'sales_discount')->value('id');
        $datasetter->add_data('account', 'rate', 'slug', [
            "title" => "Discount 3%",
            "slug" => "discount_3",
            "ledger_id" => $ledger_id,
            "value" => 3,
            "method" => '%+',
            "published" => true
        ]);
        $datasetter->add_data('account', 'rate', 'slug', [
            "title" => "Discount 5%",
            "slug" => "discount_5",
            "ledger_id" => $ledger_id,
            "value" => 5,
            "method" => '%+',
            "published" => true
        ]);
        $datasetter->add_data('account', 'rate', 'slug', [
            "title" => "Discount 10%",
            "slug" => "discount_10",
            "ledger_id" => $ledger_id,
            "value" => 10,
            "method" => '%+',
            "published" => true
        ]);
    }
}
