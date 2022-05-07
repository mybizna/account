<?php

namespace Modules\Account\Classes;

class Bank
{

    /**
     * Insert employee data as people
     *
     * @param $data
     * @param $update
     *
     * @return int
     */
    function addEmployeeAsPeople($data, $update = false)
    {
        global $wpdb;
        $people_id = null;

        if ($this->isEmployeePeople($data['user_id'])) {
            return;
        }

        $company = new \WeDevs\ERP\Company();

        if ($update) {
            $wpdb->update(
                $wpdb->prefix . 'erp_peoples',
                [
                    'first_name'    => $data['personal']['first_name'],
                    'last_name'     => $data['personal']['last_name'],
                    'company'       => $company->name,
                    'email'         => $data['user_email'],
                    'phone'         => $data['personal']['phone'],
                    'mobile'        => $data['personal']['mobile'],
                    'other'         => '',
                    'website'       => '',
                    'fax'           => '',
                    'notes'         => $data['personal']['description'],
                    'street_1'      => $data['personal']['street_1'],
                    'street_2'      => $data['personal']['street_2'],
                    'city'          => $data['personal']['city'],
                    'state'         => $data['personal']['state'],
                    'postal_code'   => $data['personal']['postal_code'],
                    'country'       => $data['personal']['country'],
                    'currency'      => '',
                    'life_stage'    => '',
                    'contact_owner' => '',
                    'hash'          => '',
                    'created_by'    => get_current_user_id(),
                    'created'       => '',
                ],
                [
                    'user_id' => $data['user_id'],
                ]
            );
        } else {
            $wpdb->insert(
                $wpdb->prefix . 'erp_peoples',
                [
                    'user_id'       => $data['user_id'],
                    'first_name'    => $data['personal']['first_name'],
                    'last_name'     => $data['personal']['last_name'],
                    'company'       => $company->name,
                    'email'         => $data['user_email'],
                    'phone'         => $data['personal']['phone'],
                    'mobile'        => $data['personal']['mobile'],
                    'other'         => '',
                    'website'       => '',
                    'fax'           => '',
                    'notes'         => $data['personal']['description'],
                    'street_1'      => $data['personal']['street_1'],
                    'street_2'      => $data['personal']['street_2'],
                    'city'          => $data['personal']['city'],
                    'state'         => $data['personal']['state'],
                    'postal_code'   => $data['personal']['postal_code'],
                    'country'       => $data['personal']['country'],
                    'currency'      => '',
                    'life_stage'    => '',
                    'contact_owner' => '',
                    'hash'          => '',
                    'created_by'    => get_current_user_id(),
                    'created'       => '',
                ]
            );

            $people_id = $wpdb->insert_id;
        }


        return $people_id;
    }

    /**
     * Inserts accounting people
     *
     * @since 1.8.5
     *
     * @param array $args
     *
     * @return mixed
     */
    function insertPeople($args)
    {
        $people = erp_get_people_by('email', $args['email']);

        // this $email belongs to nobody
        if (!$people) {
            return erp_insert_people($args);
        }

        foreach ($args as $key => $value) {
            if (empty($args[$key])) {
                unset($args[$key]);
            }
        }

        $args = wp_parse_args($args, (array) $people);
        $id   = erp_insert_people($args);

        if (!is_wp_error($id)) {
            global $wpdb;

            $type_id = $this->getPeopleTypeIdByName($args['type']);

            $wpdb->insert(
                "{$wpdb->prefix}erp_people_type_relations",
                ['people_id' => $id, 'people_types_id' => $type_id],
                ['%d', '%d']
            );
        }

        return $id;
    }

    /**
     * Get transaction by date
     *
     * @param int   $people_id
     * @param array $args
     *
     * @return array
     */
    function erp_people_filter_transaction($people_id, $args = [])
    {
        global $wpdb;
        $start_date = isset($args['start_date']) ? $args['start_date'] : '';
        $end_date   = isset($args['end_date']) ? $args['start_date'] : '';

        $rows = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}erp_acct_people_account_details WHERE trn_date >= '{$start_date}' AND trn_date <= '{$end_date}' AND people_id = {$people_id}", ARRAY_A);

        return $rows;
    }

    /**
     * Get address of a people
     *
     * @param $people_id
     *
     * @return mixed
     */
    function getPeopleAddress($people_id)
    {
        global $wpdb;

        $row = [];

        $row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT street_1, street_2, city, state, postal_code, country FROM {$wpdb->prefix}erp_peoples WHERE id = %d",
                $people_id
            ),
            ARRAY_A
        );

        return $row;
    }

    /**
     * Format people address
     */
    function formatPeopleAddress($address = [])
    {
        $add = '';

        $keys   = array_keys($address);
        $values = array_values($address);

        for ($idx = 0; $idx < count($address); $idx++) {
            $add .= $keys[$idx] . ': ' . $values[$idx] . '; ';
        }

        return $add;
    }

    /**
     * Get all transactions
     *
     * @return mixed
     */
    function getPeopleTransactions($args = [])
    {
        global $wpdb;

        $defaults = [
            'number' => 20,
            'offset' => 0,
            'order'  => 'ASC',
            'count'  => false,
            's'      => '',
        ];

        $args           = wp_parse_args($args, $defaults);
        $limit          = '';
        $where          = '';
        $fy_start_date  = !empty($args['start_date']) ? $args['start_date'] : erp_current_datetime()->format('Y-m-d');
        $financial_year = \WeDevs\ERP\Accounting\Includes\Classes\Common::closest_financial_year($fy_start_date);

        if (!empty($args['people_id'])) {
            $where .= " AND people.people_id = {$args['people_id']} ";
        }

        if (empty($args['start_date'])) {
            $args['start_date']  =   $financial_year['start_date'];
        }

        if (empty($args['end_date'])) {
            $args['end_date'] = date('Y-m-d', strtotime('last day of this month'));
        }


        if (!empty($args['start_date'])) {
            $where .= " AND people.trn_date BETWEEN '{$args['start_date']}' AND '{$args['end_date']}'";
        }

        if ('-1' === $args['number']) {
            $limit = "LIMIT {$args['number']} OFFSET {$args['offset']}";
        }

        $sql = 'SELECT';

        if ($args['count']) {
            $sql .= ' COUNT( DISTINCT people.voucher_no ) AS total_number';
        } else {
            $sql .= '
            voucher.id as voucher_no,
            people.people_id,
            people.voucher_no,
            people.trn_date,
            people.debit,
            people.credit,
            people.particulars,
            people.created_at';
        }

        $sql .= " FROM {$wpdb->prefix}erp_acct_voucher_no AS voucher
        INNER JOIN {$wpdb->prefix}erp_acct_people_trn_details AS people ON voucher.id = people.voucher_no
        {$where} ORDER BY people.trn_date {$args['order']} {$limit}";

        if ($args['count']) {
            $wpdb->get_results($sql);

            return $wpdb->num_rows;
        }


        $results = $wpdb->get_results($sql, ARRAY_A);

        $previous_balance_data  = [
            'start_date'        => $financial_year['start_date'],
            'end_date'          =>  date('Y-m-d', strtotime('-1 day', strtotime($args['start_date']))),
            'people_id'         => $args['people_id'],
            'financial_year_id' => $financial_year['id']
        ];

        $previousBalance     = $this->getPeoplePreviousBalance($previous_balance_data);  // get previous balance from financial year start date to previous date of start date



        /*    if ( $previousBalance > 0 ) {
        $dr_total = (float) $o_balance;
        $temp     = $o_balance . ' Dr';
    } else {
        $cr_total = (float) $o_balance;
        $temp     = $o_balance . ' Cr';
    }*/

        /*   */
        $total_debit  = 0;
        $total_credit  = 0;
        $balance =  (float)$previousBalance;

        foreach ($results as $index => $result) {

            $total_debit   += (float) $result['debit'];
            $total_credit  += (float) $result['credit'];

            $debit   = (float)$result['debit'] + $balance;
            $balance = $debit - (float)$result['credit'];

            $results[$index]['debit'] =  abs((float)$result['debit']);
            $results[$index]['credit'] =  abs((float)$result['credit']);
            $results[$index]['balance'] =  abs($balance) . ($balance > 0 ? __(' Dr', 'erp') : __(' Cr', 'erp'));
        }

        array_unshift(
            $results,
            [
                'voucher_no'  => null,
                'particulars' => 'Opening Balance',
                'people_id'   => null,
                'trn_no'      => null,
                'trn_date'    => null,
                'created_at'  => null,
                'debit'       => null,
                'credit'      => null,
                'balance'     => abs($previousBalance) . ($previousBalance > 0 ? ' Dr' : ' Cr'),
            ]
        );

        array_push(
            $results,
            [
                'voucher_no'  => null,
                'particulars' => 'Total',
                'people_id'   => null,
                'trn_no'      => null,
                'trn_date'    => null,
                'created_at'  => null,
                'debit'       => $total_debit,
                'credit'      => $total_credit,
                'balance'     => abs($balance) . ($balance > 0 ? ' Dr' : ' Cr'),
            ]
        );

        return $results;
    }


    /**
     * Get opening balance
     *
     * @param array $args
     *
     * @return mixed
     */
    function getPeoplePreviousBalance($args = [])
    {
        global $wpdb;

        $opening_balance_query     = $wpdb->prepare("SELECT SUM(debit - credit) AS opening_balance FROM {$wpdb->prefix}erp_acct_opening_balances where type = 'people' AND ledger_id = %d AND financial_year_id = %d", $args['people_id'], $args['financial_year_id']);
        $opening_balance_result    = $wpdb->get_row($opening_balance_query, ARRAY_A);
        $opening_balance           =  isset($opening_balance_result['opening_balance']) ? $opening_balance_result['opening_balance'] : 0;

        $people_transaction_query  =  $wpdb->prepare("SELECT SUM(debit - credit) AS balance FROM {$wpdb->prefix}erp_acct_people_trn_details where   people_id = %d AND trn_date BETWEEN %s AND %s", $args['people_id'], $args['start_date'], $args['end_date']);
        $people_transaction_result = $wpdb->get_row($people_transaction_query, ARRAY_A);
        $balance                   =  isset($people_transaction_result['balance']) ? $people_transaction_result['balance'] : 0;

        return ($balance + $opening_balance);
    }

    /**
     * Get People type by people id
     *
     * @param $people_id
     *
     * @return mixed
     */
    function getPeopleTypeById($people_id)
    {
        global $wpdb;

        $row = $wpdb->get_row($wpdb->prepare("SELECT people_types_id FROM {$wpdb->prefix}erp_people_type_relations WHERE people_id = %d LIMIT 1", $people_id));

        return $this->getPeopleTypeByTypeId($row->people_types_id);
    }

    /**
     * Get people type by type id
     *
     * @param $type_id
     *
     * @return mixed
     */
    function getPeopleTypeByTypeId($type_id)
    {
        global $wpdb;

        $row = $wpdb->get_row($wpdb->prepare("SELECT name FROM {$wpdb->prefix}erp_people_types WHERE id = %d LIMIT 1", $type_id));

        return $row->name;
    }

    /**
     * Get people type name by type id
     *
     * @since 1.8.5
     *
     * @param $type_name
     *
     * @return int|string
     */
    function getPeopleTypeIdByName($type_name)
    {
        global $wpdb;

        $row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT id
            FROM {$wpdb->prefix}erp_people_types
            WHERE name = %s LIMIT 1",
                $type_name
            )
        );

        return $row->id;
    }

    /**
     * Get people id by user id
     *
     * @return mixed
     */
    function getPeopleIdByUserId($user_id)
    {
        global $wpdb;

        $row = $wpdb->get_row($wpdb->prepare("SELECT id FROM {$wpdb->prefix}erp_peoples WHERE user_id = %d LIMIT 1", $user_id));

        return $row->id;
    }

    /**
     * Get people id by people_id
     *
     * @return mixed
     */
    function getPeopleNameByPeopleId($people_id)
    {
        global $wpdb;

        $row = $wpdb->get_row($wpdb->prepare("SELECT first_name, last_name FROM {$wpdb->prefix}erp_peoples WHERE id = %d LIMIT 1", $people_id));

        return $row->first_name . ' ' . $row->last_name;
    }

    /**
     * Checks if an employee is people
     *
     * @param $user_id
     *
     * @return bool
     */
    function isEmployeePeople($user_id)
    {
        global $wpdb;

        if (!$user_id) {
            return false;
        }

        $res = $wpdb->get_var($wpdb->prepare("SELECT COUNT(1) FROM {$wpdb->prefix}erp_peoples WHERE user_id = %d", $user_id));

        if ('1' === $res) {
            return true;
        }

        return false;
    }

    /**
     * Get $user_id by $people_id
     *
     * @param $people_id
     *
     * @return mixed
     */
    function getUserIdByPeopleId($people_id)
    {
        global $wpdb;

        $row = $wpdb->get_row($wpdb->prepare("SELECT user_id FROM {$wpdb->prefix}erp_peoples WHERE id = %d LIMIT 1", $people_id));

        return $row->user_id;
    }

    /**
     * Get Customer or Vendors
     */
    function getAccountingPeople($args = [])
    {
        global $wpdb;

        $defaults = [
            'type'       => 'all',
            'number'     => 20,
            'offset'     => 0,
            'orderby'    => 'id',
            'order'      => 'DESC',
            'trashed'    => false,
            'meta_query' => [],
            'count'      => false,
            'life_stage' => '',
            'include'    => [],
            'exclude'    => [],
            's'          => '',
            'no_object'  => false,
        ];
        $args     = wp_parse_args($args, $defaults);

        $people_type = is_array($args['type']) ? implode('-', $args['type']) : $args['type'];

        $last_changed = erp_cache_get_last_changed('accounting', 'people', 'erp');
        $cache_key    = 'erp-accounting-people-' . $people_type . '-' . md5(serialize($args)) . ":$last_changed";
        $items        = wp_cache_get($cache_key, 'erp');

        $pep_tb      = $wpdb->prefix . 'erp_peoples';
        $pepmeta_tb  = $wpdb->prefix . 'erp_peoplemeta';
        $types_tb    = $wpdb->prefix . 'erp_people_types';
        $type_rel_tb = $wpdb->prefix . 'erp_people_type_relations';

        if (false === $items) {
            extract($args);

            $sql         = [];
            $trashed_sql = $trashed ? '`deleted_at` is not null' : '`deleted_at` is null';

            if (is_array($type)) {
                $type_sql = "and `name` IN ( '" . implode("','", $type) . "' )";
            } else {
                $type_sql = ('all' !== $type) ? "and `name` = '" . $type . "'" : '';
            }

            $wrapper_select = 'SELECT people.*, ';

            $sql['select'][] = "GROUP_CONCAT( DISTINCT t.name SEPARATOR ',') AS types";
            $sql['join'][]   = "LEFT JOIN $type_rel_tb AS r ON people.id = r.people_id LEFT JOIN $types_tb AS t ON r.people_types_id = t.id";
            $sql_from_tb     = "FROM $pep_tb AS people";
            $sql_people_type = "where ( select count(*) from $types_tb
            inner join  $type_rel_tb
                on $types_tb.`id` = $type_rel_tb.`people_types_id`
                where $type_rel_tb.`people_id` = people.`id` $type_sql and $trashed_sql
          ) >= 1";
            $sql['where']    = [''];

            $sql_group_by = 'GROUP BY `people`.`id`';
            $sql_order_by = "ORDER BY $orderby $order";

            // Check if want all data without any pagination
            $sql_limit = ('-1' !== $number && !$count) ? "LIMIT $number OFFSET $offset" : '';

            if ($meta_query) {
                $sql['join'][] = "LEFT JOIN $pepmeta_tb as people_meta on people.id = people_meta.`erp_people_id`";

                $meta_key   = isset($meta_query['meta_key']) ? $meta_query['meta_key'] : '';
                $meta_value = isset($meta_query['meta_value']) ? $meta_query['meta_value'] : '';
                $compare    = isset($meta_query['compare']) ? $meta_query['compare'] : '=';

                $sql['where'][] = "AND people_meta.meta_key='$meta_key' and people_meta.meta_value='$meta_value'";
            }

            // Check if the row want to search
            if (!empty($s)) {
                $search_like = '%' . $wpdb->esc_like($s) . '%';
                $words       = explode(' ', $s);

                if ($type == 'customer' || $type == 'vendor') {
                    if (defined('DOING_AJAX') && DOING_AJAX) {
                        if ($type === 'customer') {
                            $sql['where'][] = $wpdb->prepare(
                                'AND ( people.first_name ) LIKE %s OR ' .
                                    '( people.last_name ) LIKE %s',
                                [$search_like, $search_like]
                            );
                        } else {
                            $sql['where'][] = $wpdb->prepare('AND ( people.company ) LIKE %s', [$search_like]);
                        }
                    } else {
                        $sql['where'][] = $wpdb->prepare(
                            'AND ( people.first_name ) LIKE %s OR ' .
                                '( people.last_name ) LIKE %s OR ' .
                                '( people.email ) LIKE %s OR ' .
                                '( people.company ) LIKE %s',
                            [$search_like, $search_like, $search_like, $search_like]
                        );
                    }
                } elseif (is_array($type)) {
                    $sql['where'][] = $wpdb->prepare(
                        'AND ( people.first_name ) LIKE %s OR ' .
                            '( people.last_name ) LIKE %s',
                        [$search_like, $search_like]
                    );
                }
            }

            // Check if args count true, then return total count customer according to above filter
            if ($count) {
                $sql_order_by   = '';
                $sql_group_by   = '';
                $wrapper_select = 'SELECT COUNT( DISTINCT people.id ) as total_number';
                unset($sql['select'][0]);
            }

            $sql = apply_filters('erp_get_people_pre_query', $sql, $args);

            $post_where_queries = '';

            if (!empty($sql['post_where_queries'])) {
                $post_where_queries = 'AND ( 1 = 1 '
                    . implode(' ', $sql['post_where_queries'])
                    . ' )';
            }

            $final_query = $wrapper_select . ' '
                . implode(' ', $sql['select']) . ' '
                . $sql_from_tb . ' '
                . implode(' ', $sql['join']) . ' '
                . $sql_people_type . ' '
                . 'AND ( 1=1 '
                . implode(' ', $sql['where']) . ' '
                . ' )'
                . $post_where_queries
                . $sql_group_by . ' '
                . $sql_order_by . ' '
                . $sql_limit;

            if ($count) {
                // Only filtered total count of people
                $items = $wpdb->get_var(apply_filters('erp_get_people_total_count_query', $final_query, $args));
            } else {
                // Fetch results from people table
                $results = $wpdb->get_results(apply_filters('erp_get_people_total_query', $final_query, $args), ARRAY_A);
                array_walk(
                    $results,
                    function (&$results) {
                        $results['types'] = explode(',', $results['types']);
                    }
                );

                $items = ($no_object) ? $results : erp_array_to_object($results);
            }
            wp_cache_set($cache_key, $items, 'erp');
        }

        return $items;
    }

    /**
     * Check if transaction associated with this people
     *
     * @param int $id
     */
    function checkAssociatedTranasaction($people_id)
    {
        global $wpdb;

        return $wpdb->get_var(
            $wpdb->prepare(
                "SELECT id FROM {$wpdb->prefix}erp_acct_people_trn_details WHERE people_id = %d",
                $people_id
            )
        );
    }
}
