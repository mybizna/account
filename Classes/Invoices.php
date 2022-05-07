<?php

namespace Modules\Account\Classes;

class Bank
{
    /**
     * Get all invoices
     *
     * @return mixed
     */
    function getAllInvoices($args = [])
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

        $where = '';
        $limit = '';

        if (!empty($args['start_date'])) {
            $where .= "WHERE invoice.trn_date BETWEEN '{$args['start_date']}' AND '{$args['end_date']}'";
        }

        if ('-1' === $args['number']) {
            $limit = "LIMIT {$args['number']} OFFSET {$args['offset']}";
        }

        $sql = 'SELECT';

        if ($args['count']) {
            $sql .= ' COUNT( DISTINCT invoice.id ) as total_number';
        } else {
            $sql .= ' invoice.*, SUM(ledger_detail.credit) - SUM(ledger_detail.debit) as due';
        }

        $sql .= " FROM {$wpdb->prefix}erp_acct_invoices AS invoice LEFT JOIN {$wpdb->prefix}erp_acct_ledger_details AS ledger_detail";
        $sql .= " ON invoice.voucher_no = ledger_detail.trn_no {$where} GROUP BY invoice.voucher_no ORDER BY invoice.{$args['orderby']} {$args['order']} {$limit}";

       //config()->set('database.connections.mysql.strict', false);
//config()->set('database.connections.mysql.strict', true);

        if ($args['count']) {
            return $wpdb->get_var($sql);
        }

        return $wpdb->get_results($sql, ARRAY_A);
    }

    /**
     * Get an single invoice
     *
     * @param $invoice_no
     *
     * @return mixed
     */
    function getInvoice($invoice_no)
    {
        global $wpdb;

        $sql = $wpdb->prepare(
            "SELECT

            voucher.editable,
            voucher.currency,

            invoice.id,
            invoice.voucher_no,
            invoice.customer_id,
            invoice.customer_name,
            invoice.trn_date,
            invoice.due_date,
            invoice.billing_address,
            invoice.amount,
            invoice.discount,
            invoice.discount_type,
            invoice.shipping,
            invoice.shipping_tax,
            invoice.tax,
            invoice.tax_zone_id,
            invoice.estimate,
            invoice.attachments,
            invoice.status,
            invoice.particulars,
            invoice.created_at,

            inv_acc_detail.debit,
            inv_acc_detail.credit

        FROM {$wpdb->prefix}erp_acct_invoices as invoice
        LEFT JOIN {$wpdb->prefix}erp_acct_voucher_no as voucher ON invoice.voucher_no = voucher.id
        LEFT JOIN {$wpdb->prefix}erp_acct_invoice_account_details as inv_acc_detail ON invoice.voucher_no = inv_acc_detail.trn_no
        WHERE invoice.voucher_no = %d",
            $invoice_no
        );

       //config()->set('database.connections.mysql.strict', false);
//config()->set('database.connections.mysql.strict', true);

        $row = $wpdb->get_row($sql, ARRAY_A);

        $row['line_items']  = $invoices->formatInvoiceLineItems($invoice_no);

        $row['tax_rate_id'] = empty($row['tax_zone_id']) ? $taxes->getDefaultTaxRateNameId() : (int) $row['tax_zone_id'];

        // calculate every line total
        foreach ($row['line_items'] as $key => $value) {
            $total                                   = ($value['item_total'] + $value['tax']) - $value['discount'];
            $row['line_items'][$key]['line_total'] = $total;
        }

        $row['attachments'] = isset($row['attachments']) ? maybe_unserialize($row['attachments']) : null;
        $row['total_due']   = $this->getInvoiceDue($invoice_no);
        $row['pdf_link']    = $this-> pdfAbsPathToUrl($invoice_no);

        return $row;
    }

    /**
     * Get formatted line items
     */
    function formatInvoiceLineItems($voucher_no)
    {
        global $wpdb;

        $sql = $wpdb->prepare(
            "SELECT
        inv_detail.product_id,
        inv_detail.qty,
        inv_detail.unit_price,
        inv_detail.discount,
        inv_detail.tax,
        inv_detail.item_total,
        inv_detail.ecommerce_type,

        SUM(inv_detail_tax.tax_rate) as tax_rate,

        product.name,
        product.product_type_id,
        product.category_id,
        product.vendor,
        product.cost_price,
        product.sale_price,
        product.tax_cat_id

        FROM {$wpdb->prefix}erp_acct_invoices as invoice
        LEFT JOIN {$wpdb->prefix}erp_acct_invoice_details as inv_detail ON invoice.voucher_no = inv_detail.trn_no
        LEFT JOIN {$wpdb->prefix}erp_acct_invoice_details_tax as inv_detail_tax ON inv_detail.id = inv_detail_tax.invoice_details_id
        LEFT JOIN {$wpdb->prefix}erp_acct_products as product ON inv_detail.product_id = product.id
        WHERE invoice.voucher_no = %d GROUP BY inv_detail.id",
            $voucher_no
        );

        $results = $wpdb->get_results($sql, ARRAY_A);

        if (!empty(reset($results)['ecommerce_type'])) {
            // product name should not fetch form `erp_acct_products`
            $results = array_map(
                function ($result) {
                    $result['name'] = get_the_title($result['product_id']);

                    return $result;
                },
                $results
            );
        }

        return $results;
    }

    /**
     * Insert invoice data
     *
     * @param $data
     *
     * @return int
     */
    function insertInvoice($data)
    {
        global $wpdb;

        $user_id = get_current_user_id();

        $data['created_at'] = date('Y-m-d H:i:s');
        $data['created_by'] = $user_id;
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['updated_by'] = $user_id;

        $voucher_no    = null;
        $estimate_type = 1;
        $draft         = 1;
        $currency      = erp_get_currency(true);
        $email         = erp_get_people_email($data['customer_id']);

        try {
            $wpdb->query('START TRANSACTION');

            $wpdb->insert(
                $wpdb->prefix . 'erp_acct_voucher_no',
                [
                    'type'       => 'invoice',
                    'currency'   => $currency,
                    'editable'   => 1,
                    'created_at' => $data['created_at'],
                    'created_by' => $data['created_by'],
                ]
            );

            $voucher_no = $wpdb->insert_id;

            $invoice_data = $this->getFormattedInvoiceData($data, $voucher_no);

            $wpdb->insert(
                $wpdb->prefix . 'erp_acct_invoices',
                [
                    'voucher_no'      => $invoice_data['voucher_no'],
                    'customer_id'     => $invoice_data['customer_id'],
                    'customer_name'   => $invoice_data['customer_name'],
                    'trn_date'        => $invoice_data['trn_date'],
                    'due_date'        => $invoice_data['due_date'],
                    'billing_address' => $invoice_data['billing_address'],
                    'amount'          => $invoice_data['amount'],
                    'discount'        => $invoice_data['discount'],
                    'discount_type'   => $invoice_data['discount_type'],
                    'shipping'        => $invoice_data['shipping'],
                    'shipping_tax'    => $invoice_data['shipping_tax'],
                    'tax'             => $invoice_data['tax'],
                    'tax_zone_id'     => $invoice_data['tax_rate_id'],
                    'estimate'        => $invoice_data['estimate'],
                    'attachments'     => $invoice_data['attachments'],
                    'status'          => $invoice_data['status'],
                    'particulars'     => $invoice_data['particulars'],
                    'created_at'      => $invoice_data['created_at'],
                    'created_by'      => $invoice_data['created_by'],
                ]
            );

            $this->insertInvoiceDetailsAndTax($invoice_data, $voucher_no);

            if ($estimate_type === $invoice_data['estimate'] || $draft === $invoice_data['status']) {
                $wpdb->query('COMMIT');
                $estimate          = $invoices->getInvoice($voucher_no);
                $estimate['email'] = $email;
                do_action('erp_acct_new_transaction_estimate', $voucher_no, $estimate);

                return $estimate;
            }

            $this->insertInvoiceAccountDetails($invoice_data, $voucher_no);
            $this->insertInvoiceDataIntoLedger($invoice_data);

            do_action('erp_acct_after_sales_create', $data, $voucher_no);

            $data['dr'] = $invoice_data['amount'];
            $data['cr'] = 0;
            $trans->insertDataIntoPeopleTrnDetails($data, $voucher_no);

            $wpdb->query('COMMIT');
        } catch (Exception $e) {
            $wpdb->query('ROLLBACK');

            return new WP_Error('invoice-exception', $e->getMessage());
        }

        $invoice = $invoices->getInvoice($voucher_no);

        $invoice['email'] = erp_get_people_email($data['customer_id']);

        do_action('erp_acct_new_transaction_sales', $voucher_no, $invoice);

        return $invoice;
    }

    /**
     * Insert line items and details on invoice create
     *
     * @param array $invoice_data
     * @param int   $voucher_no
     *
     * @return void
     */
    function insertInvoiceDetailsAndTax($invoice_data, $voucher_no, $contra = false)
    {
        global $wpdb;

        $user_id = get_current_user_id();

        $invoice_data['created_at'] = date('Y-m-d');
        $invoice_data['created_by'] = $user_id;
        $invoice_data['updated_at'] = date('Y-m-d');
        $invoice_data['updated_by'] = $user_id;

        $estimate_type      = 1;
        $draft              = 1;
        $tax_agency_details = [];

        $items = $invoice_data['line_items'];

        foreach ($items as $item) {
            $sub_total = $item['qty'] * $item['unit_price'];

            // insert into invoice details
            $wpdb->insert(
                $wpdb->prefix . 'erp_acct_invoice_details',
                [
                    'trn_no'         => $voucher_no,
                    'product_id'     => $item['product_id'],
                    'qty'            => $item['qty'],
                    'unit_price'     => $item['unit_price'],
                    'discount'       => $item['discount'],
                    'tax'            => $item['tax'],
                    'tax_cat_id'     => !empty($item['tax_cat_id']) ? $item['tax_cat_id'] : null,
                    'item_total'     => $sub_total,
                    'ecommerce_type' => !empty($item['ecommerce_type']) ? $item['ecommerce_type'] : null,
                    'created_at'     => $invoice_data['created_at'],
                    'created_by'     => $invoice_data['created_by'],
                ]
            );

            $details_id = $wpdb->insert_id;

            if ($estimate_type === $invoice_data['estimate'] || $draft === $invoice_data['status']) {
                continue;
            }

            if (empty($invoice_data['tax_rate_id']) && empty($item['tax_cat_id'])) {
                $tax_rate_agency = !empty($item['tax_rate_agency']) ? $item['tax_rate_agency'] : null;
            } else {
                // calculate tax for every related agency
                $tax_rate_agency = $common->getTaxRateWithAgency($invoice_data['tax_rate_id'], $item['tax_cat_id']);
            }

            if (!empty($tax_rate_agency)) {
                foreach ($tax_rate_agency as $rate_agency) {
                    /*==== calculate tax amount ====*/
                    $tax_amount = ((float) $item['tax'] * (float) $rate_agency['tax_rate']) / (float) $item['tax_rate'];

                    if (array_key_exists($rate_agency['agency_id'], $tax_agency_details)) {
                        $tax_agency_details[$rate_agency['agency_id']] += $tax_amount;
                    } else {
                        $tax_agency_details[$rate_agency['agency_id']] = $tax_amount;
                    }

                    /*==== insert into invoice details tax ====*/
                    $wpdb->insert(
                        $wpdb->prefix . 'erp_acct_invoice_details_tax',
                        [
                            'invoice_details_id' => $details_id,
                            'agency_id'          => $rate_agency['agency_id'],
                            'tax_rate'           => $rate_agency['tax_rate'],
                            'tax_amount'         => $tax_amount,
                            'created_at'         => $invoice_data['created_at'],
                            'created_by'         => $invoice_data['created_by'],
                        ]
                    );
                }
            }
        }

        if (!empty($tax_agency_details)) {
            foreach ($tax_agency_details as $agency_id => $tax_agency_detail) {
                $debit = 0;
                $credit = 0;

                if ($contra) {
                    $debit = $tax_agency_detail;
                } else {
                    $credit = $tax_agency_detail;
                }

                $wpdb->insert(
                    $wpdb->prefix . 'erp_acct_tax_agency_details',
                    [
                        'agency_id'   => $agency_id,
                        'trn_no'      => $voucher_no,
                        'trn_date'    => $invoice_data['trn_date'],
                        'particulars' => 'sales',
                        'debit'       => $debit,
                        'credit'      => $credit,
                        'created_at'  => $invoice_data['created_at'],
                        'created_by'  => $invoice_data['created_by'],
                    ]
                );
            }
        }
    }

    /**
     * Insert invoice account details
     *
     * @param array $invoice_data
     * @param int   $voucher_no
     *
     * @return void
     */
    function insertInvoiceAccountDetails($invoice_data, $voucher_no, $contra = false)
    {
        global $wpdb;

        $user_id = get_current_user_id();

        $invoice_data['created_at'] = date('Y-m-d H:i:s');
        $invoice_data['created_by'] = $user_id;
        $invoice_data['updated_at'] = date('Y-m-d H:i:s');
        $invoice_data['updated_by'] = $user_id;

        if ($contra) {
            $invoice_no = $invoice_data['voucher_no'];
            $debit      = 0;
            $credit     = ($invoice_data['amount'] - $invoice_data['discount']) + $invoice_data['tax'] + $invoice_data['shipping'] + $invoice_data['shipping_tax'];
        } else {
            $invoice_no = $voucher_no;
            $debit      = ($invoice_data['amount'] - $invoice_data['discount']) + $invoice_data['tax'] + $invoice_data['shipping'] + $invoice_data['shipping_tax'];
            $credit     = 0;
        }

        $wpdb->insert(
            $wpdb->prefix . 'erp_acct_invoice_account_details',
            [
                'invoice_no'  => $invoice_no,
                'trn_no'      => $voucher_no,
                'trn_date'    => $invoice_data['trn_date'],
                'particulars' => '',
                'debit'       => $debit,
                'credit'      => $credit,
                'created_at'  => $invoice_data['created_at'],
                'created_by'  => $invoice_data['created_by'],
                'updated_at'  => $invoice_data['created_at'],
                'updated_by'  => $invoice_data['created_by'],
            ]
        );
    }

    /**
     * Update invoice data
     *
     * @param $data
     * @param $invoice_no
     *
     * @return int
     */
    function updateInvoice($data, $invoice_no)
    {
        global $wpdb;

        if (1 === $data['estimate'] && $data['convert']) {
            $this->convertEstimateToInvoice($data, $invoice_no);

            return;
        }

        $user_id    = get_current_user_id();
        $voucher_no = null;

        $data['created_at'] = date('Y-m-d H:i:s');
        $data['created_by'] = $user_id;
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['updated_by'] = $user_id;

        $estimate_type = 1;
        $draft         = 1;
        $currency      = erp_get_currency(true);

        try {
            $wpdb->query('START TRANSACTION');

            if ($estimate_type === $data['estimate'] || $draft === $data['status']) {
                $this->updateDraftAndEstimate($data, $invoice_no);
            } else {
                // disable editing on old invoice
                $wpdb->update($wpdb->prefix . 'erp_acct_voucher_no', ['editable' => 0], ['id' => $invoice_no]);

                // insert contra voucher
                $wpdb->insert(
                    $wpdb->prefix . 'erp_acct_voucher_no',
                    [
                        'type'       => 'invoice',
                        'currency'   => $currency,
                        'editable'   => 0,
                        'created_at' => $data['created_at'],
                        'created_by' => $data['created_by'],
                        'updated_at' => $data['updated_at'],
                        'updated_by' => $data['updated_by'],
                    ]
                );

                $voucher_no = $wpdb->insert_id;

                $old_invoice = $invoices->getInvoice($invoice_no);

                // insert contra `erp_acct_invoices` (basically a duplication of row)
                $wpdb->query($wpdb->prepare("CREATE TEMPORARY TABLE acct_tmptable SELECT * FROM {$wpdb->prefix}erp_acct_invoices WHERE voucher_no = %d", $invoice_no));
                $wpdb->query(
                    $wpdb->prepare(
                        "UPDATE acct_tmptable SET id = %d, voucher_no = %d, particulars = 'Contra entry for voucher no \#%d', created_at = '%s'",
                        0,
                        $voucher_no,
                        $invoice_no,
                        $data['created_at']
                    )
                );
                $wpdb->query("INSERT INTO {$wpdb->prefix}erp_acct_invoices SELECT * FROM acct_tmptable");
                $wpdb->query('DROP TABLE acct_tmptable');

                // change invoice status and other things
                $status_closed = 7;
                $wpdb->query(
                    $wpdb->prepare(
                        "UPDATE {$wpdb->prefix}erp_acct_invoices SET status = %d, updated_at ='%s', updated_by = %d WHERE voucher_no IN (%d, %d)",
                        $status_closed,
                        $data['updated_at'],
                        $user_id,
                        $invoice_no,
                        $voucher_no
                    )
                );

                // insert contra `erp_acct_invoice_details` AND `erp_acct_invoice_details_tax`
                $this->insertInvoiceDetailsAndTax($old_invoice, $voucher_no, true);

                // insert contra `erp_acct_invoice_account_details`
                $this->insertInvoiceAccountDetails($old_invoice, $voucher_no, true);

                // insert contra `erp_acct_ledger_details`
                $this->insertInvoiceDataIntoLedger($old_invoice, $voucher_no, true);

                // insert new invoice with edited data
                $new_invoice = $invoices->insertInvoice($data);

                do_action('erp_acct_after_sales_update', $data, $invoice_no);

                $data['dr'] = $data['amount'];
                $data['cr'] = 0;
                $trans->updateDataIntoPeopleTrnDetails($data, $old_invoice['voucher_no']);
            }

            $wpdb->query('COMMIT');
        } catch (Exception $e) {
            $wpdb->query('ROLLBACK');

            return new WP_error('invoice-exception', $e->getMessage());
        }

        return $invoices->getInvoice($new_invoice['voucher_no']);
    }

    /**
     * Convert estimate to invoice
     *
     * @param array $data
     * @param int   $invoice_no
     *
     * @return array
     */
    function convertEstimateToInvoice($data, $invoice_no)
    {
        global $wpdb;

        $user_id  = get_current_user_id();

        $data['created_at'] = date('Y-m-d');
        $data['created_by'] = $user_id;
        $data['updated_at'] = date('Y-m-d');
        $data['updated_by'] = $user_id;
        $data['estimate']   = 0;

        try {
            $wpdb->query('START TRANSACTION');

            $invoice_data = $this->getFormattedInvoiceData($data, $invoice_no);

            $wpdb->update(
                $wpdb->prefix . 'erp_acct_invoices',
                [
                    'customer_id'     => $invoice_data['customer_id'],
                    'customer_name'   => $invoice_data['customer_name'],
                    'trn_date'        => $invoice_data['trn_date'],
                    'due_date'        => $invoice_data['due_date'],
                    'billing_address' => $invoice_data['billing_address'],
                    'amount'          => $invoice_data['amount'],
                    'discount'        => $invoice_data['discount'],
                    'discount_type'   => $invoice_data['discount_type'],
                    'shipping'        => $invoice_data['shipping'],
                    'shipping_tax'    => $invoice_data['shipping_tax'],
                    'tax'             => $invoice_data['tax'],
                    'estimate'        => false,
                    'attachments'     => $invoice_data['attachments'],
                    'status'          => 2,
                    'particulars'     => $invoice_data['particulars'],
                    'created_at'      => $invoice_data['created_at'],
                    'created_by'      => $invoice_data['created_by'],
                ],
                ['voucher_no' => $invoice_no]
            );

            $wpdb->delete($wpdb->prefix . 'erp_acct_invoice_details', ['trn_no' => $invoice_no]);

            // insert data into invoice_details
            $this->insertInvoiceDetailsAndTax($invoice_data, $invoice_no);

            $this->insertInvoiceAccountDetails($invoice_data, $invoice_no);

            $this->insertInvoiceDataIntoLedger($invoice_data, $invoice_no);

            do_action('erp_acct_after_sales_create', $data, $invoice_no);

            $data['dr'] = $invoice_data['amount'];
            $data['cr'] = 0;
            $trans->insertDataIntoPeopleTrnDetails($data, $invoice_no);

            $wpdb->query('COMMIT');
        } catch (Exception $e) {
            $wpdb->query('ROLLBACK');

            return new WP_error('invoice-exception', $e->getMessage());
        }

        $invoice = $invoices->getInvoice($invoice_no);

        $invoice['email'] = erp_get_people_email($data['customer_id']);

        do_action('erp_acct_new_transaction_sales', $invoice_no, $invoice);

        return $invoice;
    }

    /**
     * Update draft & estimate
     *
     * @param array $data
     * @param int   $invoice_no
     *
     * @return void
     */
    function updateDraftAndEstimate($data, $invoice_no)
    {
        global $wpdb;

        $invoice_data = $this->getFormattedInvoiceData($data, $invoice_no);

        $wpdb->update($wpdb->prefix . 'erp_acct_invoices', [
            'customer_id'     => $invoice_data['customer_id'],
            'customer_name'   => $invoice_data['customer_name'],
            'trn_date'        => $invoice_data['trn_date'],
            'due_date'        => $invoice_data['due_date'],
            'billing_address' => $invoice_data['billing_address'],
            'amount'          => $invoice_data['amount'],
            'discount'        => $invoice_data['discount'],
            'discount_type'   => $invoice_data['discount_type'],
            'shipping'        => $invoice_data['shipping'],
            'shipping_tax'    => $invoice_data['shipping_tax'],
            'tax'             => $invoice_data['tax'],
            'estimate'        => $invoice_data['estimate'],
            'attachments'     => $invoice_data['attachments'],
            'status'          => $invoice_data['status'],
            'particulars'     => $invoice_data['particulars'],
            'updated_at'      => $invoice_data['updated_at'],
            'updated_by'      => $invoice_data['updated_by'],
        ], ['voucher_no' => $invoice_no]);

        /*
     *? We can't update `invoice_details` directly
     *? suppose there were 5 detail rows previously
     *? but on update there may be 2 detail rows
     *? that's why we can't update because the foreach will iterate only 2 times, not 5 times
     *? so, remove previous rows to insert new rows
     */
        $wpdb->delete($wpdb->prefix . 'erp_acct_invoice_details', ['trn_no' => $invoice_no]);

        $this->insertInvoiceDetailsAndTax($invoice_data, $invoice_no);
    }

    /**
     * Get formatted invoice data
     *
     * @param $data
     * @param $voucher_no
     *
     * @return mixed
     */
    function getFormattedInvoiceData($data, $voucher_no)
    {
        $invoice_data = [];

        // We can pass the name from view... to reduce DB query load
        if (empty($data['customer_name'])) {
            $customer      = erp_get_people($data['customer_id']);
            $customer_name = $customer->first_name . ' ' . $customer->last_name;
        } else {
            $customer_name = $data['customer_name'];
        }

        $invoice_data['voucher_no']      = !empty($voucher_no) ? $voucher_no : 0;
        $invoice_data['customer_id']     = isset($data['customer_id']) ? $data['customer_id'] : null;
        $invoice_data['customer_name']   = $customer_name;
        $invoice_data['trn_date']        = isset($data['date']) ? $data['date'] : date('Y-m-d');
        $invoice_data['due_date']        = isset($data['due_date']) ? $data['due_date'] : date('Y-m-d');
        $invoice_data['billing_address'] = isset($data['billing_address']) ? maybe_serialize($data['billing_address']) : '';
        $invoice_data['amount']          = isset($data['amount']) ? $data['amount'] : 0;
        $invoice_data['discount']        = isset($data['discount']) ? $data['discount'] : 0;
        $invoice_data['discount_type']   = isset($data['discount_type']) ? $data['discount_type'] : null;
        $invoice_data['shipping']        = isset($data['shipping']) ? $data['shipping'] : 0;
        $invoice_data['shipping_tax']    = isset($data['shipping_tax']) ? $data['shipping_tax'] : 0;
        $invoice_data['tax_rate_id']     = isset($data['tax_rate_id']) ? $data['tax_rate_id'] : 0;
        $invoice_data['line_items']      = isset($data['line_items']) ? $data['line_items'] : [];
        $invoice_data['trn_by']          = isset($data['trn_by']) ? $data['trn_by'] : '';
        $invoice_data['tax']             = isset($data['tax']) ? $data['tax'] : 0;
        $invoice_data['attachments']     = !empty($data['attachments']) ? $data['attachments'] : '';
        $invoice_data['status']          = isset($data['status']) ? $data['status'] : 1;
        $invoice_data['particulars']     = !empty($data['particulars']) ? $data['particulars'] : sprintf(__('Invoice created with voucher no %s', 'erp'), $voucher_no);
        $invoice_data['estimate']        = isset($data['estimate']) ? $data['estimate'] : 0;
        $invoice_data['created_at']      = isset($data['created_at']) ? $data['created_at'] : null;
        $invoice_data['created_by']      = isset($data['created_by']) ? $data['created_by'] : null;
        $invoice_data['updated_at']      = isset($data['updated_at']) ? $data['updated_at'] : null;
        $invoice_data['updated_by']      = isset($data['updated_by']) ? $data['updated_by'] : null;

        $draft   = 1;
        $pending = 3;

        if (!empty($data['estimate']) && $data['status'] !== $draft) {
            $invoice_data['status'] = $pending;
        }

        return $invoice_data;
    }

    /**
     * Void an invoice
     *
     * @param $invoice_no
     *
     * @return void
     */
    function voidInvoice($invoice_no)
    {
        global $wpdb;

        if (!$invoice_no) {
            return;
        }

        $wpdb->update(
            $wpdb->prefix . 'erp_acct_invoices',
            [
                'status' => 8,
            ],
            ['voucher_no' => $invoice_no]
        );

        $wpdb->delete($wpdb->prefix . 'erp_acct_ledger_details', ['trn_no' => $invoice_no]);
        $wpdb->delete($wpdb->prefix . 'erp_acct_invoice_account_details', ['invoice_no' => $invoice_no]);

        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT
            inv_detail_tax.id
            FROM {$wpdb->prefix}erp_acct_invoice_details_tax as inv_detail_tax
            LEFT JOIN {$wpdb->prefix}erp_acct_invoice_details as inv_detail ON inv_detail_tax.invoice_details_id = inv_detail.id
            LEFT JOIN {$wpdb->prefix}erp_acct_invoices as invoice ON inv_detail.trn_no = invoice.voucher_no
            WHERE inv_detail.trn_no = %d",
                $invoice_no
            ),
            ARRAY_A
        );

        foreach ($results as $result) {
            $wpdb->delete($wpdb->prefix . 'erp_acct_invoice_details_tax', ['id' => $result['id']]);
        }

        $wpdb->delete($wpdb->prefix . 'erp_acct_tax_agency_details', ['trn_no' => $invoice_no]);

    }

    /**
     * Insert invoice/s data into ledger
     *
     * @param array $invoice_data
     *
     * @return mixed
     */
    function insertInvoiceDataIntoLedger($invoice_data, $voucher_no = 0, $contra = false)
    {
        global $wpdb;

        $user_id = get_current_user_id();
        $date    = date('Y-m-d H:i:s');

        $ledger_map = \WeDevs\ERP\Accounting\Includes\Classes\Ledger_Map::get_instance();

        $sales_ledger_id              = $ledger_map->get_ledger_id_by_slug('sales_revenue');
        $sales_discount_ledger_id     = $ledger_map->get_ledger_id_by_slug('sales_discount');
        $sales_shipping_ledger_id     = $ledger_map->get_ledger_id_by_slug('shipment');
        $sales_shipping_tax_ledger_id = $ledger_map->get_ledger_id_by_slug('shipment_tax');

        if ($contra) {
            $sales_credit        = 0;
            $discount_debit      = 0;
            $shipment_credit     = 0;
            $shipment_tax_credit = 0;

            $trn_no              = $voucher_no;
            $sales_debit         = $invoice_data['amount'];
            $discount_credit     = $invoice_data['discount'];
            $shipment_debit      = $invoice_data['shipping'];
            $shipment_tax_debit  = $invoice_data['shipping_tax'];
        } else {
            $sales_debit         = 0;
            $discount_credit     = 0;
            $shipment_debit      = 0;
            $shipment_tax_debit  = 0;

            $trn_no              = $invoice_data['voucher_no'];
            $sales_credit        = $invoice_data['amount'];
            $discount_debit      = $invoice_data['discount'];
            $shipment_credit     = $invoice_data['shipping'];
            $shipment_tax_credit = $invoice_data['shipping_tax'];
        }

        // insert amount in ledger_details
        $wpdb->insert(
            $wpdb->prefix . 'erp_acct_ledger_details',
            [
                'ledger_id'   => $sales_ledger_id,
                'trn_no'      => $trn_no,
                'particulars' => $invoice_data['particulars'],
                'debit'       => $sales_debit,
                'credit'      => $sales_credit,
                'trn_date'    => $invoice_data['trn_date'],
                'created_at'  => $date,
                'created_by'  => $user_id,
                'updated_at'  => $date,
                'updated_by'  => $user_id,
            ]
        );

        // insert discount in ledger_details
        if ((float) $discount_debit > 0 || (float) $discount_credit > 0) {
            $wpdb->insert(
                $wpdb->prefix . 'erp_acct_ledger_details',
                [
                    'ledger_id'   => $sales_discount_ledger_id,
                    'trn_no'      => $trn_no,
                    'particulars' => $invoice_data['particulars'],
                    'debit'       => $discount_debit,
                    'credit'      => $discount_credit,
                    'trn_date'    => $invoice_data['trn_date'],
                    'created_at'  => $date,
                    'created_by'  => $user_id,
                    'updated_at'  => $date,
                    'updated_by'  => $user_id,
                ]
            );
        }

        // insert shipping in ledger_details
        if ((float) $shipment_debit > 0 || (float) $shipment_credit > 0) {
            $wpdb->insert(
                $wpdb->prefix . 'erp_acct_ledger_details',
                [
                    'ledger_id'   => $sales_shipping_ledger_id,
                    'trn_no'      => $trn_no,
                    'particulars' => $invoice_data['particulars'],
                    'debit'       => $shipment_debit,
                    'credit'      => $shipment_credit,
                    'trn_date'    => $invoice_data['trn_date'],
                    'created_at'  => $date,
                    'created_by'  => $user_id,
                    'updated_at'  => $date,
                    'updated_by'  => $user_id,
                ]
            );
        }

        // insert shipping tax in ledger_details
        if ((float) $shipment_tax_debit > 0 || (float) $shipment_tax_credit > 0) {
            $wpdb->insert(
                $wpdb->prefix . 'erp_acct_ledger_details',
                [
                    'ledger_id'   => $sales_shipping_tax_ledger_id,
                    'trn_no'      => $trn_no,
                    'particulars' => $invoice_data['particulars'],
                    'debit'       => $shipment_tax_debit,
                    'credit'      => $shipment_tax_credit,
                    'trn_date'    => $invoice_data['trn_date'],
                    'created_at'  => $date,
                    'created_by'  => $user_id,
                    'updated_at'  => $date,
                    'updated_by'  => $user_id,
                ]
            );
        }

    }

    /**
     * Update invoice/s data into ledger
     *
     * @param array $invoice_data
     *
     * @return mixed
     */
    function updateInvoiceDataInLedger($invoice_data, $invoice_no)
    {
        global $wpdb;

        // Update amount in ledger_details
        $wpdb->update(
            $wpdb->prefix . 'erp_acct_ledger_details',
            [
                'particulars' => $invoice_data['particulars'],
                'credit'      => $invoice_data['amount'],
                'trn_date'    => $invoice_data['trn_date'],
                'updated_at'  => $invoice_data['updated_at'],
                'updated_by'  => $invoice_data['updated_by'],
            ],
            [
                'trn_no' => $invoice_no,
            ]
        );

        // Update discount in ledger_details
        $wpdb->update(
            $wpdb->prefix . 'erp_acct_ledger_details',
            [
                'particulars' => $invoice_data['particulars'],
                'debit'       => $invoice_data['discount'],
                'trn_date'    => $invoice_data['trn_date'],
                'updated_at'  => $invoice_data['updated_at'],
                'updated_by'  => $invoice_data['updated_by'],
            ],
            [
                'trn_no' => $invoice_no,
            ]
        );

    }

    /**
     * Get Invoice count
     *
     * @return int
     */
    function getInvoiceCount()
    {
        global $wpdb;

        $row = $wpdb->get_row('SELECT COUNT(*) as count FROM ' . $wpdb->prefix . 'erp_acct_invoices');

        return $row->count;
    }

    /**
     * Receive payments with due from a customer
     *
     * @return mixed
     */
    function receivePaymentsFromCustomer($args = [])
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

        if ('-1' === $args['number']) {
            $limit = "LIMIT {$args['number']} OFFSET {$args['offset']}";
        }

        $invoices            = "{$wpdb->prefix}erp_acct_invoices";
        $invoice_act_details = "{$wpdb->prefix}erp_acct_invoice_account_details";
        $items               = $args['count'] ? ' COUNT( id ) as total_number ' : ' id, voucher_no, due_date, (amount + tax - discount) as amount, invs.due as due ';

        $query = $wpdb->prepare(
            "SELECT $items FROM $invoices as invoice INNER JOIN
        (SELECT invoice_no, SUM( ia.debit - ia.credit) as due
        FROM $invoice_act_details as ia
        GROUP BY ia.invoice_no
        HAVING due <> 0) as invs
        ON invoice.voucher_no = invs.invoice_no
        WHERE invoice.customer_id = %d AND invoice.status != 1 AND invoice.estimate != 1
        ORDER BY %s %s $limit",
            $args['people_id'],
            $args['orderby'],
            $args['order']
        );

        if ($args['count']) {
            return $wpdb->get_var($query);
        }

        return $wpdb->get_results($query, ARRAY_A);
    }

    /**
     * Get due of a bill
     *
     * @param $bill_no
     *
     * @return int
     */
    function getDuePayment($invoice_no)
    {
        global $wpdb;

        $result = $wpdb->get_row($wpdb->prepare("SELECT invoice_no, SUM( ia.debit - ia.credit) as due FROM {$wpdb->prefix}erp_acct_invoice_account_details as ia WHERE ia.invoice_no = %d GROUP BY ia.invoice_no", $invoice_no), ARRAY_A);

        return $result['due'];
    }

    /**
     * Get recievables from given date
     *
     * @param $from String
     * @param $to   String
     *
     * @return array|object|null
     */
    function getRecievables($from, $to)
    {
        global $wpdb;

        $from_date = date('Y-m-d', strtotime($from));
        $to_date   = date('Y-m-d', strtotime($to));

        $invoices              = $wpdb->prefix . 'erp_acct_invoices';
        $invoices_acct_details = $wpdb->prefix . 'erp_acct_invoice_account_details';

        $query = $wpdb->prepare(
            "Select voucher_no, SUM(ad.debit - ad.credit) as due, due_date
        FROM $invoices LEFT JOIN $invoices_acct_details as ad
        ON ad.invoice_no = voucher_no  where due_date
        BETWEEN %s and %s Group BY voucher_no Having due > 0 ",
            $from_date,
            $to_date
        );

        $results = $wpdb->get_results($query, ARRAY_A);

        return $results;
    }

    /**
     * Get Dashboard Overview details
     */
    function getRecievablesOverview()
    {
        // get dates till coming 90 days
        $from_date = date('Y-m-d');
        $to_date   = date('Y-m-d', strtotime('+90 day', strtotime($from_date)));

        $data   = [];
        $amount = [
            'first'  => 0,
            'second' => 0,
            'third'  => 0,
        ];

        $result = $this->getRecievables($from_date, $to_date);

        if (!empty($result)) {
            $from_date = new DateTime($from_date);

            foreach ($result as $item_data) {
                $item  = (object) $item_data;
                $later = new DateTime($item->due_date);
                $diff  = $later->diff($from_date)->format('%a');

                //segment by date difference
                switch ($diff) {

                    case  $diff === 0:
                        $data['first'][] = $item_data;
                        $amount['first'] = $amount['first'] + $item->due;
                        break;

                    case  $diff <= 30:
                        $data['first'][] = $item_data;
                        $amount['first'] = $amount['first'] + $item->due;
                        break;

                    case  $diff <= 60:
                        $data['second'][] = $item_data;
                        $amount['second'] = $amount['second'] + $item->due;
                        break;

                    case  $diff <= 90:
                        $data['third'][] = $item_data;
                        $amount['third'] = $amount['third'] + $item->due;
                        break;

                    default:
                }
            }
        }

        return [
            'data'   => $data,
            'amount' => $amount,
        ];
    }

    /**
     * Get due of an invoice
     *
     * @param $invoice_no
     *
     * @return int
     */
    function getInvoiceDue($invoice_no)
    {
        global $wpdb;

        $result = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT ia.invoice_no, SUM( ia.debit - ia.credit) as due
            FROM {$wpdb->prefix}erp_acct_invoice_account_details as ia
            WHERE ia.invoice_no = %d
            GROUP BY ia.invoice_no",
                $invoice_no
            ),
            ARRAY_A
        );

        return $result['due'];
    }

    /**
     * Retrieves tax zone of an invoice
     *
     * @since 1.8.0
     *
     * @param [type] $invoice_no
     *
     * @return int|string
     */
    function getInvoiceTaxZone($invoice_no)
    {
        global $wpdb;

        $tax_zone = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT tax_zone_id FROM {$wpdb->prefix}erp_acct_invoices WHERE voucher_no = %d",
                [(int) $invoice_no]
            )
        );

        return $tax_zone;
    }
}
