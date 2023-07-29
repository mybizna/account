<?php

namespace Modules\Account\Entities\Data;

use Modules\Base\Classes\Datasetter;

class InvoiceStatus
{
    /**
     * Set ordering of the Class to be migrated.
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
        $datasetter->add_data('account', 'invoice_status', 'slug', [
            "type_name" => "Draft",
            "slug" => "draft",
        ]);
        $datasetter->add_data('account', 'invoice_status', 'slug', [
            "type_name" => "Pending",
            "slug" => "pending",
        ]);
        $datasetter->add_data('account', 'invoice_status', 'slug', [
            "type_name" => "Paid",
            "slug" => "paid",
        ]);
        $datasetter->add_data('account', 'invoice_status', 'slug', [
            "type_name" => "Partial",
            "slug" => "partial",
        ]);
        $datasetter->add_data('account', 'invoice_status', 'slug', [
            "type_name" => "Closed",
            "slug" => "closed",
        ]);
        $datasetter->add_data('account', 'invoice_status', 'slug', [
            "type_name" => "Void",
            "slug" => "void",
        ]);
    }
}
