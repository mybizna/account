<?php

namespace Modules\Account\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;use Modules\Account\Classes\CommonFunc;


class TaxRateNamesController extends Controller
{

    /**
     * Get a collection of taxes
     *
     * @param WP_REST_Request $request
     *
     * @return WP_Error|WP_REST_Response
     */
    public function get_tax_rate_names($request)
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

        $tax_data    = $taxratenames->getAllTaxRateNames($args);
        $total_items = $taxratenames->getAllTaxRateNames(
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
    public function get_tax_rate_name($request)
    {
        $id = (int) $request['id'];

        if (empty($id)) {
            return new WP_Error('rest_tax_invalid_id', __('Invalid resource id.'), ['status' => 404]);
        }

        $item = $taxratenames->getTaxRateName($id);

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
    public function create_tax_rate_name($request)
    {
        $tax_data = $this->prepare_item_for_database($request);

        $tax_id = $taxratenames->insertTaxRateName($tax_data);

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
    public function update_tax_rate_name($request)
    {
        $id = (int) $request['id'];

        if (empty($id)) {
            return new WP_Error('rest_tax_invalid_id', __('Invalid resource id.'), ['status' => 404]);
        }

        $tax_data = $this->prepare_item_for_database($request);
        $old_data = $taxratenames->getTaxRateName($id);
        $tax_id   = $taxratenames->updateTaxRateName($tax_data, $id);

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
    public function delete_tax_rate_name($request)
    {
        $id = (int) $request['id'];

        if (empty($id)) {
            return new WP_Error('rest_tax_invalid_id', __('Invalid resource id.'), ['status' => 404]);
        }

        $item = $taxratenames->getTaxRateName($id);

        $taxratenames->deleteTaxRateName($id);

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
            $item = $taxratenames->getTaxRateName($id);

            $taxratenames->deleteTaxRateName($id);

            $this->add_log($item, 'delete');
        }

        return new WP_REST_Response(true, 204);
    }

    /**
     * Log for tax zone related actions
     *
     * @param array $data
     * @param string $action
     * @param array $old_data
     *
     * @return void
     */
    public function add_log($data, $action, $old_data = [])
    {
        $common = new CommonFunc();
        switch ($action) {
            case 'edit':
                $operation = 'updated';
                $changes   = !empty($old_data) ?$common->getArrayDiff($data, $old_data) : [];
                break;
            case 'delete':
                $operation = 'deleted';
                break;
            default:
                $operation = 'created';
        }

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

        if (isset($request['tax_rate_name'])) {
            $prepared_item['tax_rate_name'] = $request['tax_rate_name'];
        }

        if (isset($request['tax_number'])) {
            $prepared_item['tax_number'] = $request['tax_number'];
        }

        if (isset($request['default'])) {
            $prepared_item['default'] = $request['default'];
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
        $data = array_merge($item, $additional_fields);

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
            'title'      => 'tax',
            'type'       => 'object',
            'properties' => [
                'id'   => [
                    'description' => __('Unique identifier for the resource.'),
                    'type'        => 'integer',
                    'context'     => ['embed', 'view', 'edit'],
                    'readonly'    => true,
                ],
                'tax_rate_name' => [
                    'description' => __('Tax rate name for the resource.'),
                    'type'        => 'string',
                    'context'     => ['view', 'edit'],
                    'arg_options' => [
                        'sanitize_callback' => 'sanitize_text_field',
                    ],
                ],
                'tax_number' => [
                    'description' => __('Tax number for the resource.'),
                    'type'        => 'string',
                    'context'     => ['view', 'edit'],
                    'arg_options' => [
                        'sanitize_callback' => 'sanitize_text_field',
                    ],
                ],
                'default' => [
                    'description' => __('Tax default value for the resource.'),
                    'type'        => ['integer', 'string', 'boolean'],
                    'context'     => ['view', 'edit'],
                ],
            ],
        ];

        return $schema;
    }
}
