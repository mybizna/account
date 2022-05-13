<?php

namespace Modules\Account\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Account\Classes\CommonFunc;
use Modules\Account\Classes\Transactions;
use Modules\Account\Classes\Invoices;

use Illuminate\Support\Facades\DB;

class InvoicesController extends Controller
{

    /**
     * Get a collection of invoices
     *
     * @param \Illuminate\Http\Request $request Request
     *
     * @return \Illuminate\Http\Response
     */
    public function getInvoices(Request $request)
    {
        $invoices = new Invoices();

        $args = [
            'number'     => $request['per_page'],
            'offset'     => ($request['per_page'] * ($request['page'] - 1)),
            'start_date' => empty($request['start_date']) ? '' : $request['start_date'],
            'end_date'   => empty($request['end_date']) ? date('Y-m-d') : $request['end_date'],
        ];

        $formatted_items   = [];
        $additional_fields = [];

        $additional_fields['namespace'] = $this->namespace;
        $additional_fields['rest_base'] = $this->rest_base;

        $invoice_data = $invoices->getAllInvoices($args);
        $total_items  = $invoices->getAllInvoices(
            [
                'count'  => true,
                'number' => -1,
            ]
        );

        foreach ($invoice_data as $item) {
            if (isset($request['include'])) {
                $include_params = explode(',', str_replace(' ', '', $request['include']));

                if (in_array('created_by', $include_params, true)) {
                    $item['created_by'] = $this->get_user($item['created_by']);
                }
            }

            $data              = $this->prepareItemForResponse($item, $request, $additional_fields);
            $formatted_items[] = $this->prepareResponseForCollection($data);
        }

        return response()->json($formatted_items);
    }

    /**
     * Get an invoice
     *
     * @param \Illuminate\Http\Request $request Request
     *
     * @return \Illuminate\Http\Response
     */
    public function getInvoice(Request $request)
    {
        $trans = new Transactions();
        $invoices = new Invoices();
        $id = (int) $request['id'];

        if (empty($id)) {
            messageBag()->add('rest_invoice_invalid_id', __('Invalid resource id.'), ['status' => 404]);
            return;
        }

        $item = $invoices->getInvoice($id);

        $link_hash    = $trans->getInvoiceLinkHash($id, 'invoice');
        $readonly_url = site_url().http_build_query([
            'query'    => 'readonly_invoice',
            'trans_id' => $id,
            'auth'     => $link_hash,
        ]);

        $item['readonly_url'] = $readonly_url;

        $additional_fields['namespace'] = $this->namespace;
        $additional_fields['rest_base'] = $this->rest_base;
        $item                           = $this->prepareItemForResponse($item, $request, $additional_fields);

        return response()->json($item);
    }

    /**
     * Create an invoice
     *
     * @param \Illuminate\Http\Request $request Request
     *
     * @return \Illuminate\Http\Response
     */
    public function createInvoice(Request $request)
    {
        $invoices = new Invoices();
        $invoice_data = $this->prepareItemFDatabase($request);

        $item_total          = 0;
        $item_discount_total = 0;
        $item_tax_total      = 0;
        $additional_fields   = [];

        $items = $request['line_items'];

        foreach ($items as $value) {
            $sub_total = $value['qty'] * $value['unit_price'];

            $item_total += $sub_total;
            $item_tax_total += $value['tax'];
            $item_discount_total += $value['discount'];
        }

        $invoice_data['billing_address'] = maybe_serialize($request['billing_address']);
        $invoice_data['discount']        = $item_discount_total;
        $invoice_data['discount_type']   = $request['discount_type'];
        $invoice_data['tax_rate_id']     = $request['tax_rate_id'];
        $invoice_data['tax']             = $item_tax_total;
        $invoice_data['amount']          = $item_total;
        $invoice_data['attachments']     = maybe_serialize($request['attachments']);
        $additional_fields['namespace']  = $this->namespace;
        $additional_fields['rest_base']  = $this->rest_base;

        $invoice_id = $invoices->insertInvoice($invoice_data);

        $invoice_data['id'] = $invoice_id;

        $this->addLog($invoice_data, 'add');

        $invoice_data = $this->prepareItemForResponse($invoice_data, $request, $additional_fields);

        return response()->json($invoice_data);
    }

    /**
     * Update an invoice
     *
     * @param \Illuminate\Http\Request $request Request
     *
     * @return \Illuminate\Http\Response
     */
    public function updateInvoice(Request $request)
    {
        $common = new CommonFunc();
        $invoices = new Invoices();
        $id = (int) $request['id'];

        if (empty($id)) {
            messageBag()->add('rest_invoice_invalid_id', __('Invalid resource id.'), ['status' => 404]);
            return;
        }

        $can_edit = $common->checkVoucherEditState($id);

        if (!$can_edit) {
            messageBag()->add('rest_invoice_invalid_edit', __('Invalid edit permission for update.'), ['status' => 403]);
            return;
        }

        $invoice_data = $this->prepareItemFDatabase($request);

        $item_total          = 0;
        $item_discount_total = 0;
        $item_tax_total      = 0;
        $additional_fields   = [];

        $items = $request['line_items'];

        foreach ($items as $value) {
            $sub_total = $value['qty'] * $value['unit_price'];

            $item_total += $sub_total;
            $item_tax_total += $value['tax'];
            $item_discount_total += $value['discount'];
        }

        $invoice_data['billing_address'] = maybe_serialize($request['billing_address']);
        $invoice_data['discount']        = $item_discount_total;
        $invoice_data['discount_type']   = $request['discount_type'];
        $invoice_data['tax_rate_id']     = $request['tax_rate_id'];
        $invoice_data['tax']             = $item_tax_total;
        $invoice_data['amount']          = $item_total;
        $invoice_data['attachments']     = maybe_serialize($request['attachments']);
        $additional_fields['namespace']  = $this->namespace;
        $additional_fields['rest_base']  = $this->rest_base;

        $old_data = $invoices->getInvoice($id);

        $invoice_id = $invoices->updateInvoice($invoice_data, $id);

        $this->addLog($id, 'edit', $old_data);

        $invoice_data['id'] = $invoice_id;

        $invoice_data = $this->prepareItemForResponse($invoice_data, $request, $additional_fields);

        return response()->json($invoice_data);
    }

    /**
     * Void an invoice
     *
     * @param \Illuminate\Http\Request $request Request
     *
     * @return messageBag()->add|\Illuminate\Http\Request
     */
    public function voidInvoice(Request $request)
    {
        $id = (int) $request['id'];

        if (empty($id)) {
            messageBag()->add('rest_invoice_invalid_id', __('Invalid resource id.'));
            return;
        }

        $this->voidInvoice($id);

        return response()->json(['status' => true]);
    }

    /**
     * Get a collection of invoices with due of a customer
     *
     * @param \Illuminate\Http\Request $request Request
     *
     * @return \Illuminate\Http\Response
     */
    public function dueInvoices(Request $request)
    {
        $invoices = new Invoices();
        $id = (int) $request['id'];

        if (empty($id)) {
            messageBag()->add('rest_invoice_invalid_id', __('Invalid resource id.'), ['status' => 404]);
            return;
        }

        $args = [
            'number' => isset($request['per_page']) ? $request['per_page'] : 20,
            'offset' => ($request['per_page'] * ($request['page'] - 1)),
        ];

        $formatted_items   = [];
        $additional_fields = [];

        $additional_fields['namespace'] = $this->namespace;
        $additional_fields['rest_base'] = $this->rest_base;

        $invoice_data = $invoices->receivePaymentsFromCustomer(['people_id' => $id]);
        $total_items  = count($invoice_data);

        foreach ($invoice_data as $item) {
            if (isset($request['include'])) {
                $include_params = explode(',', str_replace(' ', '', $request['include']));

                if (in_array('created_by', $include_params, true)) {
                    $item['created_by'] = $this->get_user($item['created_by']);
                }
            }

            $data              = $this->prepareItemForResponse($item, $request, $additional_fields);
            $formatted_items[] = $this->prepareResponseForCollection($data);
        }

        return response()->json($formatted_items);
    }

    /**
     * Get Dashboard Recievables segments
     *
     * @param \Illuminate\Http\Request $request Request
     *
     * @return mixed|\Illuminate\Http\Response
     */
    public function getOverviewReceivables(Request $request)
    {
        $invoices = new Invoices();
        $items    = $invoices->getRecievablesOverview();
        return response()->json($items);
    }

    /**
     * Upload attachment for invoice
     *
     * @param \Illuminate\Http\Request $request Request
     *
     * @return messageBag()->add|\Illuminate\Http\Request
     */
    public function uploadAttachments(Request $request)
    {
        $common = new CommonFunc();

        $file = $_FILES['attachments'];

        $movefiles = $common->uploadAttachments($file);

        return response()->json($movefiles);
    }

    /**
     * Log for invoice related actions
     *
     * @param int $id
     * @param string $action
     * @param array $old_data
     *
     * @return void
     */
    public function addLog($id, $action, $old_data = [])
    {
        $common = new CommonFunc();
        $invoices = new Invoices();
        switch ($action) {
            case 'edit':
                $operation = 'updated';
                $data      = $invoices->getInvoice($id);
                $changes   = !empty($old_data) ? $common->getArrayDiff((array) $data, (array) $old_data) : [];
                unset($changes['pdf_link'], $changes['attachments'], $changes['line_items']);
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
    protected function prepareItemFDatabase(Request $request)
    {
        $prepared_item = [];

        if (isset($request['customer_id'])) {
            $prepared_item['customer_id'] = $request['customer_id'];
        }

        if (isset($request['date'])) {
            $prepared_item['date'] = $request['date'];
        }

        if (isset($request['due_date'])) {
            $prepared_item['due_date'] = $request['due_date'];
        }

        if (isset($request['billing_address'])) {
            $prepared_item['billing_address'] = maybe_serialize($request['billing_address']);
        }

        if (isset($request['line_items'])) {
            $prepared_item['line_items'] = $request['line_items'];
        }

        if (isset($request['discount_type'])) {
            $prepared_item['discount_type'] = $request['discount_type'];
        }

        if (isset($request['tax_rate_id'])) {
            $prepared_item['tax_rate_id'] = $request['tax_rate_id'];
        }

        if (isset($request['status'])) {
            $prepared_item['status'] = $request['status'];
        }

        if (isset($request['estimate'])) {
            $prepared_item['estimate'] = $request['estimate'];
        }

        if (isset($request['attachments'])) {
            $prepared_item['attachments'] = maybe_serialize($request['attachments']);
        }

        if (isset($request['particulars'])) {
            $prepared_item['particulars'] = $request['particulars'];
        }

        if (isset($request['transaction_by'])) {
            $prepared_item['transaction_by'] = $request['transaction_by'];
        }

        if (isset($request['convert'])) {
            $prepared_item['convert'] = $request['convert'];
        }

        $prepared_item['request'] = $request;

        return $prepared_item;
    }

    /**
     * Prepare a single user output for response
     *
     * @param object|array    $item
     * @param \Illuminate\Http\Request $request           request object
     * @param array           $additional_fields (optional)
     *
     * @return \Illuminate\Http\Response $response response data
     */
    public function prepareItemForResponse($item, Request $request, $additional_fields = [])
    {
        $data = array_merge($item, $additional_fields);


        return $data;
    }

    /**
     * Get the User's schema, conforming to JSON Schema
     *
     * @return array
     */
    public function getItemSchema()
    {
        $schema = [
            '$schema'    => 'http://json-schema.org/draft-04/schema#',
            'title'      => 'invoice',
            'type'       => 'object',
            'properties' => [
                'customer_id'     => [
                    'description' => __('Customer id for the resource.'),
                    'type'        => 'integer',
                    'context'     => ['edit'],
                    'required'    => true,
                ],
                'date'            => [
                    'description' => __('Date for the resource.'),
                    'type'        => 'string',
                    'context'     => ['edit'],
                    'arg_options' => [
                        'sanitize_callback' => 'sanitize_text_field',
                    ],
                    'required'    => true,
                ],
                'due_date'        => [
                    'description' => __('Due date for the resource.'),
                    'type'        => 'string',
                    'context'     => ['edit'],
                    'arg_options' => [
                        'sanitize_callback' => 'sanitize_text_field',
                    ],
                    'required'    => true,
                ],
                'billing_address' => [
                    'description' => __('List of billing address data.', 'erp'),
                    'type'        => 'string',
                    'context'     => ['view', 'edit'],
                    'arg_options' => [
                        'sanitize_callback' => 'sanitize_text_field',
                    ],
                ],
                'discount_type' => [
                    'description' => __('Discount type data.', 'erp'),
                    'type'        => 'string',
                    'context'     => ['view', 'edit'],
                    'arg_options' => [
                        'sanitize_callback' => 'sanitize_text_field',
                    ],
                ],
                'tax_rate_id' => [
                    'description' => __('Tax rate id.', 'erp'),
                    'type'        => 'integer',
                    'context'     => ['view', 'edit'],
                ],
                'line_items'      => [
                    'description' => __('List of line items data.', 'erp'),
                    'type'        => 'array',
                    'context'     => ['view', 'edit'],
                    'properties'  => [
                        'product_id'   => [
                            'description' => __('Product id.', 'erp'),
                            'type'        => 'integer',
                            'context'     => ['view', 'edit'],
                        ],
                        'product_type_name' => [
                            'description' => __('Product type.', 'erp'),
                            'type'        => 'string',
                            'context'     => ['view', 'edit'],
                            'arg_options' => [
                                'sanitize_callback' => 'sanitize_text_field',
                            ],
                        ],
                        'tax_cat_id' => [
                            'description' => __('Product type.', 'erp'),
                            'type'        => 'integer',
                            'context'     => ['view', 'edit'],
                        ],
                        'qty'          => [
                            'description' => __('Product quantity.', 'erp'),
                            'type'        => 'integer',
                            'context'     => ['view', 'edit'],
                        ],
                        'unit_price'   => [
                            'description' => __('Unit price.', 'erp'),
                            'type'        => 'number',
                            'context'     => ['view', 'edit'],
                        ],
                        'discount'     => [
                            'description' => __('Discount.', 'erp'),
                            'type'        => 'number',
                            'context'     => ['view', 'edit'],
                        ],
                        'tax'          => [
                            'description' => __('Tax.'),
                            'type'        => 'number',
                            'context'     => ['edit'],
                        ],
                        'tax_rate'  => [
                            'description' => __('Tax percent.', 'erp'),
                            'type'        => 'number',
                            'context'     => ['view', 'edit'],
                        ],
                        'item_total'   => [
                            'description' => __('Item total.'),
                            'type'        => 'number',
                            'context'     => ['edit'],
                        ],
                    ],
                ],
                'type'            => [
                    'description' => __('Type for the resource.'),
                    'type'        => 'string',
                    'context'     => ['edit'],
                    'arg_options' => [
                        'sanitize_callback' => 'sanitize_text_field',
                    ],
                ],
                'status'          => [
                    'description' => __('Status for the resource.'),
                    'type'        => 'integer',
                    'context'     => ['edit'],
                ],
                'particulars'          => [
                    'description' => __('Status for the resource.'),
                    'type'        => 'string',
                    'context'     => ['edit'],
                    'arg_options' => [
                        'sanitize_callback' => 'sanitize_text_field',
                    ],
                ],
                'estimate'        => [
                    'description' => __('Status for the resource.'),
                    'type'        => 'integer',
                    'context'     => ['edit'],
                    'required'    => true,
                ],
            ],
        ];

        return $schema;
    }
}
