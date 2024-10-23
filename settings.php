<?php

use Modules\Account\Classes\Ledger;

if (!Schema::hasTable('account_ledger')) {
    return [];
}

$ledger = new Ledger();

$sales_revenue_id = @$ledger->getLedgerId('sales_revenue', true);

if (!$sales_revenue_id) {
    return [];
}

return [
    'sales_default_ledger' => [
        "title" => "Sales Default Ledger",
        "description" => "Enter the default Ledger for Sales.",
        "info" => "Enter the default Ledger for Sales.",
        "type" => "recordpicker",
        "value" => $sales_revenue_id,
        "comp_url" => "account/admin/ledger/list.vue",
        "setting" => [
            'path_param' => ["account", "ledger"],
            'fields' => ['name', 'slug', 'chart_id__account_chart_of_account__name'],
            'template' => '[name] ([slug]) - [chart_id__account_chart_of_account__name]',

        ],
    ],

];
