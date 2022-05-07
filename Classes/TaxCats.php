<?php

namespace Modules\Account\Classes;

class Bank
{

    /**
     * Get all tax categories
     *
     * @return mixed
     */
    function getAllTaxCats($args = [])
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

        $last_changed = erp_cache_get_last_changed('accounting', 'tax_cats', 'erp-accounting');
        $cache_key    = 'erp-get-tax-cats-' . md5(serialize($args)) . ": $last_changed";
        $tax_cats     = wp_cache_get($cache_key, 'erp-accounting');

        $cache_key_count = 'erp-get-tax-cats-count-' . md5(serialize($args)) . ": $last_changed";
        $tax_cats_count  = wp_cache_get($cache_key_count, 'erp-accounting');

        if (false === $tax_cats) {
            $limit = '';

            if (-1 !== $args['number']) {
                $limit = "LIMIT {$args['number']} OFFSET {$args['offset']}";
            }

            $sql  = 'SELECT';
            $sql .= $args['count'] ? ' COUNT( id ) as total_number ' : ' * ';
            $sql .= "FROM {$wpdb->prefix}erp_acct_tax_categories ORDER BY {$args['orderby']} {$args['order']} {$limit}";

            if ($args['count']) {
                $tax_cats_count = $wpdb->get_var($sql);

                wp_cache_set($cache_key_count, $tax_cats_count, 'erp-accounting');
            } else {
                $tax_cats = $wpdb->get_results($sql, ARRAY_A);

                wp_cache_set($cache_key, $tax_cats, 'erp-accounting');
            }
        }

        if ($args['count']) {
            return $tax_cats_count;
        }

        return $tax_cats;
    }

    /**
     * Get an single tax category
     *
     * @param $tax_no
     *
     * @return mixed
     */
    function getTaxCat($tax_no)
    {
        global $wpdb;

        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}erp_acct_tax_categories WHERE id = %d LIMIT 1", $tax_no), ARRAY_A);

        return $row;
    }

    /**
     * Insert tax category data
     *
     * @param $data
     *
     * @return int
     */
    function insertTaxCat($data)
    {
        global $wpdb;

        $created_by         = get_current_user_id();
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['created_by'] = $created_by;

        $tax_data = $taxes->getFormattedTaxData($data);

        $wpdb->insert(
            $wpdb->prefix . 'erp_acct_tax_categories',
            [
                'name'        => $tax_data['name'],
                'description' => $tax_data['description'],
                'created_at'  => $tax_data['created_at'],
                'created_by'  => $tax_data['created_by'],
                'updated_at'  => $tax_data['updated_at'],
                'updated_by'  => $tax_data['updated_by'],
            ]
        );

        $tax_id = $wpdb->insert_id;


        return $tax_id;
    }

    /**
     * Update tax category
     *
     * @param $data
     *
     * @return int
     */
    function updateTaxCat($data, $id)
    {
        global $wpdb;

        $updated_by         = get_current_user_id();
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['updated_by'] = $updated_by;

        $tax_data = $taxes->getFormattedTaxData($data);

        $wpdb->update(
            $wpdb->prefix . 'erp_acct_tax_categories',
            [
                'name'        => $tax_data['name'],
                'description' => $tax_data['description'],
                'updated_at'  => $tax_data['updated_at'],
                'updated_by'  => $tax_data['updated_by'],
            ],
            [
                'id' => $id,
            ]
        );


        return $id;
    }

    /**
     * Delete a tax category
     *
     * @param $tax_no
     *
     * @return int
     */
    function deleteTaxCat($id)
    {
        global $wpdb;

        $wpdb->delete($wpdb->prefix . 'erp_acct_tax_categories', ['id' => $id]);


        return $id;
    }
}
