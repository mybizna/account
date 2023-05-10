<?php

use Modules\Core\Classes\Currency;
use Modules\Account\Classes\Ledger;

$ledger = new Ledger();
$currency = new Currency();

$sales_revenue_id = $ledger->getLedgerId('sales_revenue', true);
$currency_id = $currency->getCurrencyId('USD');

return [
    'sales_default_ledger' => [
        "title" => "Sales Default Ledger",
        "type" => "recordpicker",
        "value" => $sales_revenue_id,
        "comp_url" => "account/admin/ledger/list.vue",
        "setting" => [
            'path_param' => ["account", "ledger"],
            'fields' => ['name', 'slug', 'chart_id__account_chart_of_account__name'],
            'template' => '[name] ([slug]) - [chart_id__account_chart_of_account__name]',

        ],
    ],
    'sales_default_currency' => [
        "title" => "Sales Default Currency",
        "type" => "recordpicker",
        "value" => $currency_id,
        "comp_url" => "core/admin/currency/list.vue",
        "setting" => [
            'path_param' => ["core", "currency"],
            'fields' => ['name', 'code'],
            'template' => '[name] ([code])',

        ],
    ],
];
