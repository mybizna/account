<?php

namespace Modules\Account\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AccountsController extends Controller
{
    /**
     * Get all the ledgers of a particular chart_id
     *
     * @param WP_REST_Request $request
     *
     * @return WP_Error|WP_REST_Response
     */
    public function get_all_ledger_accounts($request)
    {
        $formatted_items   = [];
        $additional_fields = [];

        $additional_fields['namespace'] = $this->namespace;
        $additional_fields['rest_base'] = $this->rest_base;

        $ledgers = $this->getLedgersWithBalances();

        foreach ($ledgers as $ledger) {
            $data              = $this->prepare_ledger_for_response($ledger, $request, $additional_fields);
            $formatted_items[] = $this->prepare_response_for_collection($data);
        }

        $response = rest_ensure_response($formatted_items);
        $response = $this->format_collection_response($response, $request, count($ledgers));

        $response->set_status(200);

        return $response;
    }

    /**
     * Get all the ledgers of a particular chart_id
     *
     * @param WP_REST_Request $request
     *
     * @return WP_Error|WP_REST_Response
     */
    public function get_ledger_accounts_by_chart($request)
    {
        $id = $request['chart_id'];

        if (empty($id)) {
            return new WP_Error('rest_empty_chart_id', __('Chart ID is Empty.'), ['status' => 400]);
        }

        $items = $ledger->getLedgersByChartId($id);

        $response = rest_ensure_response($items);
        $response->set_status(200);

        return $response;
    }

    /**
     * Get a specific ledger
     *
     * @param WP_REST_Request $request
     *
     * @return WP_Error|WP_REST_Response
     */
    public function get_ledger_account($request)
    {
        global $wpdb;
        $items = [];

        $id = (int) $request['id'];

        if (empty($id)) {
            return new WP_Error('rest_ledger_invalid_id', __('Invalid resource id.'), ['status' => 404]);
        }

        $item = $ledger->getLedger($id);

        $result   = $this->prepare_item_for_response($item, $request);
        $response = rest_ensure_response($item);

        return $response;
    }

    /**
     * Create an ledger
     *
     * @param WP_REST_Request $request
     *
     * @return WP_Error|WP_REST_Request
     */
    public function create_ledger_account($request)
    {
        global $wpdb;

        $exist = $wpdb->get_var($wpdb->prepare("SELECT name FROM {$wpdb->prefix}erp_acct_ledgers WHERE name = %s", $request['name']));

        if ($exist) {
            return new WP_Error('rest_ledger_name_already_exist', __('Name already exist.'), ['status' => 404]);
        }

        $item = $this->prepare_item_for_database($request);

        $result = erp_acct_insert_ledger($item);

        $this->add_log((array) $result, 'add');

        $request->set_param('context', 'edit');
        $response = $this->prepare_item_for_response($result, $request);
        $response = rest_ensure_response($response);
        $response->set_status(200);
        //$response->header( 'Location', rest_url( sprintf( '/%s/%s/%d', $this->namespace, $this->rest_base, $result['id'] ) ) );

        return $response;
    }

    /**
     * Update an ledger
     *
     * @param WP_REST_Request $request
     *
     * @return WP_Error|WP_REST_Request
     */
    public function update_ledger_account($request)
    {
        global $wpdb;

        $id = (int) $request['id'];

        if (empty($id)) {
            return new WP_Error('rest_ledger_invalid_id', __('Invalid resource id.'), ['status' => 404]);
        }

        $item = $this->prepare_item_for_database($request);

        $old_data = $ledger->getLedger($id);

        $result = $ledger->updateLedger($item, $id);

        $this->add_log($item, 'edit', $old_data);

        $request->set_param('context', 'edit');
        $response = $this->prepare_item_for_response($result, $request);
        $response = rest_ensure_response($response);
        $response->set_status(201);
        $response->header('Location', rest_url(sprintf('/%s/%s/%d', $this->namespace, $this->rest_base, $id)));

        return $response;
    }

    /**
     * Delete an account
     *
     * @param WP_REST_Request $request
     *
     * @return WP_Error|WP_REST_Request
     */
    public function delete_ledger_account($request)
    {
        global $wpdb;

        $id = (int) $request['id'];

        if (empty($id)) {
            return new WP_Error('rest_ledger_invalid_id', __('Invalid resource id.'), ['status' => 404]);
        }

        $item = erp_acct_get_ledger($id);

        $wpdb->delete("{$wpdb->prefix}erp_acct_ledgers", ['id' => $id]);

        $this->add_log($item, 'delete');

        return new WP_REST_Response(true, 204);
    }

    /**
     * Get chart of accounts
     *
     * @param WP_REST_REQUEST $request
     *
     * @return WP_ERROR|WP_REST_REQUEST
     */
    public function get_chart_accounts($request)
    {
        $accounts = erp_acct_get_all_charts();

        $response = rest_ensure_response($accounts);

        $response->set_status(200);

        return $response;
    }

    /**
     * Get a collection of bank accounts
     *
     * @param WP_REST_Request $request
     *
     * @return WP_Error|WP_REST_Response
     */
    public function get_bank_accounts($request)
    {
        $items = $bank->getBanks(true, false, false);

        if (empty($items)) {
            return new WP_Error('rest_empty_accounts', __('Bank accounts are empty.'), ['status' => 204]);
        }

        $formatted_items = [];

        foreach ($items as $item) {
            $additional_fields = [];

            $data              = $this->prepare_bank_item_for_response($item, $request, $additional_fields);
            $formatted_items[] = $this->prepare_response_for_collection($data);
        }

        $response = rest_ensure_response($formatted_items);
        $response = $this->format_collection_response($response, $request, 0);

        return $response;
    }

    /**
     * Get cash accounts
     *
     * @param WP_REST_Request $request
     *
     * @return WP_Error|WP_REST_Response
     */
    public function get_cash_accounts($request)
    {
        $args               = [];
        $args['start_date'] = date('Y-m-d');

        $closest_fy_date    = $trialbal->getClosestFnYearDate($args['start_date']);
        $args['start_date'] = $closest_fy_date['start_date'];
        $args['end_date']   = $closest_fy_date['end_date'];

        $ledger_map = \WeDevs\ERP\Accounting\Includes\Classes\Ledger_Map::get_instance();
        $ledger_id  = $ledger_map->get_ledger_id_by_slug('cash');

        $c_balance = get_ledger_balance_with_opening_balance($ledger_id, $args['start_date'], $args['end_date']);

        $item[]            = [
            'id'           => $ledger_id,
            'name'         => 'Cash',
            'obalance'     => $c_balance['obalance'],
            'balance'      => $c_balance['balance'],
            'total_debit'  => $c_balance['total_debit'],
            'total_credit' => $c_balance['total_credit'],
        ];
        $additional_fields = [];
        $data              = $this->prepare_bank_item_for_response($item, $request, $additional_fields);

        $response = rest_ensure_response($data);
        $response = $this->format_collection_response($response, $request, 0);

        return $response;
    }

    /**
     * Get ledger categories
     *
     * @param WP_REST_REQUEST $request
     *
     * @return WP_ERROR|WP_REST_REQUEST
     */
    public function get_ledger_categories($request)
    {
        $chart_id = absint($request['chart_id']);

        $categories = $ledger->getLedgerCategories($chart_id);

        $response = rest_ensure_response($categories);

        $response->set_status(200);

        return $response;
    }

    /**
     * Create ledger categories
     *
     * @param WP_REST_REQUEST $request
     *
     * @return WP_ERROR|WP_REST_REQUEST
     */
    public function create_ledger_category($request)
    {
        $category = $ledger->createLedgerCategory($request);

        if (!$category) {
            return new WP_Error('rest_ledger_already_exist', __('Category already exist.'), ['status' => 404]);
        }

        $response = rest_ensure_response($category);

        $response->set_status(200);

        return $response;
    }

    /**
     * Update ledger categories
     *
     * @param WP_REST_REQUEST $request
     *
     * @return WP_ERROR|WP_REST_REQUEST
     */
    public function update_ledger_category($request)
    {
        $id = (int) $request['id'];

        if (empty($id)) {
            return new WP_Error('rest_ledger_invalid_id', __('Invalid resource id.'), ['status' => 404]);
        }

        $category = $ledger->updateLedgerCategory($request);

        if (!$category) {
            return new WP_Error('rest_ledger_already_exist', __('Category already exist.'), ['status' => 404]);
        }

        $response = rest_ensure_response($category);

        $response->set_status(200);

        return $response;
    }

    /**
     * Remove category
     */
    public function delete_ledger_category($request)
    {
        $id = (int) $request['id'];

        if (empty($id)) {
            return new WP_Error('rest_payment_invalid_id', __('Invalid resource id.'), ['status' => 404]);
        }

        $ledger->deleteLedgerCategory($id);

        return new WP_REST_Response(true, 204);
    }

    /**
     * Log for ledger related actions
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
                $changes   = !empty($old_data) ? erp_get_array_diff($data, (array) $old_data) : [];
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
                'sub_component' => __('Ledger Account', 'erp'),
                'old_value'     => isset($changes['old_value']) ? $changes['old_value'] : '',
                'new_value'     => isset($changes['new_value']) ? $changes['new_value'] : '',
                'message'       => sprintf(__('A ledger account named %1$s has been %2$s for %3$s', 'erp'), $data['name'], $operation, $people->getPeopleNameByPeopleId($data['people_id'])),
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

        $prepared_item['chart_id']    = !empty($request['chart_id']) ? (int) $request['chart_id'] : '';
        $prepared_item['category_id'] = !empty($request['category_id']) ? (int) $request['category_id'] : null;
        $prepared_item['name']        = !empty($request['name']) ? $request['name'] : '';
        $prepared_item['code']        = !empty($request['code']) ? $request['code'] : null;

        return $prepared_item;
    }

    /**
     * Prepare a single user output for response
     *
     * @param object          $item
     * @param WP_REST_Request $request           request object
     * @param array           $additional_fields (optional)
     *
     * @return WP_REST_Response $response response data
     */
    public function prepare_item_for_response($item, $request, $additional_fields = [])
    {
        $item = (object) $item;

        $data = [
            'id'          => $item->id,
            'chart_id'    => $item->chart_id,
            'category_id' => $item->category_id,
            'name'        => $item->name,
            'slug'        => $item->slug,
            'code'        => $item->code,
            'trn_count'   => $ledger->getLedgerTrnCount($item->id),
            'system'      => $item->system,
            'balance'     => $ledger->getLedgerBalance($item->id),
        ];

        $data = array_merge($data, $additional_fields);

        // Wrap the data in a response object
        $response = rest_ensure_response($data);

        return $response;
    }

    /**
     * Prepare a single user output for response
     *
     * @param object          $item
     * @param WP_REST_Request $request           request object
     * @param array           $additional_fields (optional)
     *
     * @return WP_REST_Response $response response data
     */
    public function prepare_ledger_for_response($item, $request, $additional_fields = [])
    {
        $item = (array) $item;

        $data = array_merge($item, $additional_fields);

        // Wrap the data in a response object
        $response = rest_ensure_response($data);

        return $response;
    }

    /**
     * @param $item
     * @param $request
     * @param $additional_fields
     *
     * @return mixed|WP_REST_Response
     */
    public function prepare_bank_item_for_response($item, $request, $additional_fields)
    {
        $item = (array) $item;

        $data = array_merge($item, $additional_fields);

        // Wrap the data in a response object
        $response = rest_ensure_response($data);

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
            'title'      => 'chart of account',
            'type'       => 'object',
            'properties' => [
                'id'          => [
                    'description' => __('Unique identifier for the resource.'),
                    'type'        => 'integer',
                    'context'     => ['embed', 'view', 'edit'],
                    'readonly'    => true,
                ],
                'chart_id'    => [
                    'description' => __('Type for the resource.'),
                    'type'        => 'integer',
                    'context'     => ['edit'],
                    'required'    => true,
                ],
                'category_id' => [
                    'description' => __('Code for the resource.'),
                    'type'        => 'integer',
                    'context'     => ['edit'],
                ],
                'name'        => [
                    'description' => __('Name for the resource.'),
                    'type'        => 'string',
                    'context'     => ['edit'],
                    'arg_options' => [
                        'sanitize_callback' => 'sanitize_text_field',
                    ],
                    'required'    => true,
                ],
                'code'        => [
                    'description' => __('Description for the resource.'),
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
