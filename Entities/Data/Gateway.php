<?php

namespace Modules\Account\Entities\Data;

use Modules\Base\Classes\Datasetter;

class Gateway
{

    public $ordering = 1;

    public function data(Datasetter $datasetter)
    {
        $datasetter->add_data('account', 'gateway', 'slug', [
            "title" => "Cash",
            "slug" => "cash",
            "instruction" => "",
            "ordering" => 0,
            "is_default" => true,
            "published" => true
        ]);

        $datasetter->add_data('account', 'gateway', 'slug', [
            "title" => "Bank",
            "slug" => "bank",
            "instruction" => "Enter Reference as cheque no. or bank transaction reference no.",
            "ordering" => 0,
            "is_default" => false,
            "published" => true
        ]);
    }
}
