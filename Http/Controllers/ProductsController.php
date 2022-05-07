<?php

namespace Modules\Account\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AccountsController extends Controller
{
    /**
     * Get a collection of inventory_products
     *
     * @param WP_REST_Request $request
     *
     * @return WP_Error|WP_REST_Response
     */
    public function get_inventory_products($request)
    {
        $args = [
            'number' => !empty($request['number']) ? (int) $request['number'] : 20,
            'offset' => ($request['per_page'] * ($request['page'] - 1)),
            's'      => !empty($request['s']) ? $request['s'] : ''
        ];

        $formatted_items   = [];
        $additional_fields = [];

        $additional_fields['namespace'] = $this->namespace;
        $additional_fields['rest_base'] = $this->rest_base;

        $product_data = $products->getAllProducts($args);
        $total_items  = $products->getAllProducts(
            [
                'count'  => true,
                'number' => -1,
            ]
        );

        foreach ($product_data as $item) {
            $data              = $this->prepare_item_for_response($item, $request, $additional_fields);
            $formatted_items[] = $this->prepare_response_for_collection($data);
        }

        $response = rest_ensure_response($formatted_items);
        $response = $this->format_collection_response($response, $request, $total_items);
        $response->set_status(200);

        return $response;
    }

    /**
     * Get a specific inventory product
     *
     * @param WP_REST_Request $request
     *
     * @return WP_Error|WP_REST_Response
     */
    public function get_inventory_product($request)
    {
        $id   = (int) $request['id'];
        $item = $products->getAllProducts($id);

        if (empty($id)) {
            return new WP_Error('rest_inventory_product_invalid_id', __('Invalid resource id.'), ['status' => 404]);
        }

        $additional_fields['namespace'] = $this->namespace;
        $additional_fields['rest_base'] = $this->rest_base;
        $item                           = $this->prepare_item_for_response($item, $request, $additional_fields);
        $response                       = rest_ensure_response($item);

        $response->set_status(200);

        return $response;
    }

    /**
     * Create an inventory product
     *
     * @param WP_REST_Request $request
     *
     * @return WP_Error|WP_REST_Request
     */
    public function create_inventory_product($request)
    {
        $item  = $this->prepare_item_for_database($request);

        $id    = $products->insertProduct($item);

        if (is_wp_error($id)) {
            return $id;
        }

        $item['id'] = $id;

        $this->add_log($item, 'add');

        $additional_fields['namespace'] = $this->namespace;
        $additional_fields['rest_base'] = $this->rest_base;

        $response = $this->prepare_item_for_response($item, $request, $additional_fields);
        $response = rest_ensure_response($response);
        $response->set_status(201);

        return $response;
    }

    /**
     * Update an inventory product
     *
     * @param WP_REST_Request $request
     *
     * @return WP_Error|WP_REST_Request
     */
    public function update_inventory_product($request)
    {
        $id = (int) $request['id'];

        if (empty($id)) {
            return new WP_Error('rest_payment_invalid_id', __('Invalid resource id.'), ['status' => 404]);
        }

        $item = $this->prepare_item_for_database($request);

        $old_data = $products->getAllProducts($id);

        $id = $products->updateProduct($item, $id);

        if (is_wp_error($id)) {
            return $id;
        }

        $this->add_log($item, 'edit', $old_data);

        $item['id'] = $id;

        $additional_fields['namespace'] = $this->namespace;
        $additional_fields['rest_base'] = $this->rest_base;

        $response = $this->prepare_item_for_response($item, $request, $additional_fields);
        $response = rest_ensure_response($response);
        $response->set_status(200);

        return $response;
    }

    /**
     * Delete an inventory product
     *
     * @param WP_REST_Request $request
     *
     * @return WP_Error|WP_REST_Request
     */
    public function delete_inventory_product($request)
    {
        $id = (int) $request['id'];

        $item = $products->getAllProducts($id);

        $products->deleteProduct($id);

        $this->add_log($item, 'delete');

        return new WP_REST_Response(true, 204);
    }

    /**
     * Validates csv file data for products
     *
     * @param WP_REST_Request $request
     *
     * @return WP_Error|WP_REST_Response
     */
    public function validate_csv_data($request)
    {
        $args = [
            'csv_file'        => !empty($_FILES['csv_file'])         ? $_FILES['csv_file']         : '',
            'type'            => !empty($request['type'])            ? $request['type']            : '',
            'category_id'     => !empty($request['category_id'])     ? $request['category_id']     : '',
            'product_type_id' => !empty($request['product_type_id']) ? $request['product_type_id'] : '',
            'tax_cat_id'      => !empty($request['tax_cat_id'])      ? $request['tax_cat_id']      : '',
            'vendor'          => !empty($request['vendor'])          ? $request['vendor']          : '',
            'update_existing' => !empty($request['update_existing']) ? $request['update_existing'] : '',
            'fields'          => !empty($request['fields'])          ? $request['fields']          : '',
        ];

        $data = $products->validateCsvData($args);

        if (is_wp_error($data)) {
            return $data;
        }

        $response = rest_ensure_response($data);
        $response->set_status(200);

        return $response;
    }

    /**
     * Import products from csv
     *
     * @param WP_REST_Request $request
     *
     * @return WP_Error|WP_REST_Response
     */
    public function import_products($request)
    {
        $args = [
            'items'  => !empty($request['items'])  ? $request['items']   : '',
            'update' => !empty($request['update']) ? $request['update']  : '',
            'total'  => !empty($request['total'])  ? $request['total']   : '',
        ];

        $imported = $products->importProducts($args);

        if (is_wp_error($imported)) {
            return $imported;
        }

        $response = rest_ensure_response($imported);
        $response->set_status(201);

        return $response;
    }

    /**
     * Log for inventory product related actions
     *
     * @param array $data
     * @param string $action
     * @param array $old_data
     *
     * @return void
     */
    public function add_log($data, $action, $old_data = [])
    {
        switch ($action) {
            case 'edit':
                $operation = 'updated';
                $changes   = !empty($old_data) ? erp_get_array_diff($data, $old_data) : [];
                break;
            case 'delete':
                $operation = 'deleted';
                break;
            default:
                $operation = 'created';
        }

        erp_log()->add(
            [
                'component'     => 'Accounting',
                'sub_component' => __('Product', 'erp'),
                'old_value'     => isset($changes['old_value']) ? $changes['old_value'] : '',
                'new_value'     => isset($changes['new_value']) ? $changes['new_value'] : '',
                'message'       => sprintf(__('<strong>%1$s</strong> product has been %2$s', 'erp'), $data['name'], $operation),
                'changetype'    => $action,
                'created_by'    => get_current_user_id(),
            ]
        );
    }

    /**
     * Prepare a single item for create or update
     *
     * @param WP_REST_Request $request request object
     *
     * @return array $prepared_item
     */
    protected function prepare_item_for_database($request)
    {
        $prepared_item = [];
        // required arguments.
        if (isset($request['name'])) {
            $prepared_item['name'] = $request['name'];
        }

        if (isset($request['product_type_id'])) {
            $prepared_item['product_type_id'] = $request['product_type_id']['id'];
        }

        if (isset($request['category_id'])) {
            $prepared_item['category_id'] = $request['category_id']['id'];
        }

        if (isset($request['tax_cat_id'])) {
            $prepared_item['tax_cat_id'] = $request['tax_cat_id']['id'];
        }

        if (isset($request['vendor'])) {
            $prepared_item['vendor'] = $request['vendor']['id'];
        }

        if (isset($request['cost_price'])) {
            $prepared_item['cost_price'] = $request['cost_price'];
        }

        if (isset($request['sale_price'])) {
            $prepared_item['sale_price'] = $request['sale_price'];
        }

        return $prepared_item;
    }

    /**
     * Prepare a single user output for response
     *
     * @param array|object    $item
     * @param WP_REST_Request $request           request object
     * @param array           $additional_fields (optional)
     *
     * @return WP_REST_Response $response response data
     */
    public function prepare_item_for_response($item, $request, $additional_fields = [])
    {
        $item = (object) $item;

        $data = [
            'id'                => $item->id,
            'name'              => $item->name,
            'product_type_id'   => $item->product_type_id,
            'product_type_name' => !empty($item->product_type_name) ? $item->product_type_name : '',
            'category_id'       => $item->category_id,
            'tax_cat_id'        => $item->tax_cat_id,
            'vendor'            => !empty($item->vendor) ? $item->vendor : '',
            'cost_price'        => $item->cost_price,
            'sale_price'        => $item->sale_price,
            'vendor_name'       => !empty($item->vendor_name) ? $item->vendor_name : '',
            'cat_name'          => !empty($item->cat_name) ? $item->cat_name : '',
            'tax_cat_name'      => erp_acct_get_tax_category_by_id($item->tax_cat_id),
        ];

        $data = array_merge($data, $additional_fields);

        // Wrap the data in a response object
        $response = rest_ensure_response($data);

        $response = $this->add_links($response, $item, $additional_fields);

        return $response;
    }

    /**
     * Get the User's schema, conforming to JSON Schema
     *
     * @return array
     */
    public function get_item_schema()
    {
        $schema = [
            '$schema'    => 'http://json-schema.org/draft-04/schema#',
            'title'      => 'erp_inv_product',
            'type'       => 'object',
            'properties' => [
                'id'              => [
                    'description' => __('Unique identifier for the resource.'),
                    'type'        => 'integer',
                    'context'     => ['embed', 'view', 'edit'],
                    'readonly'    => true,
                ],
                'name'            => [
                    'description' => __('Name for the resource.'),
                    'type'        => 'string',
                    'context'     => ['edit'],
                    'arg_options' => [
                        'sanitize_callback' => 'sanitize_text_field',
                    ],
                    'required'    => true,
                ],
                'product_type_id'    => [
                    'description' => __('State for the resource.', 'erp'),
                    'type'        => 'object',
                    'context'     => ['view', 'edit'],
                    'properties'  => [
                        'id'   => [
                            'description' => __('Unique identifier for the resource.', 'erp'),
                            'type'        => 'integer',
                            'context'     => ['view', 'edit'],
                            'required'    => true,
                        ],
                        'name' => [
                            'description' => __('Type name for the resource.', 'erp'),
                            'type'        => 'string',
                            'context'     => ['view', 'edit'],
                            'arg_options' => [
                                'sanitize_callback' => 'sanitize_text_field',
                            ],
                        ],
                    ],
                ],
                'category_id'    => [
                    'description' => __('Category id for the resource.', 'erp'),
                    'type'        => 'object',
                    'context'     => ['view', 'edit'],
                    'properties'  => [
                        'id'   => [
                            'description' => __('Unique identifier for the resource.', 'erp'),
                            'type'        => 'integer',
                            'context'     => ['view', 'edit'],
                        ],
                        'name' => [
                            'description' => __('Type name for the resource.', 'erp'),
                            'type'        => 'string',
                            'context'     => ['view', 'edit'],
                        ],
                        'parent' => [
                            'description' => __('Parent category for the resource.', 'erp'),
                            'type'        => 'integer',
                            'context'     => ['view', 'edit'],
                        ],
                    ],
                ],
                'tax_cat_id'    => [
                    'description' => __('Tax category id for the resource.', 'erp'),
                    'type'        => 'object',
                    'context'     => ['view', 'edit'],
                    'properties'  => [
                        'id'   => [
                            'description' => __('Unique identifier for the resource.', 'erp'),
                            'type'        => 'integer',
                            'context'     => ['view', 'edit'],
                        ],
                        'name' => [
                            'description' => __('Tax category name for the resource.', 'erp'),
                            'type'        => 'string',
                            'context'     => ['view', 'edit'],
                        ],
                        'description' => [
                            'description' => __('Description for the resource.', 'erp'),
                            'type'        => 'integer',
                            'context'     => ['view', 'edit'],
                        ],
                    ],
                ],
                'vendor'    => [
                    'description' => __('Vendor for the resource.', 'erp'),
                    'type'        => 'object',
                    'context'     => ['view', 'edit'],
                    'properties'  => [
                        'id'   => [
                            'description' => __('Unique identifier for the resource.', 'erp'),
                            'type'        => 'integer',
                            'context'     => ['view', 'edit'],
                            'required'    => false,
                        ],
                        'name' => [
                            'description' => __('Name for the resource.', 'erp'),
                            'type'        => 'string',
                            'context'     => ['view', 'edit'],
                            'arg_options' => [
                                'sanitize_callback' => 'sanitize_text_field',
                            ],
                        ],
                    ],
                ],
                'cost_price'      => [
                    'description' => __('Cost price for the resource.'),
                    'type'        => 'number',
                    'context'     => ['embed', 'view', 'edit'],
                    'readonly'    => true,
                ],
                'sale_price'      => [
                    'description' => __('Sale price for the resource.'),
                    'type'        => 'number',
                    'context'     => ['embed', 'view', 'edit'],
                    'readonly'    => true,
                ],
            ],
        ];

        return $schema;
    }

    /**
     * Get product type
     *
     * @return object
     */
    public function get_product_types()
    {
        $types    = $products->getProductTypes();
        $response = rest_ensure_response($types);

        return $response;
    }

    /**
     * Bulk delete action
     *
     * @param object $request
     *
     * @return object
     */
    public function bulk_delete($request)
    {
        $ids = $request['ids'];
        $ids = explode(',', $ids);

        if (!$ids) {
            return;
        }

        foreach ($ids as $id) {
            $products->deleteProduct($id);
        }

        return new WP_REST_Response(true, 204);
    }
}
