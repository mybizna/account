<?php

$this->add_widget("account", "dashboard-left", "Sales", "account/widgets/sales-report.vue",5);
$this->add_widget("account", "dashboard-middle", "Asset", "account/widgets/asset.vue",5);
$this->add_widget("account", "dashboard-middle", "Income", "account/widgets/income.vue",5);

$this->add_widget("account", "dashboard-right", "Expense", "account/widgets/expense.vue",5);
$this->add_widget("account", "dashboard-right", "Liability", "account/widgets/liability.vue",5);