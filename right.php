<?php

/** @var \Modules\Base\Classes\Fetch\Rights $this */

$this->add_right("account", "chart_of_account", "administrator", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "chart_of_account", "manager", view: true, );
$this->add_right("account", "chart_of_account", "supervisor", view: true, );
$this->add_right("account", "chart_of_account", "staff", view: true);
$this->add_right("account", "chart_of_account", "registered", );
$this->add_right("account", "chart_of_account", "guest", );

$this->add_right("account", "coupon", "administrator", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "coupon", "manager", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "coupon", "supervisor", view: true, );
$this->add_right("account", "coupon", "staff", view: true, );
$this->add_right("account", "coupon", "registered", view: true);
$this->add_right("account", "coupon", "guest", );

$this->add_right("account", "financial_year", "administrator", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "financial_year", "manager", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "financial_year", "supervisor", view: true);
$this->add_right("account", "financial_year", "staff", view: true);
$this->add_right("account", "financial_year", "registered", view: true, );
$this->add_right("account", "financial_year", "guest", );

$this->add_right("account", "gateway", "administrator", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "gateway", "manager", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "gateway", "supervisor", view: true);
$this->add_right("account", "gateway", "staff", view: true);
$this->add_right("account", "gateway", "registered", view: true);
$this->add_right("account", "gateway", "guest", );

$this->add_right("account", "gateway_allowedin", "administrator", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "gateway_allowedin", "manager", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "gateway_allowedin", "supervisor", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "gateway_allowedin", "staff", view: true);
$this->add_right("account", "gateway_allowedin", "registered", view: true);
$this->add_right("account", "gateway_allowedin", "guest", );

$this->add_right("account", "gateway_disallowedin", "administrator", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "gateway_disallowedin", "manager", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "gateway_disallowedin", "supervisor", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "gateway_disallowedin", "staff", view: true, add: true, edit: true);
$this->add_right("account", "gateway_disallowedin", "registered", view: true);
$this->add_right("account", "gateway_disallowedin", "guest", );

$this->add_right("account", "gateway_rate", "administrator", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "gateway_rate", "manager", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "gateway_rate", "supervisor", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "gateway_rate", "staff", view: true, add: true, edit: true);
$this->add_right("account", "gateway_rate", "registered", view: true);
$this->add_right("account", "gateway_rate", "guest", );

$this->add_right("account", "invoice", "administrator", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "invoice", "manager", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "invoice", "supervisor", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "invoice", "staff", view: true, add: true, edit: true);
$this->add_right("account", "invoice", "registered", view: true, add: true);
$this->add_right("account", "invoice", "guest", );

$this->add_right("account", "invoice_coupon", "administrator", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "invoice_coupon", "manager", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "invoice_coupon", "supervisor", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "invoice_coupon", "staff", view: true, add: true, edit: true);
$this->add_right("account", "invoice_coupon", "registered", view: true, add: true);
$this->add_right("account", "invoice_coupon", "guest", );

$this->add_right("account", "invoice_item", "administrator", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "invoice_item", "manager", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "invoice_item", "supervisor", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "invoice_item", "staff", view: true, add: true, edit: true);
$this->add_right("account", "invoice_item", "registered", view: true, add: true);
$this->add_right("account", "invoice_item", "guest", );

$this->add_right("account", "invoice_item_rate", "administrator", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "invoice_item_rate", "manager", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "invoice_item_rate", "supervisor", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "invoice_item_rate", "staff", view: true, add: true, edit: true);
$this->add_right("account", "invoice_item_rate", "registered", view: true, add: true);
$this->add_right("account", "invoice_item_rate", "guest", );

$this->add_right("account", "invoice_status", "administrator", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "invoice_status", "manager", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "invoice_status", "supervisor", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "invoice_status", "staff", view: true, add: true, edit: true);
$this->add_right("account", "invoice_status", "registered", view: true, add: true);
$this->add_right("account", "invoice_status", "guest", );

$this->add_right("account", "journal", "administrator", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "journal", "manager", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "journal", "supervisor", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "journal", "staff", view: true, add: true, edit: true);
$this->add_right("account", "journal", "registered", view: true);
$this->add_right("account", "journal", "guest", );

$this->add_right("account", "ledger", "administrator", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "ledger", "manager", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "ledger", "supervisor", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "ledger", "staff", view: true, add: true, edit: true);
$this->add_right("account", "ledger", "registered");
$this->add_right("account", "ledger", "guest");

$this->add_right("account", "ledger_category", "administrator", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "ledger_category", "manager", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "ledger_category", "supervisor", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "ledger_category", "staff", view: true);
$this->add_right("account", "ledger_category", "registered", view: true, );
$this->add_right("account", "ledger_category", "guest", );

$this->add_right("account", "opening_balance", "administrator", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "opening_balance", "manager", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "opening_balance", "supervisor", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "opening_balance", "staff", view: true, );
$this->add_right("account", "opening_balance", "registered", );
$this->add_right("account", "opening_balance", "guest", );

$this->add_right("account", "payment", "administrator", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "payment", "manager", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "payment", "supervisor", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "payment", "staff", view: true, add: true);
$this->add_right("account", "payment", "registered", view: true);
$this->add_right("account", "payment", "guest", );

$this->add_right("account", "payment_rate", "administrator", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "payment_rate", "manager", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "payment_rate", "supervisor", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "payment_rate", "staff", view: true, add: true, edit: true);
$this->add_right("account", "payment_rate", "registered", view: true);
$this->add_right("account", "payment_rate", "guest", );

$this->add_right("account", "rate", "administrator", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "rate", "manager", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "rate", "supervisor", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "rate", "staff", view: true, add: true, edit: true);
$this->add_right("account", "rate", "registered", view: true, );
$this->add_right("account", "rate", "guest", );

$this->add_right("account", "rate_allowedin", "administrator", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "rate_allowedin", "manager", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "rate_allowedin", "supervisor", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "rate_allowedin", "staff", view: true, add: true, edit: true);
$this->add_right("account", "rate_allowedin", "registered", view: true);
$this->add_right("account", "rate_allowedin", "guest", );

$this->add_right("account", "rate_disallowedin", "administrator", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "rate_disallowedin", "manager", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "rate_disallowedin", "supervisor", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "rate_disallowedin", "staff", view: true, add: true, edit: true);
$this->add_right("account", "rate_disallowedin", "registered", view: true);
$this->add_right("account", "rate_disallowedin", "guest", );

$this->add_right("account", "rate_file", "administrator", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "rate_file", "manager", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "rate_file", "supervisor", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "rate_file", "staff", view: true, add: true, edit: true);
$this->add_right("account", "rate_file", "registered", view: true);
$this->add_right("account", "rate_file", "guest", );

$this->add_right("account", "transaction", "administrator", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "transaction", "manager", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "transaction", "supervisor", view: true, add: true, edit: true, delete: true);
$this->add_right("account", "transaction", "staff", view: true, add: true, edit: true);
$this->add_right("account", "transaction", "registered", view: true);
$this->add_right("account", "transaction", "guest", );
