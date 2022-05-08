<?php

namespace Modules\Account\Classes;


use Modules\Account\Classes\Reports\TrialBalance;

use Modules\Account\Classes\People;
use Modules\Account\Classes\TaxAgencies;
use Modules\Account\Classes\LedgerAccounts;


use Illuminate\Support\Facades\DB;

class OpenBalances
{

    /**
     * Get all opening_balances
     *
     * @return mixed
     */
    function getAllOpeningBalances($args = [])
    {


        $defaults = [
            'number'  => 20,
            'offset'  => 0,
            'orderby' => 'id',
            'order'   => 'DESC',
            'count'   => false,
            's'       => '',
        ];

        $args = array_merge($defaults, $args);

        $where = '';
        $limit = '';

        if (!empty($args['start_date'])) {
            $where .= "WHERE opening_balance.trn_date BETWEEN '{$args['start_date']}' AND '{$args['end_date']}'";
        }

        if ('-1' === $args['number']) {
            $limit = "LIMIT {$args['number']} OFFSET {$args['offset']}";
        }

        $sql = 'SELECT';

        if ($args['count']) {
            $sql .= ' COUNT( DISTINCT opening_balance.id ) as total_number';
        } else {
            $sql .= ' *';
        }

        $sql .= " FROM erp_acct_opening_balances AS opening_balance LEFT JOIN erp_acct_financial_years AS financial_year";
        $sql .= " ON opening_balance.financial_year_id = financial_year.id {$where} GROUP BY financial_year.name ORDER BY financial_year.{$args['orderby']} {$args['order']} {$limit}";

        if ($args['count']) {
            $wpdb->get_results($sql);

            return $wpdb->num_rows;
        }

        return $wpdb->get_results($sql, ARRAY_A);
    }

    /**
     * Get opening_balances of a year
     *
     * @param $year_id
     *
     * @return mixed
     */
    function getOpeningBalance($year_id)
    {


        $rows = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT ob.id, ob.financial_year_id, ob.ledger_id, ledger.name, ob.chart_id, ob.debit, ob.credit FROM erp_acct_opening_balances as ob LEFT JOIN erp_acct_ledgers as ledger ON ledger.id = ob.ledger_id WHERE financial_year_id = %d AND ob.type = 'ledger'",
                $year_id
            ),
            ARRAY_A
        );

        return $rows;
    }

    /**
     * Get virtual accounts of a year
     *
     * @param $year_id
     *
     * @return mixed
     */
    function getVirtualAcct($year_id)
    {


        $rows = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT ob.id, ob.financial_year_id, ob.ledger_id, ob.type, ob.debit, ob.credit
            FROM erp_acct_opening_balances as ob WHERE financial_year_id = %d AND ob.type <> 'ledger'",
                $year_id
            ),
            ARRAY_A
        );

        $rows = $this->getOpbInvoiceAccountDetails($year_id);

        return $rows;
    }

    /**
     * Insert opening_balance data
     *
     * @param $data
     *
     * @return mixed
     */
    function insertOpeningBalance($data)
    {


        $created_by         = auth()->user()->id;
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['created_by'] = $created_by;

        try {
            $wpdb->query('START TRANSACTION');

            $opening_balance_data = $this->getFormattedOpeningBalanceData($data);

            $items = $opening_balance_data['ledgers'];

            $ledgers = [];

            foreach ($items as $item) {
                $ledgers = array_merge($ledgers, $item);
            }

            $year_id = $opening_balance_data['year'];

            $wpdb->query($wpdb->prepare("DELETE FROM erp_acct_opening_balances WHERE financial_year_id = %d", $year_id));

            foreach ($ledgers as $ledger) {
                if ((isset($ledger['debit']) && (float) $ledger['debit'] > 0) || (isset($ledger['credit']) && (float) $ledger['credit'] > 0)) {
                    DB::table('erp_acct_opening_balances')
                        ->insert(
                            [
                                'financial_year_id' => $year_id,
                                'ledger_id'         => $ledger['ledger_id'],
                                'chart_id'          => $ledger['chart_id'],
                                'type'              => 'ledger',
                                'debit'             => isset($ledger['debit']) ? $ledger['debit'] : 0,
                                'credit'            => isset($ledger['credit']) ? $ledger['credit'] : 0,
                                'created_at'        => $opening_balance_data['created_at'],
                                'created_by'        => $opening_balance_data['created_by'],
                                'updated_at'        => $opening_balance_data['updated_at'],
                                'updated_by'        => $opening_balance_data['updated_by'],
                            ]
                        );
                }
            }

            $this->insertObVirAccounts($opening_balance_data, $year_id);

            $wpdb->query('COMMIT');
        } catch (\Exception $e) {
            $wpdb->query('ROLLBACK');

            return new WP_error('opening_balance-exception', $e->getMessage());
        }

        return $this->getOpeningBalance($year_id);
    }

    /**
     * Insert virtual accounts data
     *
     * @param $data
     * @param $year_id
     */
    function insertObVirAccounts($data, $year_id)
    {


        if (!empty($data['acct_rec'])) {
            foreach ($data['acct_rec'] as $acct_rec) {
                DB::table('erp_acct_opening_balances')
                    ->insert(
                        [
                            'financial_year_id' => $year_id,
                            'ledger_id'         => $acct_rec['people']['id'],
                            'type'              => 'people',
                            'debit'             => $acct_rec['debit'],
                            'credit'            => 0,
                            'created_at'        => $data['created_at'],
                            'created_by'        => $data['created_by'],
                            'updated_at'        => $data['updated_at'],
                            'updated_by'        => $data['updated_by'],
                        ]
                    );
            }
        }

        if (!empty($data['acct_pay'])) {
            foreach ($data['acct_pay'] as $acct_pay) {
                DB::table('erp_acct_opening_balances')
                    ->insert(
                        [
                            'financial_year_id' => $year_id,
                            'ledger_id'         => $acct_pay['people']['id'],
                            'type'              => 'people',
                            'debit'             => 0,
                            'credit'            => $acct_pay['credit'],
                            'created_at'        => $data['created_at'],
                            'created_by'        => $data['created_by'],
                            'updated_at'        => $data['updated_at'],
                            'updated_by'        => $data['updated_by'],
                        ]
                    );
            }
        }

        if (!empty($data['tax_pay'])) {
            foreach ($data['tax_pay'] as $tax_pay) {
                DB::table('erp_acct_opening_balances')
                    ->insert(
                        [
                            'financial_year_id' => $year_id,
                            'ledger_id'         => $tax_pay['agency']['id'],
                            'type'              => 'tax_agency',
                            'debit'             => 0,
                            'credit'            => $tax_pay['credit'],
                            'created_at'        => $data['created_at'],
                            'created_by'        => $data['created_by'],
                            'updated_at'        => $data['updated_at'],
                            'updated_by'        => $data['updated_by'],
                        ]
                    );
            }
        }
    }

    /**
     * Get formatted opening_balance data
     *
     * @param $data
     * @param $voucher_no
     *
     * @return mixed
     */
    function getFormattedOpeningBalanceData($data)
    {
        $opening_balance_data = [];

        $opening_balance_data['year']         = isset($data['year']) ? $data['year'] : '';
        $opening_balance_data['ledgers']      = isset($data['ledgers']) ? $data['ledgers'] : [];
        $opening_balance_data['descriptions'] = isset($data['descriptions']) ? $data['descriptions'] : '';
        $opening_balance_data['amount']       = isset($data['amount']) ? $data['amount'] : '';
        $opening_balance_data['acct_pay']     = isset($data['acct_pay']) ? $data['acct_pay'] : [];
        $opening_balance_data['acct_rec']     = isset($data['acct_rec']) ? $data['acct_rec'] : [];
        $opening_balance_data['tax_pay']      = isset($data['tax_pay']) ? $data['tax_pay'] : [];
        $opening_balance_data['created_at']   = isset($data['created_at']) ? $data['created_at'] : '';
        $opening_balance_data['created_by']   = isset($data['created_by']) ? $data['created_by'] : '';
        $opening_balance_data['updated_at']   = isset($data['updated_at']) ? $data['updated_at'] : '';
        $opening_balance_data['updated_by']   = isset($data['updated_by']) ? $data['updated_by'] : '';

        return $opening_balance_data;
    }

    /**
     * Get opening balance names
     *
     * @return array
     */
    function getOpeningBalanceNames()
    {


        $rows = $wpdb->get_results("SELECT id, name, start_date, end_date FROM erp_acct_financial_years", ARRAY_A);

        return $rows;
    }

    /**
     * Get opening balance date ranges
     *
     * @param $ob_name
     *
     * @return array
     */
    function getStartEndDate($year_id)
    {
        $dates = [];


        $rows = $wpdb->get_row($wpdb->prepare("SELECT start_date, end_date FROM erp_acct_financial_years WHERE id = %d", $year_id), ARRAY_A);

        $dates['start'] = $rows['start_date'];
        $dates['end']   = $rows['end_date'];

        return $dates;
    }

    /**
     * Get virtual accts summary for opening balance
     */
    function getObVirtualAcct($year_id)
    {

        $people = new People();
        $taxagencies = new TaxAgencies();

        $vir_ac['acct_receivable'] = $wpdb->get_results($wpdb->prepare("SELECT ledger_id as people_id, debit, credit from erp_acct_opening_balances where financial_year_id = %d and credit=0 and type='people'", $year_id), ARRAY_A);

        $vir_ac['acct_payable'] = $wpdb->get_results($wpdb->prepare("SELECT ledger_id as people_id, debit, credit from erp_acct_opening_balances where financial_year_id = %d and debit=0 and type='people'", $year_id), ARRAY_A);

        $vir_ac['tax_payable'] = $wpdb->get_results($wpdb->prepare("SELECT ledger_id as agency_id, debit, credit from erp_acct_opening_balances where financial_year_id = %d and debit=0 and type='tax_agency'", $year_id), ARRAY_A);

        for ($i = 0; $i < count($vir_ac['acct_payable']); $i++) {
            if (empty($vir_ac['acct_payable'][$i]['people_id'])) {
                return;
            }

            $vir_ac['acct_payable'][$i]['people']['id']   = $vir_ac['acct_payable'][$i]['people_id'];
            $vir_ac['acct_payable'][$i]['people']['name'] = $people->getPeopleNameByPeopleId($vir_ac['acct_payable'][$i]['people_id']);
        }

        for ($i = 0; $i < count($vir_ac['acct_receivable']); $i++) {
            if (empty($vir_ac['acct_receivable'][$i]['people_id'])) {
                return;
            }

            $vir_ac['acct_receivable'][$i]['people']['id']   = $vir_ac['acct_receivable'][$i]['people_id'];
            $vir_ac['acct_receivable'][$i]['people']['name'] = $people->getPeopleNameByPeopleId($vir_ac['acct_receivable'][$i]['people_id']);
        }

        for ($i = 0; $i < count($vir_ac['tax_payable']); $i++) {
            if (empty($vir_ac['tax_payable'][$i]['agency_id'])) {
                return;
            }

            $vir_ac['tax_payable'][$i]['agency']['id']   = $vir_ac['tax_payable'][$i]['agency_id'];
            $vir_ac['tax_payable'][$i]['agency']['name'] = $taxagencies->getTaxAgencyNameById($vir_ac['tax_payable'][$i]['agency_id']);
        }

        return $vir_ac;
    }

    /**
     * Get balance with opening balance of a ledger
     *
     * @param $ledger_id
     * @param array $args
     *
     * @return mixed
     */
    function getLedgerBalanceWithOpeningBalance($ledger_id, $start_date, $end_date)
    {

        $trialbal = new TrialBalance();
        $ledger = new LedgerAccounts();

        // get closest financial year id and start date
        $closest_fy_date = $trialbal->getClosestFnYearDate($start_date);

        // get opening balance data within that(^) financial year
        $opening_balance = (float) $reports->ledgerReportOpeningBalanceByFnYearId($closest_fy_date['id'], $ledger_id);

        // should we go further calculation, check the diff
        if (erp_acct_has_date_diff($start_date, $closest_fy_date['start_date'])) {
            $prev_date_of_start = date('Y-m-d', strtotime('-1 day', strtotime($start_date)));

            $sql1 = $wpdb->prepare(
                "SELECT SUM(debit - credit) AS balance
            FROM erp_acct_ledger_details
            WHERE ledger_id = %d AND trn_date BETWEEN '%s' AND '%s'",
                $ledger_id,
                $closest_fy_date['start_date'],
                $prev_date_of_start
            );

            $prev_ledger_details = DB::scalar($sql1);
            $opening_balance += (float) $prev_ledger_details;
        }

        // ledger details
        $sql2 = $wpdb->prepare(
            "SELECT
        SUM(debit-credit) as balance
        FROM erp_acct_ledger_details
        WHERE ledger_id = %d AND trn_date BETWEEN '%s' AND '%s'",
            $ledger_id,
            $start_date,
            $end_date
        );

        $res = $wpdb->get_row($sql2, ARRAY_A);

        $total_debit   = 0;
        $total_credit  = 0;
        $final_balance = 0;

        $final_balance = $opening_balance + $res['balance'];

        $l_data = $ledger->getLedger($ledger_id);

        if (empty($l_data)) {
            return [];
        }

        return [
            'id'           => $ledger_id,
            'name'         => $l_data->name,
            'code'         => $l_data->code,
            'obalance'     => $opening_balance,
            'balance'      => $final_balance,
            'total_debit'  => $total_debit,
            'total_credit' => $total_credit,
        ];
    }

    /**
     * Get opening balance invoice account details
     *
     * @param string $fy_start_date
     *
     * @return int
     */
    function getOpbInvoiceAccountDetails($fy_start_date)
    {


        // mainly ( debit - credit )
        $sql = "SELECT SUM(balance) AS amount
        FROM ( SELECT SUM( debit - credit ) AS balance
            FROM erp_acct_invoice_account_details WHERE trn_date < '%s'
            GROUP BY invoice_no HAVING balance > 0 )
        AS get_amount";

        return (float) DB::scalar($wpdb->prepare($sql, $fy_start_date));
    }

    /**
     * Get opening balance bill & purchase
     *
     * @param string $fy_start_date
     *
     * @return int
     */
    function getOpbBillPurchaseAccountDetails($fy_start_date)
    {


        /**
         *? Why only bills, not expense?
         *? Expense is `direct expense`, and we don't include direct expense here
         */
        $bill_sql = "SELECT SUM(balance) AS amount
        FROM ( SELECT SUM( debit - credit ) AS balance FROM erp_acct_bill_account_details WHERE trn_date < '%s'
        GROUP BY bill_no HAVING balance < 0 )
        AS get_amount";

        $purchase_sql = "SELECT SUM(balance) AS amount
        FROM ( SELECT SUM( debit - credit ) AS balance FROM erp_acct_purchase_account_details WHERE trn_date < '%s'
        GROUP BY purchase_no HAVING balance < 0 )
        AS get_amount";

        $bill_amount     = DB::scalar($wpdb->prepare($bill_sql, $fy_start_date));
        $purchase_amount = DB::scalar($wpdb->prepare($purchase_sql, $fy_start_date));

        return abs((float) $bill_amount + (float) $purchase_amount);
    }

    /**
     *Get lower and upper bound of financial years
     */
    function getDateBoundary()
    {


        $result = $wpdb->get_row("SELECT MIN(start_date) as lower, MAX(end_date) as upper FROM erp_acct_financial_years", ARRAY_A);

        return $result;
    }

    /**
     * Get current financial year
     */
    function getCurrentFinancialYear($date = '')
    {


        if (empty($date)) {
            $date = date('Y-m-d');
        }

        $result = $wpdb->get_row($wpdb->prepare("SELECT id,name,start_date,end_date FROM erp_acct_financial_years WHERE '%s' between start_date AND end_date", $date));

        return $result;
    }
}