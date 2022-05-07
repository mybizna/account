<?php

namespace Modules\Account\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AccountsController extends Controller
{

    /**
     * Get a collection of taxes
     *
     * @param WP_REST_Request $request
     *
     * @return WP_Error|WP_REST_Response
     */
    public function get_tax_cats($request)
    {
        $args = [
            'number'     => !empty($request['per_page']) ? (int) $request['per_page'] : 20,
            'offset'     => ($request['per_page'] * ($request['page'] - 1)),
            'start_date' => empty($request['start_date']) ? '' : $request['start_date'],
            'end_date'   => empty($request['end_date']) ? date('Y-m-d') : $request['end_date'],
        ];

        $formatted_items   = [];
        $additional_fields = [];

        $additional_fields['namespace'] = $this->namespace;
        $additional_fields['rest_base'] = $this->rest_base;

        $tax_data    = $this->getAllTaxCats($args);
        $total_items = $this->getAllTaxCats(
            [
                'count'  => true,
                'number' => -1,
            ]
        );

        foreach ($tax_data as $item) {
            if (isset($request['include'])) {
                $include_params = explode(',', str_replace(' ', '', $request['include']));

                if (in_array('created_by', $include_params, true)) {
                    $item['created_by'] = $this->get_user($item['created_by']);
                }
            }

            $data              = $this->prepare_item_for_response($item, $request, $additional_fields);
            $formatted_items[] = $this->prepare_response_for_collection($data);
        }

        $response = rest_ensure_response($formatted_items);
        $response = $this->format_collection_response($response, $request, $total_items);

        $response->set_status(200);

        return $response;
    }

    /**
     * Get an tax
     *
     * @param WP_REST_Request $request
     *
     * @return WP_Error|WP_REST_Response
     */
    public function get_tax_cat($request)
    {
        $id = (int) $request['id'];

        if (empty($id)) {
            return new WP_Error('rest_tax_invalid_id', __('Invalid resource id.'), ['status' => 404]);
        }

        $item = $taxcats->getTaxCat($id);

        $additional_fields['namespace'] = $this->namespace;
        $additional_fields['rest_base'] = $this->rest_base;

        $item     = $this->prepare_item_for_response($item, $request, $additional_fields);
        $response = rest_ensure_response($item);

        $response->set_status(200);

        return $response;
    }

    /**
     * Create an tax
     *
     * @param WP_REST_Request $request
     *
     * @return WP_Error|WP_REST_Response
     */
    public function create_tax_cat($request)
    {
        $tax_data = $this->prepare_item_for_database($request);

        $tax_id = $taxcats->insertTaxCat($tax_data);

        $tax_data['id'] = $tax_id;

        $this->add_log($tax_data, 'add');

        $additional_fields['namespace'] = $this->namespace;
        $additional_fields['rest_base'] = $this->rest_base;

        $tax_data = $this->prepare_item_for_response($tax_data, $request, $additional_fields);

        $response = rest_ensure_response($tax_data);
        $response->set_status(201);

        return $response;
    }

    /**
     * Update an tax
     *
     * @param WP_REST_Request $request
     *
     * @return WP_Error|WP_REST_Response
     */
    public function update_tax_cat($request)
    {
        $id = (int) $request['id'];

        if (empty($id)) {
            return new WP_Error('rest_tax_invalid_id', __('Invalid resource id.'), ['status' => 404]);
        }

        $tax_data = $this->prepare_item_for_database($request);

        $items = $request['tax_components'];

        foreach ($items as $key => $item) {
            $item_rates[$key] = $item['tax_rate'];
        }

        $tax_data['total_rate'] = array_sum($item_rates);

        $old_data = $taxcats->getTaxCat($id);
        $tax_id   = $taxcats->updateTaxCat($tax_data, $id);

        $this->add_log($tax_data, 'edit', $old_data);

        $tax_data['id']                 = $tax_id;
        $additional_fields['namespace'] = $this->namespace;
        $additional_fields['rest_base'] = $this->rest_base;

        $tax_data = $this->prepare_item_for_response($tax_data, $request, $additional_fields);

        $response = rest_ensure_response($tax_data);
        $response->set_status(201);

        return $response;
    }

    /**
     * Delete an tax
     *
     * @param WP_REST_Request $request
     *
     * @return WP_Error|WP_REST_Response
     */
    public function delete_tax_cat($request)
    {
        $id = (int) $request['id'];

        if (empty($id)) {
            return new WP_Error('rest_tax_invalid_id', __('Invalid resource id.'), ['status' => 404]);
        }

        $item = $taxcats->getTaxCat($id);

        $taxcats->deleteTaxCat($id);

        $this->add_log($item, 'delete');

        return new WP_REST_Response(true, 204);
    }

    /**
     * Bulk delete action
     *
     * @param object $request
     *
     * @return WP_Error|WP_REST_Response
     */
    public function bulk_delete($request)
    {
        $ids = $request['ids'];
        $ids = explode(',', $ids);

        if (!$ids) {
            return;
        }

        foreach ($ids as $id) {
            $item = $taxcats->getTaxCat($id);

            $taxcats->deleteTaxCat($id);

            $this->add_log($item, 'delete');
        }

        return new WP_REST_Response(true, 204);
    }

    /**
     * Log for tax category related actions
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
                'sub_component' => __('Tax', 'erp'),
                'old_value'     => isset($changes['old_value']) ? $changes['old_value'] : '',
                'new_value'     => isset($changes['new_value']) ? $changes['new_value'] : '',
                'message'       => '<strong>' . $data['name'] . '</strong>' . sprintf(__(' tax category has been %s', 'erp'), $operation),
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

        if (isset($request['name'])) {
            $prepared_item['name'] = $request['name'];
        }

        if (isset($request['description'])) {
            $prepared_item['description'] = $request['description'];
        }

        return $prepared_item;
    }

    /**
     * Prepare a single user output for response
     *
     * @param array           $item
     * @param WP_REST_Request $request           request object
     * @param array           $additional_fields (optional)
     *
     * @return WP_REST_Response $response response data
     */
    public function prepare_item_for_response($item, $request, $additional_fields = [])
    {
        $item = (object) $item;

        $data = [
            'id'          => (int) $item->id,
            'name'        => $item->name,
            'description' => !empty($item->description) ? $item->description : '',
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
            'title'      => 'tax_category',
            'type'       => 'object',
            'properties' => [
                'id'          => [
                    'description' => __('Unique identifier for the resource.'),
                    'type'        => 'integer',
                    'context'     => ['embed', 'view', 'edit'],
                    'readonly'    => true,
                ],
                'name'        => [
                    'description' => __('Tax Category name for the resource.'),
                    'type'        => 'string',
                    'context'     => ['edit'],
                    'arg_options' => [
                        'sanitize_callback' => 'sanitize_text_field',
                    ],
                ],
                'description' => [
                    'description' => __('Tax Category Description for the resource.'),
                    'type'        => 'string',
                    'context'     => ['edit'],
                    'arg_options' => [
                        'sanitize_callback' => 'sanitize_text_field',
                    ],
                ],
            ],
        ];

        return $schema;
    }
}
