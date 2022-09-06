<?php

$this->add_module_info("account", [
    'title' => 'Accounting',
    'description' => 'Accounting',
    'icon' => 'fas fa-funnel-dollar',
    'class_str' => 'text-primary border-primary'
]);

//$this->add_setting_category("module", "key", "title", "position", "params");
$this->add_setting_category("account", "ledger", "Ledger", 1, ['icon' => "fas fa-cogs"]);

//$this->add_setting("module", "key", "title","path", "position");
$this->add_setting("account", "ledger", "Sales Default Ledger", 5, [
    "name" => "sales_default_ledger",
    "type" => "recordpicker",
    "value" => "My Title",
    "comp_url" => "account/admin/ledger/list.vue",
    "setting" => [
        'path_param' => ["account", "ledger"],
        'fields' => ['first_name', 'last_name', 'email'],
        'template' => '[first_name] [last_name] - [email]',
    ]
]);
