<?php


namespace Modules\Account\Classes\Reports;

class Bank
{
    /**
     * Get closest next financial year
     *
     * @param string $date
     *
     * @return void
     */
    function getClosestNextFnYear($date)
    {
        global $wpdb;

        return $wpdb->get_row($wpdb->prepare("SELECT id, start_date, end_date FROM {$wpdb->prefix}erp_acct_financial_years WHERE start_date > '%s' ORDER BY start_date ASC LIMIT 1", $date));
    }

    /**
     * Close balance sheet now
     *
     * @param array $args
     *
     * @return void
     */
    function closeBalanceSheetNow($args)
    {
        $balance_sheet  = $reports->getBalanceSheet($args);
        $assets         = $balance_sheet['rows1'];
        $liability      = $balance_sheet['rows2'];
        $equity         = $balance_sheet['rows3'];
        $next_f_year_id = $args['f_year_id'];

        global $wpdb;

        // remove next financial year data if exists
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->prefix}erp_acct_opening_balances
    WHERE financial_year_id = %d",
                $next_f_year_id
            )
        );

        $ledger_map = \WeDevs\ERP\Accounting\Includes\Classes\Ledger_Map::get_instance();

        // ledgers
        $sql     = "SELECT id, chart_id, name, slug FROM {$wpdb->prefix}erp_acct_ledgers";
        $ledgers = $wpdb->get_results($sql, ARRAY_A);

        foreach ($ledgers as $ledger) {
            // assets
            foreach ($assets as $asset) {
                if (!empty($asset['id'])) {
                    if ($asset['id'] === $ledger['id']) {
                        if (0 <= $asset['balance']) {
                            $debit  = abs($asset['balance']);
                            $credit = 0.00;
                        } else {
                            $debit  = 0.00;
                            $credit = abs($asset['balance']);
                        }

                        $this->insertIntoOpeningBalance(
                            $next_f_year_id,
                            $ledger['chart_id'],
                            $ledger['id'],
                            'ledger',
                            $debit,
                            $credit
                        );
                    }
                }
            } // assets loop

            // liability
            foreach ($liability as $liab) {
                if (!empty($liab['id'])) {
                    if ($liab['id'] === $ledger['id']) {
                        if (0 <= $liab['balance']) {
                            $debit  = abs($liab['balance']);
                            $credit = 0.00;
                        } else {
                            $debit  = 0.00;
                            $credit = abs($liab['balance']);
                        }

                       $this->insertIntoOpeningBalance(
                            $next_f_year_id,
                            $ledger['chart_id'],
                            $ledger['id'],
                            'ledger',
                            $debit,
                            $credit
                        );
                    }
                }
            } // liability loop

            // equity
            $owners_equity_id = $ledger_map->get_ledger_id_by_slug('owner_s_equity');

            foreach ($equity as $eqt) {
                if (!empty($eqt['id']) && $owners_equity_id !== $eqt['id']) {
                    if ($eqt['id'] === $ledger['id']) {
                        if (0 <= $eqt['balance']) {
                            $debit  = abs($eqt['balance']);
                            $credit = 0.00;
                        } else {
                            $debit  = 0.00;
                            $credit = abs($eqt['balance']);
                        }

                        $this->insertIntoOpeningBalance(
                            $next_f_year_id,
                            $ledger['chart_id'],
                            $ledger['id'],
                            'ledger',
                            $debit,
                            $credit
                        );
                    }
                }
            } // liability loop
        } // ledger loop

        $chart_id_bank  = 7;
        $final_accounts = new \WeDevs\ERP\Accounting\Includes\Classes\Final_Accounts($args);

        foreach ($final_accounts->cash_at_bank_breakdowns as $cash_at_bank) {
            $this->insertIntoOpeningBalance(
                $next_f_year_id,
                $chart_id_bank,
                $cash_at_bank['ledger_id'],
                'ledger',
                $cash_at_bank['balance'],
                0.00
            );
        }

        foreach ($final_accounts->loan_at_bank_breakdowns as $loan_at_bank) {
            $this->insertIntoOpeningBalance(
                $next_f_year_id,
                $chart_id_bank,
                $loan_at_bank['ledger_id'],
                'ledger',
                0.00,
                abs($loan_at_bank['balance'])
            );
        }

        // get accounts receivable
        $accounts_receivable = $this->getAccountsReceivableBalanceWithPeople($args['start_date']); //getAccountsPayableBalanceWithPeople( $args );

        foreach ($accounts_receivable as $acc_receivable) {
            $this->insertIntoOpeningBalance(
                $next_f_year_id,
                null,
                $acc_receivable['id'],
                'people',
                $acc_receivable['balance'],
                0.00
            );
        }

        // get accounts payable
        $accounts_payable = $this->getAccountsPayableBalanceWithPeople($args['start_date']); //getAccountsPayableBalanceWithPeople( $args );

        foreach ($accounts_payable as $acc_payable) {
            $this->insertIntoOpeningBalance(
                $next_f_year_id,
                null,
                $acc_payable['id'],
                'people',
                0.00,
                abs($acc_payable['balance'])
            );
        }

        // sales tax receivable
        $tax_receivable = $this->salesTaxAgency($args, 'receivable');

        foreach ($tax_receivable as $receivable_agency) {
            $this->insertIntoOpeningBalance(
                $next_f_year_id,
                null,
                $receivable_agency['id'],
                'tax_agency',
                $receivable_agency['balance'],
                0.00
            );
        }

        // sales tax payable
        $tax_payable =  $this->salesTaxAgency($args, 'payable');

        foreach ($tax_payable as $payable_agency) {
           $this-> insertIntoOpeningBalance(
                $next_f_year_id,
                null,
                $payable_agency['id'],
                'tax_agency',
                0.00,
                abs($payable_agency['balance'])
            );
        }

        $owners_equity_ledger = $ledger_map->get_ledger_id_by_slug('owner_s_equity');
        $chart_equity_id      = 3;

        if (0 === $balance_sheet['owners_equity']) {
            return;
        }

        if ($balance_sheet['owners_equity'] > 0) {
            $this->insertIntoOpeningBalance(
                $next_f_year_id,
                $chart_equity_id,
                $owners_equity_ledger,
                'ledger',
                0.00,
                abs($balance_sheet['owners_equity'])
            );
        } else {
            $this->insertIntoOpeningBalance(
                $next_f_year_id,
                $chart_equity_id,
                $owners_equity_ledger,
                'ledger',
                abs($balance_sheet['owners_equity']),
                0.00
            );
        }
    }

    /**
     * Insert closing balance data into opening balance
     *
     * @param int    $f_year_id
     * @param int    $chart_id
     * @param int    $ledger_id
     * @param string $type
     * @param int    $debit
     * @param int    $credit
     *
     * @return void
     */
    function insertIntoOpeningBalance($f_year_id, $chart_id, $ledger_id, $type, $debit, $credit)
    {
        global $wpdb;

        $wpdb->insert(
            "{$wpdb->prefix}erp_acct_opening_balances",
            [
                'financial_year_id' => $f_year_id,
                'chart_id'          => $chart_id,
                'ledger_id'         => $ledger_id,
                'type'              => $type,
                'debit'             => $debit,
                'credit'            => $credit,
                'created_at'        => date('Y-m-d H:i:s'),
                'created_by'        => get_current_user_id(),
            ]
        );
    }

    /**
     * Get accounts receivable balance with people
     *
     * @param array $args
     *
     * @return array
     */
    function getAccountsReceivableBalanceWithPeople($args)
    {
        global $wpdb;

        // mainly ( debit - credit )
        $sql = "SELECT invoice.customer_id AS id, SUM( debit - credit ) AS balance
        FROM {$wpdb->prefix}erp_acct_invoice_account_details AS invoice_acd
        LEFT JOIN {$wpdb->prefix}erp_acct_invoices AS invoice ON invoice_acd.invoice_no = invoice.voucher_no
        WHERE invoice_acd.trn_date BETWEEN '%s' AND '%s' GROUP BY invoice_acd.invoice_no HAVING balance > 0";

        $data = $wpdb->get_results($wpdb->prepare($sql, $args['start_date'], $args['end_date']), ARRAY_A);

        return $this->peopleArCalcWithOpeningBalance($args['start_date'], $data, $sql);
    }

    /**
     * Get accounts payable balance with people
     *
     * @param array $args
     *
     * @return array
     */
    function getAccountsPayableBalanceWithPeople($args)
    {
        global $wpdb;

        $bill_sql = "SELECT bill.vendor_id AS id, SUM( debit - credit ) AS balance
        FROM {$wpdb->prefix}erp_acct_bill_account_details AS bill_acd
        LEFT JOIN {$wpdb->prefix}erp_acct_bills AS bill ON bill_acd.bill_no = bill.voucher_no
        WHERE bill_acd.trn_date BETWEEN '%s' AND '%s' GROUP BY bill_acd.bill_no HAVING balance < 0";

        $purchase_sql = "SELECT purchase.vendor_id AS id, SUM( debit - credit ) AS balance
        FROM {$wpdb->prefix}erp_acct_purchase_account_details AS purchase_acd
        LEFT JOIN {$wpdb->prefix}erp_acct_purchase AS purchase ON purchase_acd.purchase_no = purchase.voucher_no
        WHERE purchase_acd.trn_date BETWEEN '%s' AND '%s' GROUP BY purchase_acd.purchase_no HAVING balance < 0";

        $bill_data     = $wpdb->get_results($wpdb->prepare($bill_sql, $args['start_date'], $args['end_date']), ARRAY_A);
        $purchase_data = $wpdb->get_results($wpdb->prepare($purchase_sql, $args['start_date'], $args['end_date']), ARRAY_A);

        return $this->vendorApCalcWithOpeningBalance(
            $args['start_date'],
            $bill_data,
            $purchase_data,
            $bill_sql,
            $purchase_sql
        );
    }

    /**
     * Get people account receivable calculate with opening balance within financial year date range
     *
     * @param string $bs_start_date
     * @param float  $data          => account details data on balance sheet date range
     * @param string $sql
     * @param string $type
     *
     * @return array
     */
    function peopleArCalcWithOpeningBalance($bs_start_date)
    {
        global $wpdb;

        // get closest financial year id and start date
        $closest_fy_date = $trialbal->getClosestFnYearDate($bs_start_date);

        // get opening balance data within that(^) financial year
        $opening_balance = $this->customerArOpeningBalanceByFnYearId($closest_fy_date['id']);

        // $merged = array_merge( $data, $opening_balance );
        return $this->getFormattedPeopleBalance($opening_balance);

    }

    /**
     * Get people account payable calculate with opening balance within financial year date range
     *
     * @param string $bs_start_date
     * @param float  $data          => account details data on balance sheet date range
     * @param string $sql
     * @param string $type
     *
     * @return array
     */
    function vendorApCalcWithOpeningBalance($bs_start_date)
    {
        global $wpdb;

        // get closest financial year id and start date
        $closest_fy_date = $trialbal->getClosestFnYearDate($bs_start_date);

        // get opening balance data within that(^) financial year
        $opening_balance =$this->vendorSpOpeningBalanceByFnYearId($closest_fy_date['id']);

        // $merged = array_merge( $bill_data, $purchase_data, $opening_balance );
        return $this->getFormattedPeopleBalance($opening_balance);
    }

    /**
     * People accounts receivable from opening balance
     *
     * @param int $id
     *
     * @return void
     */
    function customerArOpeningBalanceByFnYearId($id)
    {
        global $wpdb;

        $sql = "SELECT ledger_id AS id, SUM( debit - credit ) AS balance
        FROM {$wpdb->prefix}erp_acct_opening_balances
        WHERE financial_year_id = %d AND type = 'people' GROUP BY ledger_id HAVING balance > 0";

        return $wpdb->get_results($wpdb->prepare($sql, $id), ARRAY_A);
    }

    /**
     * People accounts payable from opening balance
     *
     * @param int $id
     *
     * @return void
     */
    function vendorSpOpeningBalanceByFnYearId($id)
    {
        global $wpdb;

        $sql = "SELECT ledger_id AS id, SUM( debit - credit ) AS balance
        FROM {$wpdb->prefix}erp_acct_opening_balances
        WHERE financial_year_id = %d AND type = 'people' GROUP BY ledger_id HAVING balance < 0";

        return $wpdb->get_results($wpdb->prepare($sql, $id), ARRAY_A);
    }

    /**
     * Accounts receivable array merge
     *
     * @param array $arr1
     * @param array $arr2
     *
     * @return array
     */
    function getFormattedPeopleBalance($arr)
    {
        $temp = [];

        foreach ($arr as $entry) {
            // get index by id from a multidimensional array
            $index = array_search($entry['id'], array_column($arr, 'id'), true);

            if (!empty($temp[$index])) {
                $temp[$index]['balance'] += $entry['balance'];
            } else {
                $temp[] = [
                    'id'      => $entry['id'],
                    'balance' => $entry['balance'],
                ];
            }
        }

        return $temp;
    }

    /**
     * Sales tax agency with closing balance
     *
     * @param array $args
     * @param array $type
     *
     * @return float
     */
    function salesTaxAgency($args, $type)
    {
        global $wpdb;

        if ('payable' === $type) {
            $having = 'HAVING balance < 0';
        } elseif ('receivable' === $type) {
            $having = 'HAVING balance > 0';
        }

        $sql = "SELECT agency_id AS id, SUM( debit - credit ) AS balance FROM {$wpdb->prefix}erp_acct_tax_agency_details
        WHERE trn_date BETWEEN '%s' AND '%s'
        GROUP BY agency_id {$having}";

        $data = $wpdb->get_results($wpdb->prepare($sql, $args['start_date'], $args['end_date']), ARRAY_A);

        return $this->salesTaxAgencyWithOpeningBalance($args['start_date'], $data, $sql, $type);
    }

    /**
     * Get sales tax payable calculate with opening balance within financial year date range
     *
     * @param string $bs_start_date
     * @param float  $data          => agency details data on trial balance date range
     * @param string $sql
     * @param string $type
     *
     * @return float
     */
    function salesTaxAgencyWithOpeningBalance($bs_start_date, $data, $sql, $type)
    {
        global $wpdb;

        // get closest financial year id and start date
        $closest_fy_date = $trialbal->getClosestFnYearDate($bs_start_date);

        // get opening balance data within that(^) financial year
        $opening_balance = $this->salesTaxAgencyOpeningBalanceByFnYearId($closest_fy_date['id'], $type);

        $merged = array_merge($data, $opening_balance);
        $result = $this->getFormattedPeopleBalance($merged);

        // should we go further calculation, check the diff
        if (!erp_acct_has_date_diff($bs_start_date, $closest_fy_date['start_date'])) {
            return $result;
        } else {
            $prev_date_of_tb_start = date('Y-m-d', strtotime('-1 day', strtotime($bs_start_date)));
        }

        // get agency details data between
        //     `financial year start date`
        // and
        //     `previous date from trial balance start date`
        $agency_details_balance = $wpdb->get_results($wpdb->prepare($sql, $closest_fy_date['start_date'], $prev_date_of_tb_start), ARRAY_A);

        $merged = array_merge($result, $agency_details_balance);

        return $this->getFormattedPeopleBalance($merged);
    }

    /**
     * @param int    $id
     * @param string $type
     *
     * @return void
     */
    function salesTaxAgencyOpeningBalanceByFnYearId($id, $type)
    {
        global $wpdb;

        if ('payable' === $type) {
            $having = 'HAVING balance < 0';
        } elseif ('receivable' === $type) {
            $having = 'HAVING balance > 0';
        }

        $sql = "SELECT ledger_id AS id, SUM( debit - credit ) AS balance
            FROM {$wpdb->prefix}erp_acct_opening_balances
            WHERE type = 'tax_agency' GROUP BY ledger_id {$having}";

        return $wpdb->get_results($sql, ARRAY_A);
    }
}
