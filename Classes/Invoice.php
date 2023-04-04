<?php

namespace Modules\Account\Classes;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\Account\Classes\Journal;
use Modules\Account\Classes\Ledger;
use Modules\Account\Classes\Payment;
use Modules\Account\Entities\Invoice as DBInvoice;
use Modules\Account\Entities\InvoiceItem as DBInvoiceItem;
use Modules\Account\Entities\InvoiceItemRate as DBInvoiceItemRate;
use Modules\Account\Entities\Payment as DBPayment;
use Modules\Account\Events\InvoiceItemPaid;
use Modules\Account\Events\InvoicePaid;
use Modules\Core\Classes\Notification;
use Modules\Partner\Classes\Partner;

class Invoice
{
    /**
     * Generating invoice based on set parameters
     *
     * @param  string $title
     * @param  string $partner_id
     * @param  array  $items
     * @param  string $description Description for the Invoice
     * @return void
     */
    public function generateInvoice($title, $partner_id, $items = [], $status = 'draft', $description = "")
    {
        $total = 0;
        $ledger = new Ledger();
        $payment = new Payment();

        $invoice_no = $this->getInvoiceNumber();

        DB::beginTransaction();

        try {

            $due_date = Carbon::now()->addDays(14);

            $invoice = DBInvoice::create(
                [
                    'title' => $title,
                    'partner_id' => $partner_id,
                    'description' => $description,
                    'invoice_no' => $invoice_no,
                    'due_date' => $due_date,
                    'status' => $status,
                ]
            );

            $sales_revenue_id = $ledger->getLedgerId('sales_revenue');

            foreach ($items as $item_key => $item) {

                $tmp_total = isset($item['quantity']) && $item['quantity'] > 1 ? $item['total'] : $item['price'];

                $tmp_data = [
                    'title' => $item['title'],
                    'invoice_id' => $invoice->id,
                    'ledger_id' => isset($item['ledger_id']) && $item['ledger_id'] ? $item['ledger_id'] : $sales_revenue_id,
                    'price' => $item['price'],
                    'amount' => $tmp_total,
                    'quantity' => isset($item['quantity']) && $item['quantity'] ? $item['quantity'] : 1,
                ];

                if (isset($item['module'])) {
                    $tmp_data['module'] = ucfirst($item['module']);
                }

                if (isset($item['model'])) {
                    $tmp_data['model'] = ucfirst($item['model']);
                }

                if (isset($item['item_id'])) {
                    $tmp_data['item_id'] = $item['item_id'];
                }

                $invoice_item = DBInvoiceItem::create($tmp_data);

                if (isset($item['rates'])) {
                    foreach ($item['rates'] as $rate_key => $rate) {
                        $value = $rate['value'];
                        $method = $rate['method'];

                        DBInvoiceItemRate::create(
                            [
                                'title' => $rate['title'],
                                'slug' => $rate['slug'],
                                'rate_id' => $rate['id'],
                                'invoice_item_id' => $invoice_item->id,
                                'method' => $rate['method'],
                                'value' => $rate['value'],
                                'params' => $rate['params'],
                                'ordering' => $rate['ordering'],
                                'on_total' => $rate['on_total'],
                            ]
                        );

                        if ($value != 0) {
                            if ($method == '-') {
                                $tmp_total = $tmp_total - 1 * $value;
                            } elseif ($method == '-%') {
                                $tmp_total = $tmp_total - 1 * $item_total * $value / 100;
                            } elseif ($method == '+%') {
                                $tmp_total = $tmp_total + $item_total * $value / 100;
                            }
                        }
                    }
                }

                $total = $total + $tmp_total;
            }

            if ($description == '') {
                $description = "Invoice #$invoice->id.";
            }

            DBInvoice::where('id', $invoice->id)->update(['description' => $description, 'total' => $total]);

            $this->reconcileInvoices($partner_id, $invoice->id);

            DB::commit();

            $invoice = $this->getInvoice($invoice->id);

            return $invoice;

        } catch (\Throwable$th) {
            DB::rollback();
            throw $th;
        }

    }

    public function reconcileInvoices($partner_id, $invoice_id = false)
    {
        $journal = new Journal();
        $ledger = new Ledger();
        $partner = new Partner();
        $notification = new Notification();

        $invoices_total = 0;
        $amount_paid = 0;
        $wallet_total = 0;
        $receivable_total = 0;
        $non_posted_invoices_total = 0;
        $payments_title = '';

        $grouping_id = $journal->getGroupingId();
        $wallet_id = $ledger->getLedgerId('wallet');
        $receivable_id = $ledger->getLedgerId('accounts_receivable');
        $partner = $partner->getPartner($partner_id);

        // Add all payments to journal entry
        $payments = DBPayment::from('account_payment AS ap')
            ->select('ap.*', 'ag.ledger_id')
            ->leftJoin('account_gateway AS ag', 'ag.id', '=', 'ap.gateway_id')
            ->where('ap.partner_id', $partner_id)
            ->where('ap.is_posted', false)
            ->where('ap.type', 'in')
            ->get();

        foreach ($payments as $payment_key => $payment) {

            if (!$payment->partner_id || !$payment->ledger_id) {
                //TODO: Notification on payment with issues.
            } else {
                $amount_paid = $amount_paid + $payment->amount;

                $journal->journalEntry($payment->title, $payment->amount, $payment->partner_id, $payment->ledger_id, grouping_id:$grouping_id);

                DBPayment::where('id', $payment->id)->update(['is_posted' => true]);
            }
        }

        // get partner invoices
        $invoices = $this->getPartnerInvoices($partner_id, $invoice_id);

        foreach ($invoices as $invoice_key => $invoice) {
            $invoice_data = [];
            $single_invoice_total = 0;
            $inv_balance = 0;

            $items = DBInvoiceItem::where('invoice_id', $invoice->id)->get(); 

            // Get wallet total
            Cache::forget("account_ledger_total_" . $wallet_id . '_' . $partner_id);
            $wallet_ledger = $ledger->getLedgerTotal($wallet_id, $partner_id);
            $wallet_total = (isset($wallet_ledger['total']) && (int) $wallet_ledger['total'] > 0) ? (int) $wallet_ledger['total'] : 0;

            $inv_balance = $amount_paid + $wallet_total;

            if ($invoice->is_posted == false) {

                foreach ($items as $key => $item) {

                    $amount = $item_total = ($item->quantity) ? $item->price * $item->quantity : $item->price;
                    $title = ($item->title) ? $item->title : "Item ID: $item->id";
                    $journal->journalEntry($title, $amount, $invoice->partner_id, $item->ledger_id, grouping_id:$grouping_id);

                    $item_rates = DBInvoiceItemRate::from('account_invoice_item_rate AS air')
                        ->select('air.*', 'at.ledger_id AS rate_ledger_id', 'at.title AS rate_title', 'at.method AS rate_method')
                        ->leftJoin('account_rate AS at', 'at.id', '=', 'air.rate_id')
                        ->where('invoice_item_id', $item->id)
                        ->orderBy('method')
                        ->orderBy('ordering')
                        ->get();

                    foreach ($item_rates as $item_key => $item_rate) {
                        $value = $calc_amount = ($item_rate->value) ? $item_rate->value : 0;
                        $title = ($item_rate->title) ? $item_rate->title : "Item ID:$item->id Rate:$item_rate->rate_title";

                        if ($value != 0) {
                            if ($item_rate->rate_method == '-') {
                                $calc_amount = -1 * $value;
                            } elseif ($item_rate->rate_method == '-%') {
                                $calc_amount = -1 * $item_total * $value / 100;
                            } elseif ($item_rate->rate_method == '+%') {
                                $calc_amount = $item_total * $value / 100;
                            }
                        }

                        $item_total = $item_total + $calc_amount;

                        $journal->journalEntry($title, $calc_amount, $invoice->partner_id, $item_rate->rate_ledger_id, grouping_id:$grouping_id);
                    }

                    $single_invoice_total = $single_invoice_total + $item_total;
                }

                $invoice_data['is_posted'] = true;
                $invoice_data['total'] = $single_invoice_total;

            } else {
                $single_invoice_total = $invoice->total;
            }

            if ($wallet_total >= $single_invoice_total) {

                $invoice_data['status'] = 'paid';

                $journal->journalEntry("$invoice->invoice_no Payment By wallet worth " . abs($single_invoice_total), -1 * abs($single_invoice_total), $partner_id, $wallet_id, grouping_id:$grouping_id);

                if ($invoice->is_posted == true) {
                    $journal->journalEntry("$invoice->invoice_no Full Payment of " . abs($single_invoice_total), -1 * abs($single_invoice_total), $partner_id, $receivable_id, grouping_id:$grouping_id);
                }

                $invoice->payment_amount = $single_invoice_total;
                $notification->send('account_invoice_paid', $partner, $invoice);

            } elseif ($wallet_total > 0 && $inv_balance >= $single_invoice_total) {

                $invoice_data['status'] = 'paid';

                $amount_paid = $amount_paid - ($single_invoice_total - $wallet_total);

                $journal->journalEntry("$invoice->invoice_no Payment By wallet worth " . abs($wallet_total), -1 * abs($wallet_total), $partner_id, $wallet_id, grouping_id:$grouping_id);
                if ($invoice->is_posted == true) {
                    $journal->journalEntry("$invoice->invoice_no Full Payment of " . abs($single_invoice_total), -1 * abs($single_invoice_total), $partner_id, $receivable_id, grouping_id:$grouping_id);
                }

                $invoice->payment_amount = abs($single_invoice_total);
                $notification->send('account_invoice_paid', $partner, $invoice);

            } elseif ($wallet_total > 0 && $inv_balance > 0 && $inv_balance < $single_invoice_total) {

                $invoice_data['status'] = 'partial';

                $debt = $single_invoice_total - $inv_balance;

                $journal->journalEntry("$invoice->invoice_no Payment By wallet worth " . abs($wallet_total), -1 * abs($wallet_total), $partner_id, $wallet_id, grouping_id:$grouping_id);

                if ($invoice->is_posted == true) {
                    $journal->journalEntry("$invoice->invoice_no Partial Payment of " . abs($inv_balance), -1 * abs($inv_balance), $partner_id, $receivable_id, grouping_id:$grouping_id);
                } else {
                    $journal->journalEntry("$invoice->invoice_no Debt of " . $debt, $debt, $partner_id, $receivable_id, grouping_id:$grouping_id);
                }

                $invoice->payment_amount = $inv_balance;
                $invoice->balance = $debt;
                $notification->send('account_invoice_partial', $partner, $invoice);

                $amount_paid = 0;
            } elseif (!$wallet_total && $inv_balance >= $single_invoice_total) {

                $invoice_data['status'] = 'paid';
                $amount_paid = $amount_paid - $single_invoice_total;

                if ($invoice->is_posted == true) {
                    $journal->journalEntry("$invoice->invoice_no Full Payment of " . abs($single_invoice_total), -1 * abs($single_invoice_total), $partner_id, $receivable_id, grouping_id:$grouping_id);
                }

                $invoice->payment_amount = abs($single_invoice_total);
                $notification->send('account_invoice_paid', $partner, $invoice);

            } elseif (!$wallet_total && $inv_balance > 0 && $inv_balance < $single_invoice_total) {

                $invoice_data['status'] = 'partial';
                $debt = $single_invoice_total - $inv_balance;

                if ($invoice->is_posted == true) {
                    $journal->journalEntry("$invoice->invoice_no Partial Payment of " . abs($inv_balance), -1 * abs($inv_balance), $partner_id, $receivable_id, grouping_id:$grouping_id);
                } else {
                    $journal->journalEntry("$invoice->invoice_no Debt of " . $debt, $debt, $partner_id, $receivable_id, grouping_id:$grouping_id);
                }

                $invoice->payment_amount = $inv_balance;
                $invoice->balance = $debt;
                
                $notification->send('account_invoice_partial', $partner, $invoice);

                $amount_paid = 0;

            } else {
                if ($invoice->is_posted == false) {

                    $journal->journalEntry("$invoice->invoice_no Debt of " . $single_invoice_total, $single_invoice_total, $partner_id, $receivable_id, grouping_id:$grouping_id);

                    $invoice->payment_amount = 0;
                    $invoice->balance = $single_invoice_total;
                    $notification->send('account_invoice_pending', $partner, $invoice);
                }
            }

            if (!empty($invoice_data)) {
                DBInvoice::where('id', $invoice->id)->update($invoice_data);
            }

            if (isset($invoice_data['status']) && $invoice_data['status'] == 'paid') {
                event(new InvoicePaid($invoice));
                foreach ($items as $key => $item) {
                    event(new InvoiceItemPaid($invoice, $item));
                }
            }

            $invoices_total = $invoices_total + $single_invoice_total;

        }

        $credit = $amount_paid + $invoices_total - $wallet_total;

        if ($credit > 0) {
            $journal->journalEntry('Credit to wallet worth ' . abs($credit), abs($credit), $partner_id, $wallet_id, grouping_id:$grouping_id);
        }

    }

    /**
     * Function to reconcile invoices
     */

    /**
     * Getting all Invoices
     *
     * @return $invoice List of all Invoices
     */
    public function getInvoices()
    {
        $invoices_qry = DBInvoice::where('status', 'pending')
            ->where('status', 'partial');

        $invoices = $invoices_qry->orderByDesc('is_posted')->get();

        return $invoices;
    }

    /**
     * Function for getting all Partner Invoices
     *
     * @param int $partner_id
     * @param boolean $invoice_id
     * @return void
     */
    public function getPartnerInvoices(int $partner_id, $invoice_id = false)
    {
        //# Get the invoices for this partner
        if ($invoice_id) {
            DBInvoice::where('id', $invoice_id)->update(['status' => 'pending']);
        }

        $invoices_qry = DBInvoice::where('partner_id', $partner_id)
            ->where('status', 'pending')
            ->orWhere('status', 'partial');

        if ($invoice_id) {
            $invoices_qry->where('id', $invoice_id);
        }

        $invoices = $invoices_qry->orderByDesc('is_posted')->get();

        return $invoices;
    }

    public function getInvoice($invoice_id, $fetch_items = false)
    {
        $invoice_total = 0;
        $invoice = DBInvoice::where('id', $invoice_id)->first();

        if ($fetch_items) {
            $items = DBInvoiceItem::where('invoice_id', $invoice->id)->get();

            foreach ($items as $key => $item) {

                $item_rates = DBInvoiceItemRate::from('account_invoice_item_rate AS air')
                    ->select('air.*', 'at.ledger_id AS rate_ledger_id', 'at.title AS rate_title', 'at.method AS rate_method')
                    ->leftJoin('account_rate AS at', 'at.id', '=', 'air.rate_id')
                    ->where('invoice_item_id', $item->id)
                    ->orderBy('method')
                    ->orderBy('ordering')
                    ->get();

                $item->rates = $item_rates;

            }

            $invoice->items = $items;

        }

        return $invoice;
    }

    public function processInvoices()
    {
        $invoices = DBInvoice::select('partner_id')->distinct()->where('status', 'pending')
            ->orWhere('status', 'partial')->orderByDesc('id')->get();

        foreach ($invoices as $invoice_key => $invoice) {
            $this->reconcileInvoices($invoice->partner_id);
        }
    }

    public function deleteInvoice($invoice_id)
    {
        try {
            DBInvoice::where('id', $invoice_id)->delete();
            DBInvoiceItem::where('invoice_id', $invoice_id)->delete();
        } catch (\Throwable$th) {
            throw $th;
        }

    }

    public function getInvoiceNumber()
    {

        $padding = 4;
        $prefix = 'Inv/' . date('Y') . '/' . date('m') . '/';

        $invoice_count = DBInvoice::where('invoice_no', 'like', $prefix . '%')->count() + 1;
        $invoice_count_str = (string) $invoice_count;

        $padding = $padding - strlen($invoice_count_str);

        return $prefix . str_pad($invoice_count_str, $padding, "0", STR_PAD_LEFT);

    }

}
