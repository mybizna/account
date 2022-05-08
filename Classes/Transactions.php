<?php

namespace Modules\Account\Classes;

use Modules\Account\Classes\CommonFunc;
use Modules\Account\Classes\People;
use Modules\Account\Classes\RecPayments;

use Modules\Account\Classes\Bills;
use Modules\Account\Classes\PayBills;
use Modules\Account\Classes\Purchases;
use Modules\Account\Classes\PayPurchases;
use Modules\Account\Classes\Bank;

use Illuminate\Support\Facades\DB;

class Transactions
{
    /**
     * Get all sales transactions
     *
     * @param array $args
     *
     * @return mixed
     */
    function getSalesTransactions($args = [])
    {


        $defaults = [
            'number'      => 20,
            'offset'      => 0,
            'order'       => 'DESC',
            'count'       => false,
            'customer_id' => false,
            'status'      => '',
            'type'        => '',
        ];

        $args = wp_parse_args($args, $defaults);

        $sales_transaction_count = $sales_transaction = false;

        if (false === $sales_transaction) {
            $limit = '';

            $where = "WHERE (voucher.type = 'invoice' OR voucher.type = 'payment' OR voucher.type = 'return_payment')";

            if (!empty($args['customer_id'])) {
                $where .= " AND (invoice.customer_id = {$args['customer_id']} OR invoice_receipt.customer_id = {$args['customer_id']}) ";
            }

            if (!empty($args['start_date'])) {
                $where .= " AND ( (invoice.trn_date BETWEEN '{$args['start_date']}' AND '{$args['end_date']}') OR (invoice_receipt.trn_date BETWEEN '{$args['start_date']}' AND '{$args['end_date']}') )";
            }


            if (!empty($args['status'])) {
                $where .= " AND (invoice.status={$args['status']} OR invoice_receipt.status={$args['status']}) ";
            }

            if (!empty($args['type'])) {
                if ($args['type'] === 'estimate') {
                    $where .= " AND invoice.estimate = 1 ";
                }
                if ($args['type'] === 'payment') {
                    $where .= " AND voucher.type = '{$args['type']}' ";
                }
                if ($args['type'] === 'invoice') {
                    $where .= " AND voucher.type = '{$args['type']}' AND invoice.estimate = 0 ";
                }
            }

            if (-1 !== $args['number']) {
                $limit = "LIMIT {$args['number']} OFFSET {$args['offset']}";
            }

            $sql = 'SELECT';

            if ($args['count']) {
                $sql .= ' COUNT( DISTINCT voucher.id ) AS total_number';
            } else {
                $sql .= ' voucher.id,
                    voucher.type,
                    voucher.editable,
                    invoice.customer_id AS inv_cus_id,
                    invoice.customer_name AS inv_cus_name,
                    invoice_receipt.customer_name AS pay_cus_name,
                    invoice.trn_date AS invoice_trn_date,
                    invoice_receipt.trn_date AS payment_trn_date,
                    invoice_receipt.ref,
                    invoice.due_date,
                    invoice.estimate,
                    (invoice.amount + invoice.tax + invoice.shipping + invoice.shipping_tax) - invoice.discount AS sales_amount,
                    SUM(invoice_account_detail.debit - invoice_account_detail.credit) AS due,
                    invoice_receipt.amount AS payment_amount,
                    invoice.status as inv_status,
                    invoice_receipt.status as pay_status';
            }

            $sql .= " FROM {$wpdb->prefix}erp_acct_voucher_no AS voucher
            LEFT JOIN {$wpdb->prefix}erp_acct_invoices AS invoice ON invoice.voucher_no = voucher.id
            LEFT JOIN {$wpdb->prefix}erp_acct_invoice_receipts AS invoice_receipt ON invoice_receipt.voucher_no = voucher.id
            LEFT JOIN {$wpdb->prefix}erp_acct_invoice_account_details AS invoice_account_detail ON invoice_account_detail.invoice_no = invoice.voucher_no
            {$where} GROUP BY voucher.id ORDER BY voucher.id {$args['order']} {$limit}";

            //config()->set('database.connections.mysql.strict', false);
            //config()->set('database.connections.mysql.strict', true);

            if ($args['count']) {
                $wpdb->get_results($sql);
                $sales_transaction_count = $wpdb->num_rows;

                wp_cache_set($cache_key_count, $sales_transaction_count, 'erp-accounting');
            } else {
                $sales_transaction = $wpdb->get_results($sql, ARRAY_A);

                wp_cache_set($cache_key, $sales_transaction, 'erp-accounting');
            }
        }

        if ($args['count']) {
            return $sales_transaction_count;
        }

        return $sales_transaction;
    }

    /**
     * Get sales chart status
     *
     * @param array $args
     *
     * @return array|object|null
     */
    function getSalesChartStatus($args = [])
    {


        $where = 'WHERE invoice.estimate<>1';

        if (!empty($args['start_date'])) {
            $where .= " AND invoice.trn_date BETWEEN '{$args['start_date']}' AND '{$args['end_date']}'";
        }

        if (!empty($args['people_id'])) {
            $where .= " AND invoice.customer_id = {$args['people_id']} ";
        }

        $sql = "SELECT COUNT(invoice.status) AS sub_total, status_type.type_name
            FROM {$wpdb->prefix}erp_acct_trn_status_types AS status_type
            LEFT JOIN {$wpdb->prefix}erp_acct_invoices AS invoice ON invoice.status = status_type.id {$where}
            GROUP BY status_type.id HAVING COUNT(invoice.status) > 0 ORDER BY status_type.type_name ASC";

        return $wpdb->get_results($sql, ARRAY_A);
    }

    /**
     * Get sales chart payment
     *
     * @param array $args
     *
     * @return array|object|void|null
     */
    function getSalesChartPayment($args = [])
    {


        $where = ' WHERE invoice.estimate<>1 AND invoice.status<>1 AND invoice.status<>7 AND invoice.status<>8';

        if (!empty($args['start_date'])) {
            $where .= " AND invoice.trn_date BETWEEN '{$args['start_date']}' AND '{$args['end_date']}'";
        }

        if (!empty($args['people_id'])) {
            $where .= " AND invoice.customer_id = {$args['people_id']} ";
        }

        $sql = "SELECT SUM(credit) as received, SUM(balance) AS outstanding
        FROM ( SELECT invoice.voucher_no, SUM(invoice_acc_detail.credit) AS credit, SUM( invoice_acc_detail.debit - invoice_acc_detail.credit) AS balance
        FROM {$wpdb->prefix}erp_acct_invoices AS invoice
        LEFT JOIN {$wpdb->prefix}erp_acct_invoice_account_details AS invoice_acc_detail ON invoice.voucher_no = invoice_acc_detail.invoice_no {$where}
        GROUP BY invoice.voucher_no) AS get_amount";

        return $wpdb->get_row($sql, ARRAY_A);
    }

    /**
     * Get bill chart data
     *
     * @param array $args
     *
     * @return array|object|null
     */
    function getBillChartData($args = [])
    {


        $where = ' WHERE bill.status != 1  AND bill.status!=7 AND bill.status!=8';

        if (!empty($args['start_date'])) {
            $where .= " AND bill.trn_date BETWEEN '{$args['start_date']}' AND '{$args['end_date']}'";
        }

        if (!empty($args['people_id'])) {
            $where .= " AND bill.vendor_id = {$args['people_id']} ";
        }

        $sql = "SELECT SUM(debit) as paid, ABS(SUM(balance)) AS payable
        FROM ( SELECT bill.voucher_no, SUM(bill_acc_detail.debit) AS debit, SUM( bill_acc_detail.debit - bill_acc_detail.credit) AS balance
        FROM {$wpdb->prefix}erp_acct_bills AS bill
        LEFT JOIN {$wpdb->prefix}erp_acct_bill_account_details AS bill_acc_detail ON bill.voucher_no = bill_acc_detail.bill_no {$where}
        GROUP BY bill.voucher_no) AS get_amount";

        return $wpdb->get_row($sql, ARRAY_A);
    }

    /**
     * Get bill chart status
     *
     * @param array $args
     *
     * @return array|object|null
     */
    function getBillChartStatus($args = [])
    {


        $where = '';

        if (!empty($args['start_date'])) {
            $where .= "WHERE bill.trn_date BETWEEN '{$args['start_date']}' AND '{$args['end_date']}'";
        }

        if (!empty($args['people_id'])) {
            $where .= " AND bill.vendor_id = {$args['people_id']} ";
        }

        $sql = "SELECT status_type.type_name, COUNT(bill.status) AS sub_total
            FROM {$wpdb->prefix}erp_acct_trn_status_types AS status_type
            LEFT JOIN {$wpdb->prefix}erp_acct_bills AS bill ON bill.status = status_type.id {$where}
            GROUP BY status_type.id
            HAVING sub_total > 0
            ORDER BY status_type.type_name ASC";

        return $wpdb->get_results($sql, ARRAY_A);
    }

    /**
     * Get expense chart data
     *
     * @param array $args
     *
     * @return array|object|null
     */
    function getPurchaseChartData($args = [])
    {


        $where = ' WHERE purchase.purchase_order<>1 AND purchase.status<>1 AND purchase.status<>7 AND purchase.status<>8';

        if (!empty($args['start_date'])) {
            $where .= " AND purchase.trn_date BETWEEN '{$args['start_date']}' AND '{$args['end_date']}'";
        }

        if (!empty($args['people_id'])) {
            $where .= " AND purchase.vendor_id = {$args['people_id']} ";
        }

        $sql = "SELECT SUM(debit) as paid, ABS(SUM(balance)) AS payable
        FROM ( SELECT purchase.voucher_no, SUM(purchase_acc_detail.debit) AS debit, SUM( purchase_acc_detail.debit - purchase_acc_detail.credit) AS balance
        FROM {$wpdb->prefix}erp_acct_purchase AS purchase
        LEFT JOIN {$wpdb->prefix}erp_acct_purchase_account_details AS purchase_acc_detail ON purchase.voucher_no = purchase_acc_detail.purchase_no {$where}
        GROUP BY purchase.voucher_no) AS get_amount";

        $result = $wpdb->get_row($sql, ARRAY_A);

        return $result;
    }

    /**
     * Get expense chart status
     *
     * @param array $args
     *
     * @return array|object|null
     */
    function getPurchaseChartStatus($args = [])
    {


        $where = 'WHERE purchase.purchase_order<>1';

        if (!empty($args['start_date'])) {
            $where .= " AND purchase.trn_date BETWEEN '{$args['start_date']}' AND '{$args['end_date']}'";
        }

        if (!empty($args['people_id'])) {
            $where .= " AND purchase.vendor_id = {$args['people_id']} ";
        }

        $sql = "SELECT status_type.type_name, COUNT(purchase.status) AS sub_total
            FROM {$wpdb->prefix}erp_acct_trn_status_types AS status_type
            LEFT JOIN {$wpdb->prefix}erp_acct_purchase AS purchase ON purchase.status = status_type.id {$where}
            GROUP BY status_type.id
            HAVING sub_total > 0
            ORDER BY status_type.type_name ASC";

        $result = $wpdb->get_results($sql, ARRAY_A);

        return $result;
    }

    /**
     * Get expense chart data
     *
     * @param array $args
     *
     * @return array|object|null
     */
    function getExpenseChartData($args = [])
    {


        $where = '';

        if (!empty($args['start_date'])) {
            $where .= "WHERE bill.trn_date BETWEEN '{$args['start_date']}' AND '{$args['end_date']}'";
        }

        if (!empty($args['people_id'])) {
            $where .= " AND bill.people_id = {$args['people_id']} ";
        }

        $sql = "SELECT SUM(balance) as paid, 0 AS payable
        FROM ( SELECT bill.voucher_no, bill_acc_detail.amount AS balance
        FROM {$wpdb->prefix}erp_acct_expenses AS bill
        LEFT JOIN {$wpdb->prefix}erp_acct_expense_details AS bill_acc_detail ON bill.voucher_no = bill_acc_detail.trn_no {$where} HAVING balance > 0 ) AS get_amount";

        return $wpdb->get_row($sql, ARRAY_A);
    }

    /**
     * Get expense chart status
     *
     * @param array $args
     *
     * @return array|object|null
     */
    function getExpenseChartStatus($args = [])
    {


        $where = '';

        if (!empty($args['start_date'])) {
            $where .= "WHERE bill.trn_date BETWEEN '{$args['start_date']}' AND '{$args['end_date']}'";
        }

        if (!empty($args['people_id'])) {
            $where .= " AND bill.people_id = {$args['people_id']} ";
        }

        $sql = "SELECT status_type.type_name, COUNT(bill.status) AS sub_total
            FROM {$wpdb->prefix}erp_acct_trn_status_types AS status_type
            LEFT JOIN {$wpdb->prefix}erp_acct_expenses AS bill ON bill.status = status_type.id {$where}
            GROUP BY status_type.id
            HAVING sub_total > 0
            ORDER BY status_type.type_name ASC";

        return $wpdb->get_row($sql, ARRAY_A);
    }

    /**
     * Get Income Expense Chart data for dashbaord
     *
     * @return array|object|null
     */
    function getIncomeExpenseChartData()
    {
        $income_chart_id  = 4; //Default db value
        $expense_chart_id = 5; //Default db value

        //Generate current month data

        $incomes          = $this->getDailyBalanceByChartId($income_chart_id, 'current');
        $incomes_monthly  = $this->formatDailyDataToYearlyData($incomes);
        $expenses         = $this->getDailyBalanceByChartId($expense_chart_id, 'current');
        $expenses_monthly = $this->formatDailyDataToYearlyData($expenses);

        $this_month = [
            'labels'  => array_keys($incomes_monthly),
            'income'  => array_values($incomes_monthly),
            'expense' => array_values($expenses_monthly),
        ];

        //Generate last month data

        $incomes          = $this->getDailyBalanceByChartId($income_chart_id, 'last');
        $incomes_monthly  = $this->formatDailyDataToYearlyData($incomes);
        $expenses         = $this->getDailyBalanceByChartId($expense_chart_id, 'last');
        $expenses_monthly = $this->formatDailyDataToYearlyData($expenses);

        $last_month = [
            'labels'  => array_keys($incomes_monthly),
            'income'  => array_values($incomes_monthly),
            'expense' => array_values($expenses_monthly),
        ];

        $current_year = date('Y');
        $start_date   = $current_year . '-01-01';
        $end_date     = $current_year . '-12-31';

        $incomes     = $this->getMonthlyBalanceByChartId($start_date, $end_date, $income_chart_id);
        $income_data = $this->formatMonthlyDataToYearlyData($incomes);

        $expenses     = $this->getMonthlyBalanceByChartId($start_date, $end_date, $expense_chart_id);
        $expense_data = $this->formatMonthlyDataToYearlyData($expenses);

        $this_year = [
            'labels'  => array_keys($income_data),
            'income'  => array_values($income_data),
            'expense' => array_values($expense_data),
        ];

        //Generate last year data
        $last_year  = $current_year - 1;
        $start_date = $last_year . '-01-01';
        $end_date   = $last_year . '-12-31';

        $incomes     = $this->getMonthlyBalanceByChartId($start_date, $end_date, $income_chart_id);
        $income_data = $this->formatMonthlyDataToYearlyData($incomes);

        $expenses     = $this->getMonthlyBalanceByChartId($start_date, $end_date, $expense_chart_id);
        $expense_data = $this->formatMonthlyDataToYearlyData($expenses);
        $last_yr      = [
            'labels'  => array_keys($income_data),
            'income'  => array_values($income_data),
            'expense' => array_values($expense_data),
        ];

        return [
            'thisMonth' => $this_month,
            'lastMonth' => $last_month,
            'thisYear'  => $this_year,
            'lastYear'  => $last_yr,
        ];
    }

    /**
     * Get Balance amount for given chart of account in time range
     *
     * @param $start_date
     * @param $end_date
     * @param $chart_id
     *
     * @return array|object|null
     */
    function getMonthlyBalanceByChartId($start_date, $end_date, $chart_id)
    {


        $ledger_details = 'erp_acct_ledger_details';
        $ledgers        = 'erp_acct_ledgers';
        $chart_of_accs  = 'erp_acct_chart_of_accounts';

        $query = "Select Month(ld.trn_date) as month, SUM( ld.debit-ld.credit ) as balance
              From $ledger_details as ld
              Inner Join $ledgers as al on al.id = ld.ledger_id
              Inner Join $chart_of_accs as ca on ca.id = al.chart_id
              Where ca.id = %d
              AND ld.trn_date BETWEEN %s AND %s
              Group By Month(ld.trn_date)";

        $results = $wpdb->get_results($wpdb->prepare($query, $chart_id, $start_date, $end_date), ARRAY_A);

        return $results;
    }

    /**
     * Format Monthly result to Yearly data
     *
     * @param $result
     *
     * @return array
     */
    function formatMonthlyDataToYearlyData($result)
    {
        $default_year_data = [
            'Jan' => 0,
            'Feb' => 0,
            'Mar' => 0,
            'Apr' => 0,
            'May' => 0,
            'Jun' => 0,
            'Jul' => 0,
            'Aug' => 0,
            'Sep' => 0,
            'Oct' => 0,
            'Nov' => 0,
            'Dec' => 0,
        ];

        $result = array_map(
            function ($item) {
                $item['month']   = date('M', mktime(0, 0, 0, $item['month']));
                $item['balance'] = abs($item['balance']);

                return $item;
            },
            $result
        );

        $labels  = wp_list_pluck($result, 'month');
        $balance = wp_list_pluck($result, 'balance');

        $this_yr_data = array_combine($labels, $balance);

        $this_yr_data = wp_parse_args($this_yr_data, $default_year_data);

        return $this_yr_data;
    }

    /**
     * Get Balance amount for given chart of account in time range
     *
     * @param $chart_id
     * @param string $month
     *
     * @return array|object|null
     */
    function getDailyBalanceByChartId($chart_id, $month = 'current')
    {

        $start_date = null;
        $end_date   = null;

        switch ($month) {
            case 'current':
                $start_date = date('Y-m-d', strtotime('first day of this month'));
                $end_date   = date('Y-m-d', strtotime('last day of this month'));
                break;

            case 'last':
                $start_date = date('Y-m-d', strtotime('first day of previous month'));
                $end_date   = date('Y-m-d', strtotime('last day of previous month'));
                break;
            default:
                break;
        }

        $ledger_details = 'erp_acct_ledger_details';
        $ledgers        = 'erp_acct_ledgers';
        $chart_of_accs  = 'erp_acct_chart_of_accounts';

        $query = "Select ld.trn_date as day, SUM( ld.debit-ld.credit ) as balance
              From $ledger_details as ld
              Inner Join $ledgers as al on al.id = ld.ledger_id
              Inner Join $chart_of_accs as ca on ca.id = al.chart_id
              Where ca.id = %d
              AND ld.trn_date BETWEEN %s AND %s
              Group By ld.trn_date";

        $results = $wpdb->get_results($wpdb->prepare($query, $chart_id, $start_date, $end_date), ARRAY_A);

        return $results;
    }

    /**
     * Format Daily result to Yearly data
     *
     * @param $result
     *
     * @return array
     */
    function formatDailyDataToYearlyData($result)
    {
        $result = array_map(
            function ($item) {
                $item['day']     = date('d-m', strtotime($item['day']));
                $item['balance'] = abs($item['balance']);

                return $item;
            },
            $result
        );

        $labels  = wp_list_pluck($result, 'day');
        $balance = wp_list_pluck($result, 'balance');

        $monthly_data = array_combine($labels, $balance);

        return $monthly_data;
    }

    /**
     * Get all Expenses
     *
     * @param array $args
     *
     * @return mixed
     */
    function getExpenseTransactions($args = [])
    {


        $defaults = [
            'number'    => 20,
            'offset'    => 0,
            'order'     => 'DESC',
            'count'     => false,
            'vendor_id' => false,
            'status'    => '',
            'type'      => '',
        ];

        $args = wp_parse_args($args, $defaults);

        $expense_transaction_count =  $expense_transaction =  false;

        if (false === $expense_transaction) {
            $limit = '';

            $where = "WHERE (voucher.type = 'pay_bill' OR voucher.type = 'bill' OR voucher.type = 'expense' OR voucher.type = 'check' ) ";

            if (!empty($args['start_date'])) {
                $where .= " AND ( (bill.trn_date BETWEEN '{$args['start_date']}' AND '{$args['end_date']}') OR (pay_bill.trn_date BETWEEN '{$args['start_date']}' AND '{$args['end_date']}') OR (expense.trn_date BETWEEN '{$args['start_date']}' AND '{$args['end_date']}') )";
            }

            if (!empty($args['vendor_id'])) {
                $where .= " AND (bill.vendor_id = {$args['vendor_id']} OR pay_bill.vendor_id = {$args['vendor_id']}) ";
            }

            if (!empty($args['start_date'])) {
                $where .= " AND ( (bill.trn_date BETWEEN '{$args['start_date']}' AND '{$args['end_date']}') OR (pay_bill.trn_date BETWEEN '{$args['start_date']}' AND '{$args['end_date']}') )";
            }

            if (0 === $args['status']) {
                $where .= '';
            } else {
                if (!empty($args['status'])) {
                    $where .= " AND (bill.status={$args['status']} OR pay_bill.status={$args['status']} OR expense.status={$args['status']} )";
                }
            }

            if (!empty($args['type'])) {
                $where .= " AND voucher.type = '{$args['type']}' ";
            }

            if (-1 !== $args['number']) {
                $limit = "LIMIT {$args['number']} OFFSET {$args['offset']}";
            }

            $sql = 'SELECT';

            $wpdb->query("SET SESSION sql_mode='';");

            if ($args['count']) {
                $sql .= ' COUNT( DISTINCT voucher.id ) AS total_number';
            } else {
                $sql .= ' voucher.id,
                voucher.type,
                bill.vendor_id AS vendor_id,
                bill.vendor_name AS vendor_name,
                pay_bill.vendor_name AS pay_bill_vendor_name,
                expense.people_name AS expense_people_name,
                bill.trn_date AS bill_trn_date,
                pay_bill.trn_date AS pay_bill_trn_date,
                expense.trn_date AS expense_trn_date,
                bill.due_date,
                bill.amount,
                bill.ref,
                expense.ref AS exp_ref,
                pay_bill.ref AS pay_ref,
                pay_bill.amount as pay_bill_amount,
                expense.amount as expense_amount,
                SUM(bill_acct_details.debit - bill_acct_details.credit) AS due,
                bill.status as bill_status,
                pay_bill.status as pay_bill_status,
                expense.status as expense_status';
            }

            $sql .= " FROM {$wpdb->prefix}erp_acct_voucher_no AS voucher
            LEFT JOIN {$wpdb->prefix}erp_acct_bills AS bill ON bill.voucher_no = voucher.id
            LEFT JOIN {$wpdb->prefix}erp_acct_pay_bill AS pay_bill ON pay_bill.voucher_no = voucher.id
            LEFT JOIN {$wpdb->prefix}erp_acct_bill_account_details AS bill_acct_details ON bill_acct_details.bill_no = bill.voucher_no
            LEFT JOIN {$wpdb->prefix}erp_acct_expenses AS expense ON expense.voucher_no = voucher.id
            LEFT JOIN {$wpdb->prefix}erp_acct_expense_checks AS cheque ON cheque.trn_no = voucher.id
            {$where}
            GROUP BY voucher.id
            ORDER BY voucher.id {$args['order']} {$limit}";

            //config()->set('database.connections.mysql.strict', false);
            //config()->set('database.connections.mysql.strict', true);

            if ($args['count']) {
                $wpdb->get_results($sql);

                $expense_transaction_count =  $wpdb->num_rows;

                wp_cache_set($cache_key_count, $expense_transaction_count, 'erp-accounting');
            } else {
                $expense_transaction = $wpdb->get_results($sql, ARRAY_A);

                wp_cache_set($cache_key, $expense_transaction, 'erp-accounting');
            }
        }

        if ($args['count']) {
            return $expense_transaction_count;
        }

        return $expense_transaction;
    }

    /**
     * Get all Purchases
     *
     * @param array $args
     *
     * @return mixed
     */
    function getPurchaseTransactions($args = [])
    {


        $defaults = [
            'number'    => 20,
            'offset'    => 0,
            'order'     => 'DESC',
            'count'     => false,
            'vendor_id' => false,
            's'         => '',
            'status'    => '',
            'type'      => '',
        ];

        $args = wp_parse_args($args, $defaults);

        $purchase_transaction_count = $purchase_transaction =  false;

        if (false === $purchase_transaction) {
            $limit = '';

            $where = "WHERE (voucher.type = 'pay_purchase' OR voucher.type = 'receive_pay_purchase' OR voucher.type = 'purchase')";

            if (!empty($args['vendor_id'])) {
                $where .= " AND (purchase.vendor_id = {$args['vendor_id']} OR pay_purchase.vendor_id = {$args['vendor_id']}) ";
            }

            if (!empty($args['start_date'])) {
                $where .= " AND ( (purchase.trn_date BETWEEN '{$args['start_date']}' AND '{$args['end_date']}') OR (pay_purchase.trn_date BETWEEN '{$args['start_date']}' AND '{$args['end_date']}') )";
            }

            if (!empty($args['type'])) {
                $where .= " AND voucher.type = '{$args['type']}'";
            }

            if (empty($args['status'])) {
                $where .= '';
            } else {
                if (!empty($args['status'])) {
                    $where .= " AND (purchase.status={$args['status']} OR pay_purchase.status={$args['status']}) ";
                }
            }

            if (!empty($args['type'])) {
                $where .= " AND voucher.type = '{$args['start_date']}'";
            }

            if (-1 !== $args['number']) {
                $limit = "LIMIT {$args['number']} OFFSET {$args['offset']}";
            }

            $sql = 'SELECT';

            if ($args['count']) {
                $sql .= ' COUNT( DISTINCT voucher.id ) AS total_number';
            } else {
                $sql .= ' voucher.id,
                voucher.type,
                purchase.vendor_id as vendor_id,
                purchase.vendor_name AS vendor_name,
                pay_purchase.vendor_name AS pay_bill_vendor_name,
                purchase.trn_date AS bill_trn_date,
                pay_purchase.trn_date AS pay_bill_trn_date,
                purchase.due_date,
                purchase.amount,
                purchase.ref,
                pay_purchase.ref as pay_ref,
                purchase.purchase_order,
                pay_purchase.amount as pay_bill_amount,
                SUM(purchase_acct_details.debit - purchase_acct_details.credit) AS due,
                purchase.status AS purchase_status,
                pay_purchase.status AS pay_purchase_status';
            }

            $sql .= " FROM {$wpdb->prefix}erp_acct_voucher_no AS voucher
            LEFT JOIN {$wpdb->prefix}erp_acct_purchase AS purchase ON purchase.voucher_no = voucher.id
            LEFT JOIN {$wpdb->prefix}erp_acct_pay_purchase AS pay_purchase ON pay_purchase.voucher_no = voucher.id
            LEFT JOIN {$wpdb->prefix}erp_acct_purchase_account_details AS purchase_acct_details ON purchase_acct_details.purchase_no = purchase.voucher_no
            {$where} GROUP BY voucher.id ORDER BY voucher.id {$args['order']} {$limit}";

            //config()->set('database.connections.mysql.strict', false);
            //config()->set('database.connections.mysql.strict', true);

            if ($args['count']) {
                $wpdb->get_results($sql);

                $purchase_transaction_count =  $wpdb->num_rows;

                wp_cache_set($cache_key_count, $purchase_transaction_count, 'erp-accounting');
            }

            $purchase_transaction = $wpdb->get_results($sql, ARRAY_A);

            wp_cache_set($cache_key, $purchase_transaction, 'erp-accounting');
        }

        if ($args['count']) {
            return $purchase_transaction_count;
        }

        return $purchase_transaction;
    }

    /**
     * Generate transaction pdf by voucher_no
     *
     * @return void
     */
    function generateTransactionPdf($voucher_no)
    {
        $transaction = $this->getTransaction($voucher_no);
        $filename    = $this->getPdfFilename($voucher_no);

        $this->generatePdf([], $transaction, $filename, 'F');
    }

    /**
     * Generate all transaction pdfs
     *
     * @return void
     */
    function generateTransactionPdfs()
    {


        $voucher_nos = $wpdb->get_results("SELECT id, type FROM {$wpdb->prefix}erp_acct_voucher_no", ARRAY_A);

        for ($i = 0; $i < count($voucher_nos); $i++) {
            if ('journal' === $voucher_nos[$i]['type']) {
                continue;
            }

            $transaction = $this->getTransaction($voucher_nos[$i]['id']);
            $filename    = $this->getPdfFilename($voucher_nos[$i]['id']);
            $this->generatePdf([], $transaction, $filename, 'F');
        }
    }

    /**
     * Generate pdf
     *
     * @param $request
     * @param $transaction
     * @param string $file_name
     * @param string $output_method
     *
     * @return bool
     */
    function generatePdf($request, $transaction, $file_name = '', $output_method = 'D')
    {
        $people = new People();
        if (!is_plugin_active('erp-pdf-invoice/wp-erp-pdf.php')) {
            return;
        }

        if (is_array($transaction)) {
            $transaction = (object) $transaction;
        }

        $company     = new \WeDevs\ERP\Company();
        $theme_color = config('erp_ac_pdf_theme_color', false, '#9e9e9e');

        $user_id = null;
        $trn_id  = null;
        $type    = $this->getTransactionType($transaction->voucher_no);

        if (!empty($request)) {
            $receiver   = isset($request['receiver']) ? $request['receiver'] : $transaction->email;
            $subject    = isset($request['subject']) ? $request['subject'] : $transaction->subject;
            $body       = isset($request['message']) ? $request['message'] : $request['body'];
            $attach_pdf = isset($request['attachment']) && 'on' === $request['attachment'] ? true : false;
        }

        if (!empty($transaction->customer_id)) {
            $user_id = $transaction->customer_id;
        }

        if (!empty($transaction->vendor_id)) {
            $user_id = $transaction->vendor_id;
        }

        if (!empty($transaction->people_id)) {
            $user_id = $transaction->people_id;
        }
        $user = new \WeDevs\ERP\People(intval($user_id));

        if (!defined('WPERP_PDF_VERSION')) {
            wp_die(esc_html__('ERP PDF extension is not installed. Please install the extension for PDF support', 'erp'));
        }

        //Create a new instance
        $trn_pdf = new \WeDevs\ERP_PDF\PDF_Invoicer('A4', '$', 'en');

        //Set theme color
        $trn_pdf->set_theme_color($theme_color);

        //Set your logo
        $logo_id = (int) $company->logo;

        if ($logo_id) {
            $image = wp_get_attachment_image_src($logo_id, 'medium');
            $url   = $image[0];
            $trn_pdf->set_logo($url);
        }

        if (!empty($transaction->voucher_no)) {
            $trn_id = $transaction->voucher_no;
        } elseif (!empty($transaction->trn_no)) {
            $trn_id = $transaction->trn_no;
        }

        $title =  isset($transaction->estimate) && (int)$transaction->estimate ? __('Estimate', 'erp')  : __($type, 'erp');
        //Set type
        $trn_pdf->set_type($title);

        // Set barcode
        if ($trn_id) {
            $trn_pdf->set_barcode($trn_id);
        }

        // Set reference
        if ($trn_id) {
            $trn_pdf->set_reference($trn_id, __('Transaction Number', 'erp'));
        }

        // Set Reference No
        if ($transaction->ref) {
            $trn_pdf->set_reference($transaction->ref, __('Reference No', 'erp'));
        }


        // Set Issue Date
        $date = !empty($transaction->trn_date) ? $transaction->trn_date : $transaction->date;
        $trn_pdf->set_reference($this->formatDate($date), __('Transaction Date', 'erp'));

        // Set Due Date
        if ($transaction->due_date) {
            $trn_pdf->set_reference($transaction->due_date, __('Due Date', 'erp'));
        }

        // Set from Address
        $from_address = explode('<br/>', $company->get_formatted_address());
        array_unshift($from_address, $company->name);

        $trn_pdf->set_from_title(__('FROM', 'erp'));
        $trn_pdf->set_from($from_address);

        // Set to Address
        $to_address = array_values($people->getPeopleAddress($user_id));

        if (empty($to_address)) {
            $to_address = $people->getPeople($user_id)->email;
        }
        array_unshift($to_address, $user->get_full_name());

        $trn_pdf->set_to_title(__('TO', 'erp'));
        $trn_pdf->set_to_address($to_address);

        // Customize columns based on transaction type
        switch ($type) {
            case 'invoice':
                // Set column headers
                $trn_pdf->set_table_headers(
                    array(
                        __('PRODUCT', 'erp'),
                        __('QUANTITY', 'erp'),
                        __('UNIT PRICE', 'erp'),
                        __('AMOUNT', 'erp'),
                    )
                );

                // Add table items
                foreach ($transaction->line_items as $line) {
                    $trn_pdf->add_item(
                        array(
                            $line['name'],
                            $line['qty'],
                            $this->formatAmount($line['unit_price']),
                            $this->formatAmount($line['item_total']),
                        )
                    );
                }

                $trn_pdf->add_badge(sprintf(__('%s', 'erp'), $this->getFormattedStatus($transaction->status)));
                $trn_pdf->add_total(__('SUB TOTAL', 'erp'), $this->formatAmount($transaction->amount));
                $trn_pdf->add_total(__('DISCOUNT', 'erp'), $this->formatAmount($transaction->discount));
                $trn_pdf->add_total(__('TAX', 'erp'), $this->formatAmounttransaction->tax);
                $trn_pdf->add_total(__('TOTAL', 'erp'), $this->formatAmounttransaction->amount + $transaction->tax - $transaction->discount);
                $trn_pdf->add_total(__('DUE', 'erp'), $this->formatAmounttransaction->total_due);

                // Add particulars
                if ($transaction->particulars) {
                    $trn_pdf->add_title(__('Notes', 'erp'));
                    $trn_pdf->add_paragraph($transaction->particulars);
                }
                break;

            case 'payment':
                // Set column headers
                $trn_pdf->set_table_headers(
                    array(
                        __('INVOICE NO', 'erp'),
                        __('TRN DATE', 'erp'),
                        __('AMOUNT', 'erp'),
                    )
                );

                // Add table items
                foreach ($transaction->line_items as $line) {
                    $trn_pdf->add_item(
                        array(
                            $line['invoice_no'],
                            $transaction->trn_date,
                            $this->formatAmount($line['amount']),
                        )
                    );
                }

                // Add particulars
                if ($transaction->particulars) {
                    $trn_pdf->add_title(__('Notes', 'erp'));
                    $trn_pdf->add_paragraph($transaction->particulars);
                }

                $trn_pdf->add_badge(__('PAID', 'erp'));
                $trn_pdf->add_total(__('SUB TOTAL', 'erp'), $this->formatAmount($transaction->amount));
                $trn_pdf->add_total(__('TOTAL', 'erp'), $this->formatAmount($transaction->amount));
                break;

            case 'return_payment':
                // Set column headers
                $trn_pdf->set_table_headers(
                    array(
                        __('INVOICE NO', 'erp'),
                        __('TRN DATE', 'erp'),
                        __('AMOUNT', 'erp'),
                    )
                );

                // Add table items
                foreach ($transaction->line_items as $line) {
                    $trn_pdf->add_item(
                        array(
                            $line['invoice_no'],
                            $transaction->trn_date,
                            $this->formatAmount($line['amount']),
                        )
                    );
                }

                // Add particulars
                if ($transaction->particulars) {
                    $trn_pdf->add_title(__('Notes', 'erp'));
                    $trn_pdf->add_paragraph($transaction->particulars);
                }

                $trn_pdf->add_badge(__('PAID', 'erp'));
                $trn_pdf->add_total(__('SUB TOTAL', 'erp'), $this->formatAmount($transaction->amount));
                $trn_pdf->add_total(__('TOTAL', 'erp'), $this->formatAmount($transaction->amount));
                break;

            case 'purchase_return':
                // Set column headers
                $trn_pdf->set_table_headers(
                    array(
                        __('PRODUCT', 'erp'),
                        __('QUANTITY', 'erp'),
                        __('UNIT PRICE', 'erp'),
                        __('AMOUNT', 'erp'),
                    )
                );

                // Add table items
                foreach ($transaction->line_items as $line) {
                    $trn_pdf->add_item(
                        array(
                            $line['name'],
                            $line['qty'],
                            $this->formatAmountline['price'],
                            $this->formatAmounttval($line['price']) * floatval($line['qty']),
                        )
                    );
                }

                // Add particulars
                if ($transaction->reason) {
                    $trn_pdf->add_title(__('Notes', 'erp'));
                    $trn_pdf->add_paragraph($transaction->reason);
                }

                $trn_pdf->add_badge(__('RETURNED', 'erp'));
                $trn_pdf->add_total(__('SUB TOTAL', 'erp'), $this->formatAmounttransaction->amount);
                $trn_pdf->add_total(__('VAT', 'erp'), $this->formatAmount($transaction->tax));
                $trn_pdf->add_total(__('DISCOUNT', 'erp'), $this->formatAmount($transaction->discount));
                $trn_pdf->add_total(__('TOTAL', 'erp'), $this->formatAmount(floatval($transaction->amount) + floatval($transaction->tax) - floatval($transaction->discount)));
                break;

            case 'sales_return':
                // Set column headers
                $trn_pdf->set_table_headers(
                    array(
                        __('PRODUCT', 'erp'),
                        __('QUANTITY', 'erp'),
                        __('UNIT PRICE', 'erp'),
                        __('AMOUNT', 'erp'),
                    )
                );

                // Add table items
                foreach ($transaction->line_items as $line) {
                    $trn_pdf->add_item(
                        array(
                            $line['name'],
                            $line['qty'],
                            $this->formatAmount($line['unit_price']),
                            $this->formatAmount(floatval($line['unit_price']) * floatval($line['qty'])),
                        )
                    );
                }

                // Add particulars
                if ($transaction->reason) {
                    $trn_pdf->add_title(__('Notes', 'erp'));
                    $trn_pdf->add_paragraph($transaction->reason);
                }

                $trn_pdf->add_badge(__('RETURNED', 'erp'));
                $trn_pdf->add_total(__('SUB TOTAL', 'erp'), $this->formatAmount($transaction->amount));
                $trn_pdf->add_total(__('TAX', 'erp'), $this->formatAmount($transaction->tax));
                $trn_pdf->add_total(__('DISCOUNT', 'erp'), $this->formatAmount($transaction->discount));
                $trn_pdf->add_total(__('TOTAL', 'erp'), $this->formatAmount(floatval($transaction->amount) + floatval($transaction->tax) - floatval($transaction->discount)));
                break;

            case 'bill':
                // Set column headers
                $trn_pdf->set_table_headers(
                    array(
                        __('BILL NO', 'erp'),
                        __('BILL DATE', 'erp'),
                        __('DUE DATE', 'erp'),
                        __('AMOUNT', 'erp'),
                    )
                );

                // Add table items
                foreach ($transaction->bill_details as $line) {
                    $trn_pdf->add_item(
                        array(
                            $line['id'],
                            $transaction->trn_date,
                            $transaction->due_date,
                            $this->formatAmount($line['amount']),
                        )
                    );
                }

                // Add particulars
                if ($transaction->particulars) {
                    $trn_pdf->add_title(__('Notes', 'erp'));
                    $trn_pdf->add_paragraph($transaction->particulars);
                }

                $trn_pdf->add_badge(__('PENDING', 'erp'));
                $trn_pdf->add_total(__('DUE', 'erp'), $this->formatAmount($bills->getBillDue($transaction->voucher_no)));
                $trn_pdf->add_total(__('SUB TOTAL', 'erp'), $this->formatAmount($transaction->amount));
                $trn_pdf->add_total(__('TOTAL', 'erp'), $this->formatAmount($transaction->amount));
                break;

            case 'pay_bill':
                // Set column headers
                $trn_pdf->set_table_headers(
                    array(
                        __('BILL NO', 'erp'),
                        __('DUE DATE', 'erp'),
                        __('AMOUNT', 'erp'),
                    )
                );

                // Add table items
                foreach ($transaction->bill_details as $line) {
                    $trn_pdf->add_item(
                        array(
                            $line['bill_no'],
                            $transaction->trn_date,
                            $this->formatAmount($line['amount']),
                        )
                    );
                }

                // Add particulars
                if ($transaction->particulars) {
                    $trn_pdf->add_title(__('Notes', 'erp'));
                    $trn_pdf->add_paragraph($transaction->particulars);
                }

                $trn_pdf->add_badge(__('PAID', 'erp'));
                $trn_pdf->add_total(__('SUB TOTAL', 'erp'), $this->formatAmount($transaction->amount));
                $trn_pdf->add_total(__('TOTAL', 'erp'), $this->formatAmount($transaction->amount));
                break;

            case 'purchase':
                $subtotal = 0;
                // Set column headers
                $trn_pdf->set_table_headers(
                    array(
                        __('PRODUCT', 'erp'),
                        __('QUANTITY', 'erp'),
                        __('COST PRICE', 'erp'),
                        __('AMOUNT', 'erp'),
                    )
                );

                // Add table items
                foreach ($transaction->line_items as $line) {
                    $trn_pdf->add_item(
                        array(
                            $line['name'],
                            $line['qty'],
                            $this->formatAmount($line['cost_price']),
                            $this->formatAmount($line['amount']),
                        )
                    );

                    $subtotal += floatval($line['line_total']);
                }

                // Add particulars
                if ($transaction->particulars) {
                    $trn_pdf->add_title(__('Notes', 'erp'));
                    $trn_pdf->add_paragraph($transaction->particulars);
                }

                $trn_pdf->add_badge(sprintf(__('%s', 'erp'), $this->getFormattedStatus($transaction->status)));
                $trn_pdf->add_total(__('SUB TOTAL', 'erp'), $this->formatAmount($subtotal));
                $trn_pdf->add_total(__('VAT', 'erp'), $this->formatAmount($transaction->tax));
                $trn_pdf->add_total(__('TOTAL', 'erp'), $this->formatAmount($transaction->amount));
                $trn_pdf->add_total(__('DUE', 'erp'), $this->formatAmount($transaction->total_due));
                break;

            case 'pay_purchase':
                // Set column headers
                $trn_pdf->set_table_headers(
                    array(
                        __('PURCHASE NO', 'erp'),
                        __('DUE DATE', 'erp'),
                        __('AMOUNT', 'erp'),
                    )
                );

                // Add table items
                foreach ($transaction->purchase_details as $line) {
                    $trn_pdf->add_item(
                        array(
                            $line['purchase_no'],
                            $transaction->due_date,
                            $this->formatAmount($line['amount']),
                        )
                    );
                }

                // Add particulars
                if ($transaction->particulars) {
                    $trn_pdf->add_title(__('Notes', 'erp'));
                    $trn_pdf->add_paragraph($transaction->particulars);
                }

                $trn_pdf->add_badge(__('PAID', 'erp'));
                // $trn_pdf->add_total( __( 'DUE', 'erp' ), $this->formatAmount( $transaction->due ) );
                $trn_pdf->add_total(__('SUB TOTAL', 'erp'), $this->formatAmount($transaction->amount));
                $trn_pdf->add_total(__('TOTAL', 'erp'), $this->formatAmount($transaction->amount));
                break;

            case 'receive_pay_purchase':
                // Set column headers
                $trn_pdf->set_table_headers(
                    array(
                        __('PURCHASE NO', 'erp'),
                        __('DUE DATE', 'erp'),
                        __('AMOUNT', 'erp'),
                    )
                );

                // Add table items
                foreach ($transaction->purchase_details as $line) {
                    $trn_pdf->add_item(
                        array(
                            $line['purchase_no'],
                            $transaction->due_date,
                            $this->formatAmount($line['amount']),
                        )
                    );
                }

                // Add particulars
                if ($transaction->particulars) {
                    $trn_pdf->add_title(__('Notes', 'erp'));
                    $trn_pdf->add_paragraph($transaction->particulars);
                }

                $trn_pdf->add_badge(__('PAID', 'erp'));
                // $trn_pdf->add_total( __( 'DUE', 'erp' ), $transaction->due );
                $trn_pdf->add_total(__('SUB TOTAL', 'erp'), $this->formatAmount($transaction->amount));
                $trn_pdf->add_total(__('TOTAL', 'erp'), $this->formatAmount($transaction->amount));
                break;

            case 'expense':
                // Set column headers
                $trn_pdf->set_table_headers(
                    array(
                        __('EXPENSE NO', 'erp'),
                        __('EXPENSE DATE', 'erp'),
                        __('AMOUNT', 'erp'),
                    )
                );

                // Add table items
                foreach ($transaction->bill_details as $line) {
                    $trn_pdf->add_item(
                        array(
                            $line['trn_no'],
                            $transaction->trn_date,
                            $this->formatAmount($line['amount']),
                        )
                    );
                }

                // Add particulars
                if ($transaction->particulars) {
                    $trn_pdf->add_title(__('Notes', 'erp'));
                    $trn_pdf->add_paragraph($transaction->particulars);
                }

                $trn_pdf->add_badge(__('PAID', 'erp'));
                $trn_pdf->add_total(__('SUB TOTAL', 'erp'), $this->formatAmount($transaction->amount));
                $trn_pdf->add_total(__('TOTAL', 'erp'), $this->formatAmount($transaction->amount));
                break;

            case 'check':
                // Set column headers
                $trn_pdf->set_table_headers(
                    array(
                        __('CHECK NO', 'erp'),
                        __('CHECK DATE', 'erp'),
                        __('PAY TO', 'erp'),
                        __('AMOUNT', 'erp'),
                    )
                );

                // Add table items
                foreach ($transaction->bill_details as $line) {
                    $trn_pdf->add_item(
                        array(
                            $line['check_no'],
                            $transaction->trn_date,
                            $transaction->pay_to,
                            $this->formatAmount($line['amount']),
                        )
                    );
                }

                // Add particulars
                if ($transaction->particulars) {
                    $trn_pdf->add_title(__('Notes', 'erp'));
                    $trn_pdf->add_paragraph($transaction->particulars);
                }

                $trn_pdf->add_badge(__('PAID', 'erp'));
                $trn_pdf->add_total(__('SUB TOTAL', 'erp'), $this->formatAmount($transaction->total));
                $trn_pdf->add_total(__('TOTAL', 'erp'), $this->formatAmount($transaction->total));
                break;

            case 'transfer_voucher':
                // Set column headers
                $trn_pdf->set_table_headers(
                    array(
                        __('VOUCHER NO', 'erp'),
                        __('ACCOUNT FROM', 'erp'),
                        __('AMOUNT', 'erp'),
                        __('ACCOUNT TO', 'erp'),
                    )
                );

                $trn_pdf->add_item(
                    array(
                        $transaction->voucher_no,
                        $transaction->ac_from,
                        $this->formatAmount($transaction->amount),
                        $transaction->ac_to
                    )
                );

                // Add particulars
                if ($transaction->particulars) {
                    $trn_pdf->add_title(__('Notes', 'erp'));
                    $trn_pdf->add_paragraph($transaction->particulars);
                }

                $trn_pdf->add_total(__('SUB TOTAL', 'erp'), $this->formatAmount($transaction->balance));
                $trn_pdf->add_total(__('TOTAL', 'erp'), $this->formatAmount($transaction->balance));
                break;

            case 'people_trn':
                // Set column headers
                $trn_pdf->set_table_headers(
                    array(
                        __('VOUCHER NO', 'erp'),
                        __('PARTICULARS', 'erp'),
                        __('AMOUNT', 'erp'),
                    )
                );

                $trn_pdf->add_item(
                    array(
                        $transaction->voucher_no,
                        $transaction->particulars,
                        $this->formatAmount($transaction->balance),
                    )
                );

                $trn_pdf->add_total(__('SUB TOTAL', 'erp'), $this->formatAmount($transaction->balance));
                $trn_pdf->add_total(__('TOTAL', 'erp'), $this->formatAmount($transaction->balance));
                break;
        }

        $trn_pdf->render($file_name, $output_method);
        $file_name = isset($attach_pdf) ? $file_name : '';

        return $file_name;
    }

    /**
     * Generate and send pdf
     *
     * @param $request
     * @param $transaction
     * @param $file_name
     * @param string $output_method
     *
     * @return bool
     */
    function sendEmailWithPdfAttached($request, $transaction, $file_name, $output_method = 'D')
    {
        if (!is_plugin_active('erp-pdf-invoice/wp-erp-pdf.php')) {
            return;
        }

        $trn_email = new \WeDevs\ERP\Accounting\Includes\Classes\Send_Email();
        $user_id   = null;
        $trn_id    = null;
        $result    = [];

        $type     = isset($request['type']) ? $request['type'] : $this->getTransactionType($transaction->voucher_no);
        $receiver = isset($request['receiver']) ? $request['receiver'] : [];
        // translators: %s: type
        $subject = isset($request['subject']) ? $request['subject'] : sprintf(__('Transaction alert for %s', 'erp'), $request['type']);
        $body    = isset($request['message']) ? $request['message'] : __('Thank you for the transaction', 'erp');
        // $attach_pdf = isset( $request['attachment'] ) && 'on' === $request['attachment'] ? true : false;

        $pdf_file = $this->generatePdf($request, $transaction, $file_name, 'F');

        if ($pdf_file) {
            $result = $trn_email->trigger($receiver, $subject, $body, $pdf_file);
        } else {
            wp_die(esc_html__('PDF not generated!', 'erp'));
        }

        return $result;
    }

    /**
     * Generate PDF and Send to Email on Transaction
     *
     * @param $voucher_no
     * @param $transaction
     *
     * @return bool
     */
    function sendEmailOnTransaction($voucher_no, $transaction)
    {
        if (!is_plugin_active('erp-pdf-invoice/wp-erp-pdf.php')) {
            return;
        }

        $user_id   = null;
        $trn_id    = null;
        $request   = [];
        $result    = [];

        $request['type']       = !empty($transaction['type']) ? $transaction['type'] : $this->getTransactionType($voucher_no);
        $request['receiver'][] = !empty($transaction['email']) ? $transaction['email'] : [];
        // translators: %s: type

        $file_name = $this->getPdfFilename($voucher_no);
        $pdf_file  = $this->generatePdf($request, $transaction, $file_name, 'F');

        if ($pdf_file) {
            switch (current_action()) {
                case 'erp_acct_new_transaction_sales':
                    $email_type = 'Transactional_Email';
                    break;

                case 'erp_acct_new_transaction_payment':
                    $email_type = 'Transactional_Email_Payments';
                    break;

                case 'erp_acct_new_transaction_bill':
                    $email_type = 'Transactional_Email_Bill';
                    break;

                case 'erp_acct_new_transaction_pay_bill':
                    $email_type = 'Transactional_Email_Pay_Bill';
                    break;

                case 'erp_acct_new_transaction_purchase':
                    $email_type = 'Transactional_Email_Purchase';
                    break;

                case 'erp_acct_new_transaction_pay_purchase':
                    $email_type = 'Transactional_Email_Pay_Purchase';
                    break;

                case 'erp_acct_new_transaction_purchase_return':
                    $email_type = 'Transactional_Email_Purchase_Return';
                    break;

                case 'erp_acct_new_transaction_sales_return':
                    $email_type = 'Transactional_Email_Sales_Return';
                    break;

                case 'erp_acct_new_transaction_expense':
                    $email_type = 'Transactional_Email_Expense';
                    break;

                case 'erp_acct_new_transaction_estimate':
                    $email_type = 'Transactional_Email_Estimate';
                    break;

                case 'erp_acct_new_transaction_purchase_order':
                    $email_type = 'Transactional_Email_Purchase_Order';
                    break;
                default:
                    $email_type = 'Transactional_Email';
            }

            acct_send_email($request['receiver'], $pdf_file, $email_type, $voucher_no);
        } else {
            wp_die(esc_html__('PDF not generated!', 'erp'));
        }
    }

    /**
     * Send accounting emails to receivers
     *
     * @param $receiver
     * @param $pdf
     * @param $type
     *
     * @return bool
     */
    function acct_send_email($receiver, $pdf_file, $email_type, $voucher_no)
    {
        $emailer = wperp()->emailer->get_email($email_type);
        $company = new \WeDevs\ERP\Company();

        if (is_a($emailer, '\WeDevs\ERP\Email')) {
            if (is_array($receiver)) {
                foreach ($receiver as $email) {
                    $emailer->trigger($email, $pdf_file, $voucher_no, $company);
                }
            } else {
                $emailer->trigger($receiver, $pdf_file, $voucher_no, $company);
            }
        }


        /**
         * Get voucher type by id
         *
         * @param $voucher_no
         *
         * @return string|null
         */
        function getTransactionType($voucher_no)
        {


            return $wpdb->get_var($wpdb->prepare("SELECT type FROM {$wpdb->prefix}erp_acct_voucher_no WHERE id = %d", $voucher_no));
        }

        /**
         * @param $transaction_id
         *
         * @return mixed
         */
        function getTransaction($transaction_id)
        {
            $invoices = new Invoices();
            $recpayments = new RecPayments();
            $bills = new Bills();
            $paybills = new PayBills();
            $purchases = new Purchases();
            $pay_purchases = new PayPurchases();
            $bank = new Bank();

            $transaction = [];

            $transaction_type = $this->getTransactionType($transaction_id);
            $link_hash        = $this->getInvoiceLinkHash($transaction_id, $transaction_type);
            $readonly_url     = add_query_arg(
                [
                    'query'    => 'readonly_invoice',
                    'trans_id' => $transaction_id,
                    'auth'     => $link_hash,
                ],
                site_url()
            );

            switch ($transaction_type) {
                case 'invoice':
                    $transaction = $invoices->getInvoice($transaction_id);
                    break;

                case 'payment':
                    $transaction = $recpayments->getPayment($transaction_id);
                    break;

                case 'bill':
                    $transaction = $bills->getBill($transaction_id);
                    break;

                case 'pay_bill':
                    $transaction = $paybills->getPayBill($transaction_id);
                    break;

                case 'purchase':
                    $transaction = $purchases->getPurchases($transaction_id);
                    break;

                case 'pay_purchase':
                    $transaction = $pay_purchases->getPayPurchase($transaction_id);
                    break;

                case 'expense':
                case 'check':
                    $transaction = $this->getExpense($transaction_id);
                    break;

                case 'transfer_voucher':
                    $transaction = $bank->getSingleVoucher($transaction_id);
                    break;
                default:
                    break;
            }

            $transaction['type']         = $transaction_type;
            $transaction['readonly_url'] = $readonly_url;

            return $transaction;
        }

        /**
         * Varify transaction hash
         *
         * @param $transaction_id
         * @param string $transaction_type
         * @param string $hash_to_verify
         * @param string $algo
         *
         * @return bool
         */
        function verifyInvoiceLinkHash($transaction_id, $transaction_type, $hash_to_verify = '', $algo = 'sha256')
        {
            if ($transaction_id && $transaction_type && $hash_to_verify) {
                $to_hash       = $transaction_id . $transaction_type;
                $hash_original = hash($algo, $to_hash);

                if ($hash_original === $hash_to_verify) {
                    return true;
                }
            }

            return false;
        }

        /**
         * Get unique transaction hash for sharing
         *
         * @param $transaction_id
         * @param string $transaction_type
         * @param string $algo
         *
         * @return string
         */
        function getInvoiceLinkHash($transaction_id, $transaction_type, $algo = 'sha256')
        {
            $hash_string = '';

            if ($transaction_id && $transaction_type) {
                $to_hash     = $transaction_id . $transaction_type;
                $hash_string = hash($algo, $to_hash);
            }

            return $hash_string;
        }

        /**
         * Get pdf file name
         *
         * @param $voucher_no
         *
         * @return string
         */
        function getPdfFilename($voucher_no)
        {
            $inv_dir = WP_CONTENT_DIR . '/uploads/erp-pdfs/';

            if (!file_exists($inv_dir)) {
                mkdir($inv_dir, 0777, true);
            }

            $pdf_file = $inv_dir . "voucher_{$voucher_no}.pdf";

            return $pdf_file;
        }

        /**
         * Insert data into `erp_acct_people_trn_details` table
         *
         * @param $voucher_no
         * @param $transaction
         */
        function insertDataIntoPeopleTrnDetails($transaction, $voucher_no)
        {


            $data = [];

            if (!empty($transaction['customer_id'])) {
                $people_id = $transaction['customer_id'];
            } else {
                if (!empty($transaction['vendor_id'])) {
                    $people_id = $transaction['vendor_id'];
                } else {
                    $people_id = $transaction['people_id'];
                }
            }

            $date = !empty($transaction['trn_date']) ? $transaction['trn_date'] : $transaction['date'];

            DB::table('erp_acct_people_trn_details')
                ->insert(
                    [
                        'people_id'   => $people_id,
                        'voucher_no'  => $voucher_no,
                        'debit'       => $transaction['dr'],
                        'credit'      => $transaction['cr'],
                        'trn_date'    => $date,
                        'particulars' => $transaction['particulars'],
                        'created_at'  => $transaction['created_at'],
                        'created_by'  => $transaction['created_by'],
                        'updated_at'  => $transaction['updated_at'],
                        'updated_by'  => $transaction['updated_by'],
                    ]
                );
        }

        /**
         * Update data into `erp_acct_people_trn_details` table
         *
         * @param $transaction
         * @param $voucher_no
         */
        function updateDataIntoPeopleTrnDetails($transaction, $voucher_no)
        {


            $wpdb->delete('erp_acct_people_trn_details', ['voucher_no' => $voucher_no]);
        }

        /**
         * Return url from a absolute path
         *
         * @param $voucher_no
         *
         * @return string
         */
        function pdfAbsPathToUrl($voucher_no)
        {
            $upload_url = wp_upload_dir();
            $url        = $upload_url['baseurl'] . '/erp-pdfs/' . "voucher_{$voucher_no}.pdf";

            return esc_url_raw($url);
        }

        /**
         * Get formatted transaction status for pdf voucher
         *
         * @since 1.8.0
         *
         * @param int|string $trn_status
         *
         * @return $string
         */
        function getFormattedStatus($trn_status)
        {
            $common = new CommonFunc();
            $trn_status = $common->getTrnStatusById($trn_status);
            $trn_status = explode('_', $trn_status);
            $status     = '';

            foreach ($trn_status as $i => $ts) {
                $status .= strtoupper($ts);

                if ($i < count($trn_status) - 1) {
                    $status .= ' ';
                }
            }

            return $status;
        }

        /**
         * Formats amount with currency in appropriate form.
         *
         * @since 1.10.4
         *
         * @param int|float|string $amount
         *
         * @return string
         */
        function formatAmount($amount)
        {
            return str_replace('&nbsp;', ' ', $this->getPrice($amount));
        }
    }
}
