<?php

namespace Modules\Account\Classes;

class Bank
{
    /**
     * Get all payments
     *
     * @return mixed
     */
    function getPayments($args = [])
    {
        global $wpdb;

        $defaults = [
            'number'  => 20,
            'offset'  => 0,
            'orderby' => 'id',
            'order'   => 'DESC',
            'count'   => false,
            's'       => '',
        ];

        $args = wp_parse_args($args, $defaults);

        $limit = '';

        if ('-1' !== $args['number']) {
            $limit = "LIMIT {$args['number']} OFFSET {$args['offset']}";
        }

        $sql  = 'SELECT';
        $sql .= $args['count'] ? ' COUNT( id ) as total_number ' : ' * ';
        $sql .= "FROM {$wpdb->prefix}erp_acct_invoice_receipts ORDER BY {$args['orderby']} {$args['order']} {$limit}";

        if ($args['count']) {
            return $wpdb->get_var($sql);
        }

        $payment_data = $wpdb->get_results($sql, ARRAY_A);

        return $payment_data;
    }

    /**
     * Get a single payment
     *
     * @param $invoice_no
     *
     * @return mixed
     */
    function getPayment($invoice_no)
    {
        global $wpdb;

        $sql = "SELECT
                pay_inv.id,
                pay_inv.voucher_no,
                pay_inv.customer_id,
                pay_inv.customer_name,
                pay_inv.trn_date,
                pay_inv.amount,
                pay_inv.trn_by,
                pay_inv.ref,
                pay_inv.trn_by_ledger_id,
                pay_inv.particulars,
                pay_inv.attachments,
                pay_inv.status,
                pay_inv.created_at,
                pay_inv.transaction_charge,

                pay_inv_detail.invoice_no,
                pay_inv_detail.amount as pay_inv_detail_amount,

                ledger_detail.particulars,
                ledger_detail.debit,
                ledger_detail.credit

            from {$wpdb->prefix}erp_acct_invoice_receipts as pay_inv

            LEFT JOIN {$wpdb->prefix}erp_acct_invoice_receipts_details as pay_inv_detail ON pay_inv.voucher_no = pay_inv_detail.voucher_no
            LEFT JOIN {$wpdb->prefix}erp_acct_ledger_details as ledger_detail ON pay_inv.voucher_no = ledger_detail.trn_no

            WHERE pay_inv.voucher_no = {$invoice_no}";

       //config()->set('database.connections.mysql.strict', false);
//config()->set('database.connections.mysql.strict', true);

        $row = $wpdb->get_row($sql, ARRAY_A);

        $row['line_items'] = $this->formatPaymentLineItems($invoice_no);
        $row['pdf_link']   = $this-> pdfAbsPathToUrl($invoice_no);

        return $row;
    }

    /**
     * Insert payment info
     *
     * @param $data
     *
     * @return mixed
     */
    function insertPayment($data)
    {
        global $wpdb;

        $created_by         = get_current_user_id();
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['created_by'] = $created_by;
        $voucher_no         = null;
        $currency           = erp_get_currency(true);

        try {
            $wpdb->query('START TRANSACTION');

            if (floatval($data['amount']) < 0) {
                $trn_type = 'return_payment';
            } else {
                $trn_type = 'payment';
            }

            $wpdb->insert(
                $wpdb->prefix . 'erp_acct_voucher_no',
                [
                    'type'       => $trn_type,
                    'currency'   => $currency,
                    'created_at' => $data['created_at'],
                    'created_by' => $data['created_by'],
                    'updated_at' => isset($data['updated_at']) ? $data['updated_at'] : '',
                    'updated_by' => isset($data['updated_by']) ? $data['updated_by'] : '',
                ]
            );

            $voucher_no = $wpdb->insert_id;

            $payment_data = $this->getFormattedPaymentData($data, $voucher_no);

            // check transaction charge
            $transaction_charge = 0;

            if (isset($payment_data['bank_trn_charge']) && 0 < (float) $payment_data['bank_trn_charge'] && 2 === (int) $payment_data['trn_by']) {
                $transaction_charge = (float) $payment_data['bank_trn_charge'];
            }

            $wpdb->insert(
                $wpdb->prefix . 'erp_acct_invoice_receipts',
                [
                    'voucher_no'         => $voucher_no,
                    'customer_id'        => $payment_data['customer_id'],
                    'customer_name'      => $payment_data['customer_name'],
                    'trn_date'           => $payment_data['trn_date'],
                    'particulars'        => $payment_data['particulars'],
                    'amount'             => abs(floatval($payment_data['amount'])),
                    'transaction_charge' => $transaction_charge,
                    'ref'                => $payment_data['ref'],
                    'trn_by'             => $payment_data['trn_by'],
                    'attachments'        => $payment_data['attachments'],
                    'status'             => $payment_data['status'],
                    'trn_by_ledger_id'   => $payment_data['trn_by_ledger_id'],
                    'created_at'         => $payment_data['created_at'],
                    'created_by'         => $payment_data['created_by'],
                    'updated_at'         => $payment_data['updated_at'],
                    'updated_by'         => $payment_data['updated_by'],
                ]
            );

            $items = $payment_data['line_items'];

            foreach ($items as $key => $item) {
                $total              = 0;
                $invoice_no[$key] = $payment_data['invoice_no'];
                $total += $item['line_total'];

                $payment_data['amount'] = $total;

                $recpayments->insertPaymentLineItems($payment_data, $item, $voucher_no);
            }

            if (isset($payment_data['trn_by']) && 3 === $payment_data['trn_by']) {
                $common->insertCheckData($payment_data);
            }

            // add transaction charge entry to ledger
            if ($transaction_charge) {
                $bank->insertBankTransactionChargeIntoLedger($payment_data);
            }

            $data['dr'] = 0;
            $data['cr'] = 0;

            if (floatval($payment_data['amount']) < 0) {
                $data['dr'] = abs(floatval($payment_data['amount']));
            } else {
                $data['cr'] = $payment_data['amount'];
            }

            $trans->insertDataIntoPeopleTrnDetails($data, $voucher_no);

            do_action('erp_acct_after_payment_create', $payment_data, $voucher_no);


            $wpdb->query('COMMIT');
        } catch (Exception $e) {
            $wpdb->query('ROLLBACK');

            return new WP_error('payment-exception', $e->getMessage());
        }

        foreach ($items as $key => $item) {
            $this->changeInvoiceStatus($item['invoice_no']);
        }

        $payment = $recpayments->getPayment($voucher_no);

        $payment['email'] = erp_get_people_email($data['customer_id']);

        do_action('erp_acct_new_transaction_payment', $voucher_no, $payment);

        return $payment;
    }

    /**
     * Insert payment line items
     *
     * @param $data
     * @param $invoice_no
     * @param $voucher_no
     * @param $due
     *
     * @return int
     */
    function insertPaymentLineItems($data, $item, $voucher_no)
    {
        global $wpdb;

        $payment_data               = $this->getFormattedPaymentData($data, $voucher_no, $item['invoice_no']);
        $created_by                 = get_current_user_id();
        $payment_data['created_at'] = date('Y-m-d H:i:s');
        $payment_data['created_by'] = $created_by;

        $wpdb->insert(
            $wpdb->prefix . 'erp_acct_invoice_receipts_details',
            [
                'voucher_no' => $voucher_no,
                'invoice_no' => $item['invoice_no'],
                'amount'     => abs($item['line_total']),
                'created_at' => $payment_data['created_at'],
                'created_by' => $payment_data['created_by'],
                'updated_at' => $payment_data['updated_at'],
                'updated_by' => $payment_data['updated_by'],
            ]
        );

        if (1 === $payment_data['status']) {
            return;
        }

        $debit  = 0;
        $credit = 0;

        if (floatval($item['line_total']) < 0) {
            $debit  = abs(floatval($item['line_total']));
        } else {
            $credit = $item['line_total'];
        }

        $wpdb->insert(
            $wpdb->prefix . 'erp_acct_invoice_account_details',
            [
                'invoice_no'  => $item['invoice_no'],
                'trn_no'      => $voucher_no,
                'trn_date'    => $payment_data['trn_date'],
                'particulars' => $payment_data['particulars'],
                'debit'       => $debit,
                'credit'      => $credit,
                'created_at'  => $payment_data['created_at'],
                'created_by'  => $payment_data['created_by'],
                'updated_at'  => $payment_data['updated_at'],
                'updated_by'  => $payment_data['updated_by'],
            ]
        );

        $this->insertPaymentDataIntoLedger($payment_data);

        return $voucher_no;
    }

    /**
     * Update payment data
     *
     * @param $data
     * @param $invoice_no
     *
     * @return mixed
     */
    function updatePayment($data, $voucher_no)
    {
        global $wpdb;

        $updated_by         = get_current_user_id();
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['updated_by'] = $updated_by;

        try {
            $wpdb->query('START TRANSACTION');

            $payment_data = $this->getFormattedPaymentData($data, $voucher_no);

            $wpdb->update(
                $wpdb->prefix . 'erp_acct_invoice_receipts',
                [
                    'trn_date'         => $payment_data['trn_date'],
                    'particulars'      => $payment_data['particulars'],
                    'amount'           => $payment_data['amount'],
                    'trn_by'           => $payment_data['trn_by'],
                    'trn_by_ledger_id' => $payment_data['trn_by_ledger_id'],
                    'created_at'       => $payment_data['created_at'],
                    'created_by'       => $payment_data['created_by'],
                    'updated_at'       => $payment_data['updated_at'],
                    'updated_by'       => $payment_data['updated_by'],
                ],
                [
                    'voucher_no' => $voucher_no,
                ]
            );

            $items = $payment_data['line_items'];

            foreach ($items as $key => $item) {
                $total = 0;

                $invoice_no[$key] = $item['invoice_id'];
                $total += $item['line_total'];

                $payment_data['amount'] = $total;

                erp_acct_update_payment_line_items($payment_data, $voucher_no, $invoice_no[$key]);
            }

            if (isset($payment_data['trn_by']) && 3 === $payment_data['trn_by']) {
                $common->insertCheckData($payment_data);
            }

            $wpdb->query('COMMIT');
        } catch (Exception $e) {
            $wpdb->query('ROLLBACK');

            return new WP_error('payment-exception', $e->getMessage());
        }

        foreach ($items as $key => $item) {
            $this->changeInvoiceStatus($item['invoice_no']);
        }

        return $recpayments->getPayment($voucher_no);
    }

    /**
     * Insert payment line items
     *
     * @param $data
     * @param $invoice_no
     * @param $voucher_no
     * @param $due
     *
     * @return int
     */
    function updatePaymentLineItems($data, $invoice_no, $voucher_no)
    {
        global $wpdb;

        $payment_data = $this->getFormattedPaymentData($data, $voucher_no, $invoice_no);

        $wpdb->update(
            $wpdb->prefix . 'erp_acct_invoice_receipts_details',
            [
                'voucher_no' => $voucher_no,
                'amount'     => abs($payment_data['amount']),
                'created_at' => $payment_data['created_at'],
                'created_by' => $payment_data['created_by'],
                'updated_at' => $payment_data['updated_at'],
                'updated_by' => $payment_data['updated_by'],
            ],
            [
                'invoice_no' => $invoice_no,
            ]
        );

        if (1 === $payment_data['status']) {
            return;
        }

        $debit  = 0;
        $credit = 0;

        if (floatval($payment_data['amount']) < 0) {
            $debit = abs(floatval($payment_data['amount']));
        } else {
            $credit  = $payment_data['amount'];
        }

        $wpdb->update(
            $wpdb->prefix . 'erp_acct_invoice_account_details',
            [
                'trn_no'      => $voucher_no,
                'particulars' => $payment_data['particulars'],
                'trn_date'    => $payment_data['trn_date'],
                'debit'       => $debit,
                'credit'      => $credit,
                'created_at'  => $payment_data['created_at'],
                'created_by'  => $payment_data['created_by'],
                'updated_at'  => $payment_data['updated_at'],
                'updated_by'  => $payment_data['updated_by'],
            ],
            [
                'invoice_no' => $invoice_no,
            ]
        );

        $this->insertPaymentDataIntoLedger($payment_data);

        return $voucher_no;
    }

    /**
     * Get formatted payment data
     *
     * @param $data
     * @param $voucher_no
     * @param $invoice_no
     *
     * @return mixed
     */
    function getFormattedPaymentData($data, $voucher_no, $invoice_no = 0)
    {
        $payment_data = [];

        // We can pass the name from view... to reduce query load
        $user_info = erp_get_people($data['customer_id']);
        $company   = new \WeDevs\ERP\Company();

        $payment_data['voucher_no']       = !empty($voucher_no) ? $voucher_no : 0;
        $payment_data['invoice_no']       = !empty($invoice_no) ? $invoice_no : 0;
        $payment_data['customer_id']      = isset($data['customer_id']) ? $data['customer_id'] : null;
        $payment_data['customer_name']    = isset($user_info) ? $user_info->first_name . ' ' . $user_info->last_name : '';
        $payment_data['trn_date']         = isset($data['trn_date']) ? $data['trn_date'] : date('Y-m-d');
        $payment_data['line_items']       = isset($data['line_items']) ? $data['line_items'] : [];
        $payment_data['created_at']       = date('Y-m-d');
        $payment_data['amount']           = isset($data['amount']) ? $data['amount'] : 0;
        $payment_data['bank_trn_charge']  = isset($data['bank_trn_charge']) ? $data['bank_trn_charge'] : 0;
        $payment_data['ref']              = isset($data['ref']) ? $data['ref'] : null;
        $payment_data['attachments']      = isset($data['attachments']) ? $data['attachments'] : '';
        $payment_data['voucher_type']     = isset($data['type']) ? $data['type'] : '';
        $payment_data['particulars']      = !empty($data['particulars']) ? $data['particulars'] : sprintf(__('Invoice receipt created with voucher no %s', 'erp'), $voucher_no);
        $payment_data['trn_by']           = isset($data['trn_by']) ? $data['trn_by'] : '';
        $payment_data['trn_by_ledger_id'] = isset($data['deposit_to']) ? $data['deposit_to'] : null;
        $payment_data['status']           = isset($data['status']) ? $data['status'] : null;
        $payment_data['check_no']         = isset($data['check_no']) ? $data['check_no'] : 0;
        $payment_data['pay_to']           = isset($user_info) ? $user_info->first_name . ' ' . $user_info->last_name : '';
        $payment_data['name']             = isset($data['name']) ? $data['name'] : $company->name;
        $payment_data['bank']             = isset($data['bank']) ? $data['bank'] : '';
        $payment_data['voucher_type']     = isset($data['type']) ? $data['type'] : '';
        $payment_data['created_at']       = isset($data['created_at']) ? $data['created_at'] : null;
        $payment_data['created_by']       = isset($data['created_by']) ? $data['created_by'] : '';
        $payment_data['updated_at']       = isset($data['updated_at']) ? $data['updated_at'] : null;
        $payment_data['updated_by']       = isset($data['updated_by']) ? $data['updated_by'] : '';
        $payment_data['trn_by_ledger_id'] = empty($payment_data['trn_by_ledger_id']) ? $data['trn_by_ledger_id'] : $payment_data['trn_by_ledger_id'];

        return $payment_data;
    }

    /**
     * Delete a payment
     *
     * @param $id
     *
     * @return void
     */
    function deletePayment($id)
    {
        global $wpdb;

        $wpdb->delete($wpdb->prefix . 'erp_acct_invoice_receipts', ['voucher_no' => $id]);
        $wpdb->delete($wpdb->prefix . 'erp_acct_invoice_receipts_details', ['voucher_no' => $id]);
        $wpdb->delete($wpdb->prefix . 'erp_acct_invoice_account_details', ['invoice_no' => $id]);
    }

    /**
     * Void a payment
     *
     * @param $id
     *
     * @return void
     */
    function voidPayment($id)
    {
        global $wpdb;

        if (!$id) {
            return;
        }

        $wpdb->update(
            $wpdb->prefix . 'erp_acct_invoice_receipts',
            [
                'status' => 8,
            ],
            ['voucher_no' => $id]
        );

        $wpdb->delete($wpdb->prefix . 'erp_acct_ledger_details', ['trn_no' => $id]);
        $wpdb->delete($wpdb->prefix . 'erp_acct_invoice_account_details', ['trn_no' => $id]);
    }

    /**
     * Update invoice status after a payment
     *
     * @param $invoice_no
     *
     * @return void
     */
    function changeInvoiceStatus($invoice_no)
    {
        global $wpdb;

        $due = (float) $invoices->getInvoiceDue($invoice_no);

        if (0.00 === $due) {
            $wpdb->update(
                $wpdb->prefix . 'erp_acct_invoices',
                [
                    'status' => 4,
                ],
                ['voucher_no' => $invoice_no]
            );
        } else {
            $wpdb->update(
                $wpdb->prefix . 'erp_acct_invoices',
                [
                    'status' => 5,
                ],
                ['voucher_no' => $invoice_no]
            );
        }
    }

    /**
     * Insert Payment/s data into ledger
     *
     * @param array $payment_data
     *
     * @return mixed
     */
    function insertPaymentDataIntoLedger($payment_data)
    {
        global $wpdb;

        if (1 === $payment_data['status'] || (isset($payment_data['trn_by']) && 4 === $payment_data['trn_by'])) {
            return;
        }

        $debit  = 0;
        $credit = 0;

        if (floatval($payment_data['amount']) < 0) {
            $credit = abs(floatval($payment_data['amount']));
        } else {
            $debit  = $payment_data['amount'];
        }

        // Insert amount in ledger_details
        $wpdb->insert(
            $wpdb->prefix . 'erp_acct_ledger_details',
            [
                'ledger_id'   => $payment_data['trn_by_ledger_id'],
                'trn_no'      => $payment_data['voucher_no'],
                'particulars' => $payment_data['particulars'],
                'debit'       => $debit,
                'credit'      => $credit,
                'trn_date'    => $payment_data['trn_date'],
                'created_at'  => $payment_data['created_at'],
                'created_by'  => $payment_data['created_by'],
                'updated_at'  => $payment_data['updated_at'],
                'updated_by'  => $payment_data['updated_by'],
            ]
        );
    }

    /**
     * Update Payment/s data into ledger
     *
     * @param array $payment_data
     * @param int   $invoice_no
     *
     * @return mixed
     */
    function updatePaymentDataInLedger($payment_data, $invoice_no)
    {
        global $wpdb;

        if (1 === $payment_data['status'] || (isset($payment_data['trn_by']) && 4 === $payment_data['trn_by'])) {
            return;
        }

        $debit  = 0;
        $credit = 0;

        if (floatval($payment_data['amount']) < 0) {
            $credit = abs(floatval($payment_data['amount']));
        } else {
            $debit  = $payment_data['amount'];
        }

        // Update amount in ledger_details
        $wpdb->update(
            $wpdb->prefix . 'erp_acct_ledger_details',
            [
                'ledger_id'   => $payment_data['trn_by_ledger_id'],
                'particulars' => $payment_data['particulars'],
                'debit'       => $debit,
                'credit'      => $credit,
                'trn_date'    => $payment_data['trn_date'],
                'created_at'  => $payment_data['created_at'],
                'created_by'  => $payment_data['created_by'],
                'updated_at'  => $payment_data['updated_at'],
                'updated_by'  => $payment_data['updated_by'],
            ],
            [
                'trn_no' => $invoice_no,
            ]
        );
    }

    /**
     * Get Payment count
     *
     * @return int
     */
    function getPaymentCount()
    {
        global $wpdb;

        $row = $wpdb->get_row('SELECT COUNT(*) as count FROM ' . $wpdb->prefix . 'erp_acct_invoice_receipts');

        return $row->count;
    }

    /**
     * Format payment line items
     *
     * @param string $invoice
     *
     * @return array
     */
    function formatPaymentLineItems($invoice = 'all')
    {
        global $wpdb;

        $sql = 'SELECT inv_rec_detail.id, inv_rec_detail.voucher_no, inv_rec_detail.invoice_no, inv_rec_detail.amount, voucher.type ';

        if ('all' === $invoice) {
            $invoice_sql = '';
        } else {
            $invoice_sql = 'WHERE voucher_no = ' . $invoice;
        }
        $sql .= "FROM {$wpdb->prefix}erp_acct_invoice_receipts_details AS inv_rec_detail
            LEFT JOIN {$wpdb->prefix}erp_acct_voucher_no AS voucher
            ON inv_rec_detail.voucher_no = voucher.id
            {$invoice_sql}";

        return $wpdb->get_results($sql, ARRAY_A);
    }
}
