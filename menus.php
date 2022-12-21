<?php

$this->add_module_info("account", [
    'title' => 'Account',
    'description' => 'Account',
    'icon' => 'fas fa-funnel-dollar',
    'path' => '/account/admin/invoice',
    'class_str'=> 'text-primary border-primary'
]);
 

//$this->add_menu("module", "key", "title","path", "icon", "position");
$this->add_menu("account", "invoice", "Invoice", "/account/admin/invoice", "fas fa-cogs", 1);
$this->add_menu("account", "payment", "Payment", "/account/admin/payment", "fas fa-cogs", 1);
$this->add_menu("account", "transaction", "Transaction", "/account/admin/transaction", "fas fa-cogs", 1);
$this->add_menu("account", "journal", "Journal", "/account/admin/journal", "fas fa-cogs", 1);
$this->add_menu("account", "coupon", "Coupon", "/account/admin/coupon", "fas fa-cogs", 1);
$this->add_menu("account", "setting", "Settings", "/settings", "fas fa-cogs", 5);
$this->add_menu("account", "reports", "Reports", "/reports", "fas fa-cogs", 5);

//$this->add_submenu("module", "key", "title","path", "position");

$this->add_submenu("account", "setting", "Gateway", "/account/admin/gateway", 5);
$this->add_submenu("account", "setting", "", "", 5);
$this->add_submenu("account", "setting", "Financial Year", "/account/admin/financial_year", 5);
$this->add_submenu("account", "setting", "Chart of Accounts", "/account/admin/chart_of_account", 5);
$this->add_submenu("account", "setting", "", "", 5);
$this->add_submenu("account", "setting", "Rate", "/account/admin/rate", 5);
$this->add_submenu("account", "setting", "Ledger", "/account/admin/ledger", 5);
$this->add_submenu("account", "setting", "Ledger Category", "/account/admin/ledger_category", 5);

$this->add_submenu("account", "reports", "Balance Sheet", "/account/reports/balance-sheet", 5);
$this->add_submenu("account", "reports", "Income Statement", "/account/reports/balance-sheet", 5);
$this->add_submenu("account", "reports", "Ledger Report", "/account/reports/ledger-report", 5);
$this->add_submenu("account", "reports", "Trail Balance", "/account/reports/trail-balance", 5);
//$this->add_submenu("account", "settings", "Tax Rates", "/settings/taxes/tax-rates", 5);
//$this->add_submenu("account", "settings", "Tax Payments", "/settings/taxes/tax-records", 5);
//$this->add_submenu("account", "settings", "Tax Rates", "/settings/taxes/tax-rates", 5);
