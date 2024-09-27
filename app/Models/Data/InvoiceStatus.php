<?php

namespace Modules\Account\Models\Data;

use Modules\Base\Classes\Datasetter;

class InvoiceStatus
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
        $datasetter->add_data('account', 'invoice_status', 'slug', [
            "name" => "Draft",
            "slug" => "draft",
            "color" => "gray",
            "status" => "active",
        ]);
        $datasetter->add_data('account', 'invoice_status', 'slug', [
            "name" => "Pending",
            "slug" => "pending",
            "color" => "orange",
            "status" => "active",
        ]);
        $datasetter->add_data('account', 'invoice_status', 'slug', [
            "name" => "Paid",
            "slug" => "paid",
            "color" => "green",
            "status" => "active",
        ]);
        $datasetter->add_data('account', 'invoice_status', 'slug', [
            "name" => "Partial",
            "slug" => "partial",
            "color" => "blue",
            "status" => "active",
        ]);
        $datasetter->add_data('account', 'invoice_status', 'slug', [
            "name" => "Closed",
            "slug" => "closed",
            "color" => "black",
            "status" => "active",
        ]);
        $datasetter->add_data('account', 'invoice_status', 'slug', [
            "name" => "Void",
            "slug" => "void",
            "color" => "red",
            "status" => "active",
        ]);
    }
}
