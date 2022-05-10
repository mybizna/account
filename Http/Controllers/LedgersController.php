<?php

namespace Modules\Account\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Account\Classes\CommonFunc;

use Modules\Account\Classes\Bank;

use Modules\Account\Classes\LedgerAccounts;

use Modules\Account\Classes\Reports\TrialBalance;

use Illuminate\Support\Facades\DB;

class LedgersController extends Controller
{
    /**
     * Get all the ledgers of a particular chart_id
     *
     * @param \Illuminate\Http\Request $request Request
     *
     * @return \Illuminate\Http\Response
     */
    public function get_all_ledger_accounts(Request $request)
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

        return response()->json($formatted_items);

        

        
    }

    /**
     * Get all the ledgers of a particular chart_id
     *
     * @param \Illuminate\Http\Request $request Request
     *
     * @return \Illuminate\Http\Response
     */
    public function get_ledger_accounts_by_chart(Request $request)
    {
        $ledger = new LedgerAccounts();
        $id = $request['chart_id'];

        if (empty($id)) {
            messageBag()->add('rest_empty_chart_id', __('Chart ID is Empty.'), ['status' => 400]);
            return ;
        }

        $items = $ledger->getLedgersByChartId($id);

        return response()->json($items);
        

        
    }

    /**
     * Get a specific ledger
     *
     * @param \Illuminate\Http\Request $request Request
     *
     * @return \Illuminate\Http\Response
     */
    public function get_ledger_account(Request $request)
    {
        $ledger = new LedgerAccounts();

        $items = [];

        $id = (int) $request['id'];

        if (empty($id)) {
            messageBag()->add('rest_ledger_invalid_id', __('Invalid resource id.'), ['status' => 404]);
            return ;
        }

        $item = $ledger->getLedger($id);

        $result   = $this->prepare_item_for_response($item, $request);
        return response()->json($item);

        
    }

    /**
     * Create an ledger
     *
     * @param \Illuminate\Http\Request $request Request
     *
     * @return messageBag()->add|\Illuminate\Http\Request
     */
    public function create_ledger_account(Request $request)
    {
        $ledger = new LedgerAccounts();


        $exist = DB::scalar("SELECT name FROM erp_acct_ledgers WHERE name = %s", [$request['name']]);

        if ($exist) {
            messageBag()->add('rest_ledger_name_already_exist', __('Name already exist.'), ['status' => 404]);
            return ;
        }

        $item = $this->prepare_item_for_database($request);

        $result = $ledger->insertLedger($item);

        $this->add_log((array) $result, 'add');

        $request->set_param('context', 'edit');
        $response = $this->prepare_item_for_response($result, $request);
        return response()->json($response);
        
        //$response->header( 'Location', rest_url( sprintf( '/%s/%s/%d', $this->namespace, $this->rest_base, $result['id'] ) ) );

        
    }

    /**
     * Update an ledger
     *
     * @param \Illuminate\Http\Request $request Request
     *
     * @return messageBag()->add|\Illuminate\Http\Request
     */
    public function update_ledger_account(Request $request)
    {
        $ledger = new LedgerAccounts();


        $id = (int) $request['id'];

        if (empty($id)) {
            messageBag()->add('rest_ledger_invalid_id', __('Invalid resource id.'), ['status' => 404]);
            return ;
        }

        $item = $this->prepare_item_for_database($request);

        $old_data = $ledger->getLedger($id);

        $result = $ledger->updateLedger($item, $id);

        $this->add_log($item, 'edit', $old_data);

        $request->set_param('context', 'edit');
        $response = $this->prepare_item_for_response($result, $request);
        return response()->json($response);
        $response->set_status(201);
        $response->header('Location', rest_url(sprintf('/%s/%s/%d', $this->namespace, $this->rest_base, $id)));

        
    }

    /**
     * Delete an account
     *
     * @param \Illuminate\Http\Request $request Request
     *
     * @return messageBag()->add|\Illuminate\Http\Request
     */
    public function delete_ledger_account(Request $request)
    {
        $ledger = new LedgerAccounts();


        $id = (int) $request['id'];

        if (empty($id)) {
            messageBag()->add('rest_ledger_invalid_id', __('Invalid resource id.'), ['status' => 404]);
            return ;
        }

        $item = $ledger->getLedger($id);

        DB::table("erp_acct_ledgers")->where([['id' => $id]])->delete();

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
    public function get_chart_accounts(Request $request)
    {
        $ledger = new LedgerAccounts();
        $accounts = $ledger->getAllCharts();

        return response()->json($accounts);

        

        
    }

    /**
     * Get a collection of bank accounts
     *
     * @param \Illuminate\Http\Request $request Request
     *
     * @return \Illuminate\Http\Response
     */
    public function get_bank_accounts(Request $request)
    {
        $bank = new Bank();
        $items = $bank->getBanks(true, false, false);

        if (empty($items)) {
            messageBag()->add('rest_empty_accounts', __('Bank accounts are empty.'), ['status' => 204]);
            return ;
        }

        $formatted_items = [];

        foreach ($items as $item) {
            $additional_fields = [];

            $data              = $this->prepare_bank_item_for_response($item, $request, $additional_fields);
            $formatted_items[] = $this->prepare_response_for_collection($data);
        }

        return response()->json($formatted_items);

        
    }

    /**
     * Get cash accounts
     *
     * @param \Illuminate\Http\Request $request Request
     *
     * @return \Illuminate\Http\Response
     */
    public function get_cash_accounts(Request $request)
    {
        $trialbal = new TrialBalance();

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

        return response()->json($data);

        
    }

    /**
     * Get ledger categories
     *
     * @param WP_REST_REQUEST $request
     *
     * @return WP_ERROR|WP_REST_REQUEST
     */
    public function get_ledger_categories(Request $request)
    {
        $ledger = new LedgerAccounts();
        $chart_id = absint($request['chart_id']);

        $categories = $ledger->getLedgerCategories($chart_id);

        return response()->json($categories);

        

        
    }

    /**
     * Create ledger categories
     *
     * @param WP_REST_REQUEST $request
     *
     * @return WP_ERROR|WP_REST_REQUEST
     */
    public function create_ledger_category(Request $request)
    {
        $ledger = new LedgerAccounts();
        $category = $ledger->createLedgerCategory($request);

        if (!$category) {
            messageBag()->add('rest_ledger_already_exist', __('Category already exist.'), ['status' => 404]);
            return ;
        }

        return response()->json($category);

        

        
    }

    /**
     * Update ledger categories
     *
     * @param WP_REST_REQUEST $request
     *
     * @return WP_ERROR|WP_REST_REQUEST
     */
    public function update_ledger_category(Request $request)
    {
        $ledger = new LedgerAccounts();
        $id = (int) $request['id'];

        if (empty($id)) {
            messageBag()->add('rest_ledger_invalid_id', __('Invalid resource id.'), ['status' => 404]);
            return ;
        }

        $category = $ledger->updateLedgerCategory($request);

        if (!$category) {
            messageBag()->add('rest_ledger_already_exist', __('Category already exist.'), ['status' => 404]);
            return ;
        }

        return response()->json($category);

        

        
    }

    /**
     * Remove category
     */
    public function delete_ledger_category(Request $request)
    {
        $ledger = new LedgerAccounts();
        $id = (int) $request['id'];

        if (empty($id)) {
            messageBag()->add('rest_payment_invalid_id', __('Invalid resource id.'), ['status' => 404]);
            return ;
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
        $common = new CommonFunc();
        switch ($action) {
            case 'edit':
                $operation = 'updated';
                $changes   = !empty($old_data) ? $common->getArrayDiff($data, (array) $old_data) : [];
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
     * @param \Illuminate\Http\Request $request request object
     *
     * @return array $prepared_item
     */
    protected function prepare_item_for_database(Request $request)
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
     * @param \Illuminate\Http\Request $request           request object
     * @param array           $additional_fields (optional)
     *
     * @return \Illuminate\Http\Response $response response data
     */
    public function prepare_item_for_response($item, Request $request, $additional_fields = [])
    {
        $ledger = new LedgerAccounts();
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


        return $data;
    }

    /**
     * Prepare a single user output for response
     *
     * @param object          $item
     * @param \Illuminate\Http\Request $request           request object
     * @param array           $additional_fields (optional)
     *
     * @return \Illuminate\Http\Response $response response data
     */
    public function prepare_ledger_for_response($item, Request $request, $additional_fields = [])
    {
        $item = (array) $item;

        $data = array_merge($item, $additional_fields);

        // Wrap the data in a response object
        return response()->json($data);

        
    }

    /**
     * @param $item
     * @param \Illuminate\Http\Request $request Request
     * @param $additional_fields
     *
     * @return mixed|\Illuminate\Http\Response
     */
    public function prepare_bank_item_for_response($item, Request $request, $additional_fields)
    {
        $item = (array) $item;

        $data = array_merge($item, $additional_fields);

        // Wrap the data in a response object
        return response()->json($data);

        
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
