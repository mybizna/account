<?php

$this->add_widget("account", "dashboard-left", "Sales", "account/widgets/sales-report.vue",5);
$this->add_widget("account", "dashboard-middle", "Profit", "account/widgets/profit.vue",5);
$this->add_widget("account", "dashboard-middle", "Sale", "account/widgets/sale.vue",5);

$this->add_widget("account", "dashboard-right", "Expense", "account/widgets/expense.vue",5);
$this->add_widget("account", "dashboard-right", "Expense", "account/widgets/receivable.vue",5);