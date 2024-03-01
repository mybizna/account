<?php

$this->add_widget("account", "dashboard-main", "Sales", "account/widgets/sales-report.vue",5);
$this->add_widget("account", "dashboard-main-q1", "Asset", "account/widgets/asset.vue",5);
$this->add_widget("account", "dashboard-main-q2", "Expense", "account/widgets/expense.vue",5);

$this->add_widget("account", "dashboard-side", "Mini Summary", "account/widgets/mini-summary.vue",5);
$this->add_widget("account", "dashboard-side", "Income", "account/widgets/income.vue",5);
$this->add_widget("account", "dashboard-side", "Liability", "account/widgets/liability.vue",5);