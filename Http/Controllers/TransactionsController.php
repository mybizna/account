<?php

namespace Modules\Account\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Account\Classes\CommonFunc;
use Modules\Account\Classes\Transactions;

use Illuminate\Support\Facades\DB;

class TransactionsController extends Controller
{

    /**
     * Get transactions type
     *
     * @param \Illuminate\Http\Request $request Request
     *
     * @return \Illuminate\Http\Response
     */
    public function getTransactionType(Request $request)
    {
        $trans = new Transactions();

        $input = $request->all();

        $voucher_no = !empty($input['voucher_no']) ? $input['voucher_no'] : 0;

        $voucher_type = $trans->getTransactionType($voucher_no);

        return response()->json($voucher_type);
    }

    /**
     * Get transaction statuses
     *
     * @param \Illuminate\Http\Request $request Request
     *
     * @return \Illuminate\Http\Response
     */
    public function getTrnStatuses(Request $request)
    {


        $statuses = DB::select("SELECT id, type_name as name, slug FROM account_transaction_status_type");

        return response()->json($statuses);
    }

    /**
     * Get sales transactions
     *
     * @param \Illuminate\Http\Request $request Request
     *
     * @return \Illuminate\Http\Response
     */
    public function getSales(Request $request)
    {


        $input = $request->all();
        $args = [
            'number'     => empty($input['per_page']) ? 20 : (int) $input['per_page'],
            'offset'     => ($input['per_page'] * ($input['page'] - 1)),
            'start_date' => empty($input['start_date']) ? '' : $input['start_date'],
            'end_date'   => empty($input['end_date']) ? date('Y-m-d') : $input['end_date'],
            'status'     => empty($input['status']) ? '' : $input['status'],
            'type'       => empty($input['type']) ? '' : $input['type'],
            'customer_id' => empty($input['customer_id']) ? '' : $input['customer_id'],
        ];



        $formatted_items   = [];
        $additional_fields = [];

        $additional_fields['namespace'] = __NAMESPACE__;

        $transactions = $this->getSalesTransactions($args);
        $total_items  = $this->getSalesTransactions(
            [
                'count'       => true,
                'number'      => -1,
                'status'      => $args['status'],
                'type'        => $args['type'],
                'start_date'  => $args['start_date'],
                'end_date'    => $args['end_date'],
                'customer_id' => $args['customer_id'],
            ]
        );

        foreach ($transactions as $transaction) {
            $formatted_items[]              = $this->prepareItemForResponse($transaction, $request, $additional_fields);
        }

        return response()->json($formatted_items);
    }

    /**
     * Chart status
     */
    public function getSalesChartStatus(Request $request)
    {
        $trans = new Transactions();

        $input = $request->all();

        $args = [
            'start_date' => empty($input['start_date']) ? '' : $input['start_date'],
            'end_date'   => empty($input['end_date']) ? date('Y-m-d') : $input['end_date'],
        ];

        $chart_status = $trans->getSalesChartStatus($args);

        return response()->json($chart_status);
    }

    /**
     * Chart payment
     */
    public function getSalesChartPayment(Request $request)
    {
        $trans = new Transactions();

        $input = $request->all();

        $args = [
            'start_date' => empty($input['start_date']) ? '' : $input['start_date'],
            'end_date'   => empty($input['end_date']) ? date('Y-m-d') : $input['end_date'],
        ];

        $chart_payment = $trans->getSalesChartPayment($args);

        return response()->json($chart_payment);
    }

    /**
     * Get Dashboard Income Expense Overview data
     *
     * @param \Illuminate\Http\Request $request Request
     *
     * @return mixed|\Illuminate\Http\Response
     */
    public function getIncomeExpenseOverview(Request $request)
    {
        $data = $this->getIncomeExpenseChartData();

        return response()->json($data);
    }

    /**
     * Get expense chart stauts data
     *
     * @param \Illuminate\Http\Request $request Request
     *
     * @return mixed|\Illuminate\Http\Response
     */
    public function getExpenseChartData(Request $request)
    {
        $trans = new Transactions();

        $input = $request->all();

        $args = [
            'start_date' => empty($input['start_date']) ? '' : $input['start_date'],
            'end_date'   => empty($input['end_date']) ? date('Y-m-d') : $input['end_date'],
        ];

        $bill_payment    = $this->getBillChartData($args);
        $expense_payment = $trans->getExpenseChartData($args);

        $chart_payment['paid'] = $bill_payment['paid'] + $expense_payment['paid'];

        $chart_payment['payable'] = $bill_payment['payable'] + $expense_payment['payable'];

        return response()->json($chart_payment);
    }

    /**
     * Get expense Chart status
     *
     * @param \Illuminate\Http\Request $request Request
     *
     * @return mixed|\Illuminate\Http\Response
     */
    public function getExpenseChartStatus(Request $request)
    {
        $trans = new Transactions();

        $input = $request->all();
        $args = [
            'start_date' => empty($input['start_date']) ? '' : $input['start_date'],
            'end_date'   => empty($input['end_date']) ? date('Y-m-d') : $input['end_date'],
        ];

        $chart_statuses = $trans->getBillChartStatus($args);
        $expense_status = $trans->getExpenseChartStatus($args);

        foreach ($chart_statuses as $bill_status) {
            if ($bill_status['type_name'] === $expense_status['type_name']) {
                $bill_status['sub_total'] += $expense_status['sub_total'];
            } else {
                array_push($chart_statuses, $expense_status);
                break;
            }
        }

        return response()->json($chart_statuses);
    }

    /**
     * Get Expense transactions
     *
     * @param \Illuminate\Http\Request $request Request
     *
     * @return \Illuminate\Http\Response
     */
    public function getExpenses(Request $request)
    {
        $trans = new Transactions();

        $input = $request->all();
        $args = [
            'number'     => empty($input['per_page']) ? 20 : (int) $input['per_page'],
            'offset'     => ($input['per_page'] * ($input['page'] - 1)),
            'start_date' => empty($input['start_date']) ? '' : $input['start_date'],
            'end_date'   => empty($input['end_date']) ? date('Y-m-d') : $input['end_date'],
            'status'     => empty($input['status']) ? '' : $input['status'],
            'type'       => empty($input['type']) ? '' : $input['type'],
            'vendor_id'  => empty($input['vendor_id']) ? '' : $input['vendor_id'],
        ];

        $formatted_items   = [];
        $additional_fields = [];

        $additional_fields['namespace'] = __NAMESPACE__;

        $transactions = $trans->getExpenseTransactions($args);
        $total_items  = $trans->getExpenseTransactions(
            [
                'count'      => true,
                'number'     => -1,
                'status'     => $args['status'],
                'type'       => $args['type'],
                'start_date' => $args['start_date'],
                'end_date'   => $args['end_date'],
                'vendor_id'  => $args['vendor_id']
            ]
        );

        foreach ($transactions as $transaction) {
            $formatted_items[]              = $this->prepareItemForResponse($transaction, $request, $additional_fields);
        }

        return response()->json($formatted_items);
    }

    /**
     * Get Purchase chart stauts data
     *
     * @param \Illuminate\Http\Request $request Request
     *
     * @return mixed|\Illuminate\Http\Response
     */
    public function getPurchaseChartData(Request $request)
    {
        $trans = new Transactions();

        $input = $request->all();
        $args = [
            'start_date' => empty($input['start_date']) ? '' : $input['start_date'],
            'end_date'   => empty($input['end_date']) ? date('Y-m-d') : $input['end_date'],
        ];

        $chart_payment = $trans->getPurchaseChartData($args);

        return response()->json($chart_payment);
    }

    /**
     * Get Purchase Chart status
     *
     * @param \Illuminate\Http\Request $request Request
     *
     * @return mixed|\Illuminate\Http\Response
     */
    public function getPurchaseChartStatus(Request $request)
    {
        $trans = new Transactions();

        $input = $request->all();

        $args = [
            'start_date' => empty($input['start_date']) ? '' : $input['start_date'],
            'end_date'   => empty($input['end_date']) ? date('Y-m-d') : $input['end_date'],
        ];

        $chart_status = $trans->getPurchaseChartStatus($args);

        return response()->json($chart_status);
    }

    /**
     * Get Purchase transactions
     *
     * @param \Illuminate\Http\Request $request Request
     *
     * @return \Illuminate\Http\Response
     */
    public function getPurchases(Request $request)
    {
        $trans = new Transactions();

        $input = $request->all();
        $args = [
            'number'     => (int) empty($input['per_page']) ? 20 : (int) $input['per_page'],
            'offset'     => ($input['per_page'] * ($input['page'] - 1)),
            'start_date' => empty($input['start_date']) ? '' : $input['start_date'],
            'end_date'   => empty($input['end_date']) ? date('Y-m-d') : $input['end_date'],
            'status'     => empty($input['status']) ? '' : $input['status'],
            'type'       => empty($input['type']) ? '' : $input['type'],
            'vendor_id'  => empty($input['vendor_id']) ? '' : $input['vendor_id'],
        ];

        $formatted_items   = [];
        $additional_fields = [];

        $additional_fields['namespace'] = __NAMESPACE__;

        $transactions = $trans->getPurchaseTransactions($args);
        $total_items  = $trans->getPurchaseTransactions(
            [
                'count'      => true,
                'number'     => -1,
                'status'     => $args['status'],
                'type'       => $args['type'],
                'start_date' => $args['start_date'],
                'end_date'   => $args['end_date'],
                'vendor_id'  => $args['vendor_id']
            ]
        );

        foreach ($transactions as $transaction) {
            $formatted_items[]              = $this->prepareItemForResponse($transaction, $request, $additional_fields);
        }

        return response()->json($formatted_items);
    }

    /**
     * Return available payment methods
     *
     * @return array
     */
    public function getPaymentMethods()
    {


        $rows = DB::select("SELECT id, name FROM payment_method");

        return apply_filters('pay_methods', $rows);
    }

    public function getVoucherType($request)
    {
        $trans = new Transactions();

        $input = $request->all();

        $id = (int) $input['id'];

        if (empty($id)) {
            config('kernel.messageBag')->add('rest_voucher_type_invalid_id', __('Invalid resource id.'));
            return;
        }

        $response = $trans->getTransactionType($id);

        return response()->json($response);
    }

    /**
     * Prepare a single user output for response
     *
     * @param object|array    $item
     * @param \Illuminate\Http\Request $request           request object
     * @param array           $additional_fields (optional)
     * @return object $response Response data.
     */
    public function prepareItemForResponse($item, Request $request, $additional_fields = [])
    {
        $common = new CommonFunc();
        $status = null;

        if (!empty($item['inv_status'])) {
            $status = $item['inv_status'];
        } else {
            if (!empty($item['pay_status'])) {
                $status = $item['pay_status'];
            } else {
                if (!empty($item['bill_status'])) {
                    $status = $item['bill_status'];
                } else {
                    if (!empty($item['pay_bill_status'])) {
                        $status = $item['pay_bill_status'];
                    } else {
                        if (!empty($item['expense_status'])) {
                            $status = $item['expense_status'];
                        } else {
                            if (!empty($item['purchase_status'])) {
                                $status = $item['purchase_status'];
                            } else {
                                if (!empty($item['pay_purchase_status'])) {
                                    $status = $item['pay_purchase_status'];
                                }
                            }
                        }
                    }
                }
            }
        }

        $item['status']      = $common->getTrnStatusById($status);
        $item['status_code'] = $status;

        $item['ref']         = isset($item['ref'])
            ? $item['ref']
            : (isset($item['pay_ref'])
                ? $item['pay_ref']
                : (isset($item['exp_ref'])
                    ? $item['exp_ref']
                    : ''));

        $data = array_merge($item, $additional_fields);


        return $data;
    }

    /**
     * Send mail with attachment
     *
     * @param \Illuminate\Http\Request $request Request
     *
     * @return array|mixed|object
     */
    public function sendAsPdf($request)
    {
        $trans = new Transactions();

        $input = $request->all();
        $id = (int) $input['id'];

        if (empty($id)) {
            config('kernel.messageBag')->add('rest_trn_invalid_id', __('Invalid resource id.'));
            return;
        }

        $response = [
            'status'  => 304,
            'message' => 'There was an error sending mail!',
        ];

        $file_name   = $trans->getPdfFilename($input['trn_data']['voucher_no']);
        $transaction = (object) $input['trn_data'];

        if ($trans->sendEmailWithPdfAttached($request, $transaction, $file_name, 'F')) {
            $response['status']  = 200;
            $response['message'] = 'mail sent successfully.';
        }
    }

    /**
     * Chart transaction data of a people
     */
    public function getPeopleTrnAmountData($request)
    {
        $trans = new Transactions();

        $input = $request->all();

        $input = $request->all();
        $args = [
            'start_date' => empty($input['start_date']) ? '' : $input['start_date'],
            'end_date'   => empty($input['end_date']) ? date('Y-m-d') : $input['end_date'],
        ];

        $args['people_id'] = $input['id'];

        $bill_payment     = $trans->getBillChartData($args);
        $expense_payment  = $trans->getExpenseChartData($args);
        $sales_payment    = $trans->getSalesChartPayment($args);
        $purchase_payment = $trans->getPurchaseChartData($args);

        $chart_payment['paid']    = $bill_payment['paid'] + $expense_payment['paid'] + $sales_payment['received'] + $purchase_payment['paid'];
        $chart_payment['payable'] = $bill_payment['payable'] + $expense_payment['payable'] + $sales_payment['outstanding'] + $purchase_payment['payable'];

        return response()->json($chart_payment);
    }

    /**
     * Chart transaction status/s of a people
     */
    public function getPeopleTrnStatusData($request)
    {
        $trans = new Transactions();

        $input = $request->all();
        $args = [
            'start_date' => empty($input['start_date']) ? '' : $input['start_date'],
            'end_date'   => empty($input['end_date']) ? date('Y-m-d') : $input['end_date'],
        ];

        $args['people_id'] = $input['id'];

        $chart_statuses    = $trans->getBillChartStatus($args);
        $expense_status    = $trans->getExpenseChartStatus($args);
        $sales_statuses    = $trans->getSalesChartStatus($args);
        $purchase_statuses = $trans->getPurchaseChartStatus($args);

        foreach ($chart_statuses as $key => $item) {
            $chart_statuses[$key]['sub_total'] = (int) $chart_statuses[$key]['sub_total'];
        }

        foreach ($sales_statuses  as $key => $item) {
            $sales_statuses[$key]['sub_total'] = (int) $sales_statuses[$key]['sub_total'];
        }

        foreach ($purchase_statuses as $key => $item) {
            $purchase_statuses[$key]['sub_total'] = (int) $purchase_statuses[$key]['sub_total'];
        }

        if (!empty($expense_status)) {
            $expense_status['sub_total'] = (int) $expense_status['sub_total'];
            array_push($chart_statuses, $expense_status);
        }
        $chart_statuses = array_merge($chart_statuses, $sales_statuses, $purchase_statuses);

        $statuses = [];

        $len = count($chart_statuses);

        for ($i = 0; $i < $len; $i++) {
            $k = 0;

            if (is_null($chart_statuses[$i])) {
                continue;
            }

            for ($j = $i + 1; $j < $len; $j++) {
                if (is_null($chart_statuses[$j])) {
                    continue;
                }

                if ($chart_statuses[$i]['type_name'] === $chart_statuses[$j]['type_name']) {
                    $chart_statuses[$i]['sub_total'] += $chart_statuses[$j]['sub_total'];
                    $statuses[$k]['type_name']        = $chart_statuses[$i]['type_name'];
                    $statuses[$k]['sub_total']        = $chart_statuses[$i]['sub_total'];
                    $k++;
                    $chart_statuses[$j] = null;
                }
            }
            $statuses[$k]['type_name'] = $chart_statuses[$i]['type_name'];
            $statuses[$k]['sub_total'] = $chart_statuses[$i]['sub_total'];
            $k++;
        }

        return response()->json($statuses);
    }
}
