<?php

namespace Modules\Account\Classes;

use Illuminate\Support\Facades\DB;
use Modules\Account\Classes\Journal;
use Modules\Account\Classes\Ledger;
use Modules\Account\Classes\Payment;
use Modules\Account\Entities\Invoice as DBInvoice;
use Modules\Account\Entities\InvoiceItem as DBInvoiceItem;
use Modules\Account\Entities\InvoiceItemRate as DBInvoiceItemRate;
use Modules\Account\Entities\Payment as DBPayment;
use Modules\Partner\Entities\Partner as DBPartner;

class Invoice
{
    public function generateInvoice($title, $partner_id, $items = [], $status = 'draft', $gateways = [], $description = "")
    {
        $total = 0;
        $ledger = new Ledger();
        $payment = new Payment();
        DB::beginTransaction();

        try {
            $invoice = DBInvoice::create(
                [
                    'title' => $title,
                    'partner_id' => $partner_id,
                    'description' => $description,
                    'status' => $status,
                ]
            );

            $sales_revenue_id = $ledger->getLedgerId('sales_revenue');

            foreach ($items as $item_key => $item) {
                $tmp_total = isset($item['quantity']) && $item['quantity'] > 1 ? $item['total'] : $item['price'];

                $invoice_item = DBInvoiceItem::create(
                    [
                        'title' => $item['title'],
                        'invoice_id' => $invoice->id,
                        'ledger_id' => isset($item['ledger_id']) && $item['ledger_id'] ? $item['ledger_id'] : $sales_revenue_id,
                        'price' => $item['price'],
                        'amount' => $total,
                        'quantity' => isset($item['quantity']) && $item['quantity'] ? $item['quantity'] : 1,
                    ]
                );

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

            foreach ($gateways as $item_key => $gateway) {
                if ($gateway['paid_amount'] && $status == 'draft') {
                    $status = 'pending';
                }

                $title = $gateway['title'] . " Payment. " . $gateway['reference'] . ' ' . $gateway['others'];
                $payment->makePayment($partner_id, $title, $gateway['paid_amount'], $gateway['id']);
            }

            if ($description == 'Invoice #') {
                $description = "Invoice #$invoice->id.";
                DBInvoice::where('id', $invoice->id)
                    ->update(
                        [
                            'description' => $description,
                            'status' => $status,
                        ]
                    );
            }

            DBInvoice::where('id', $invoice->id)->update(['total' => $total]);

            $this->reconcileInvoices($partner_id, $invoice->id);

            DB::commit();

            return $invoice->id;

        } catch (\Throwable$th) {
            DB::rollback();
            throw $th;
        }

    }

    public function reconcileInvoices($partner_id, $invoice_id = false)
    {
        $journal = new Journal();
        $ledger = new Ledger();

        $invoices_total = 0;
        $payments_total = 0;
        $tmp_payments_total = 0;
        $wallet_total = 0;
        $receivable_total = 0;
        $payments_title = '';

        // Add all payments to journal entry
        $payments = DBPayment::from('account_payment AS ap')
            ->select('ap.*', 'ag.ledger_id')
            ->leftJoin('account_gateway AS ag', 'ag.id', '=', 'ap.gateway_id')
            ->where('ap.partner_id', $partner_id)
            ->where('ap.is_posted', false)
            ->where('ap.type', 'in')
            ->get();

        foreach ($payments as $payment_key => $payment) {
            $payments_total = $payments_total + $payment->amount;
            $journal->journalEntry($payment->title, $payment->amount, $payment->partner_id, $payment->ledger_id);

            DBPayment::where('id', $payment->id)->update(['is_posted' => true]);
        }

        $wallet_id = $ledger->getLedgerId('wallet');
        $receivable_id = $ledger->getLedgerId('accounts_receivable');

        // Get wallet total
        $wallet_ledger = $ledger->getLedgerTotal($wallet_id);
        $wallet_total = (isset($wallet_ledger['total'])) ? $wallet_ledger['total'] : 0;

        // Get receivable total
        $receivable_ledger = $ledger->getLedgerTotal($receivable_id);
        $receivable_total = (isset($receivable_ledger['total'])) ? $receivable_ledger['total'] : 0;

        //Process receivable first
        if ($receivable_total) {
            $balance = $payments_total - $receivable_total;
            if ($balance <= 0) {
                $journal->journalEntry('Repayment of past debt', -1 * abs($payments_total), $partner_id, $receivable_id);
                $payments_total = 0;
            } else {
                $payments_total = $balance;
                $journal->journalEntry('Repayment of past debt', -1 * abs($receivable_total), $partner_id, $receivable_id);
            }
        }

        $tmp_payments_total = $payments_total;

        // process invoices
        $invoices = $this->getInvoices($partner_id, $invoice_id);

        foreach ($invoices as $payment_key => $invoice) {
            $invoice_data = [];
            $single_invoice_total = 0;

            if ($invoice->is_posted == false) {
                $items = DBInvoiceItem::where('invoice_id', $invoice->id)->get();

                foreach ($items as $key => $item) {
                    $amount = $item_total = ($item->quantity) ? $item->price * $item->quantity : $item->price;
                    $title = ($item->title) ? $item->title : "Item ID:$item->id";
                    $journal->journalEntry($title, $amount, $invoice->partner_id, $item->ledger_id);

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

                        $journal->journalEntry($title, $calc_amount, $invoice->partner_id, $item_rate->rate_ledger_id);
                    }

                    $single_invoice_total = $single_invoice_total + $item_total;
                }

                $invoice_data['is_posted'] = true;
                $invoice_data['total'] = $single_invoice_total;
            } else {
                $single_invoice_total = $invoice->total;
            }

            if ($tmp_payments_total > $single_invoice_total) {
                $invoice_data['status'] = 'paid';
            } elseif ($tmp_payments_total > 0) {
                $invoice_data['status'] = 'partial';
            }

            $tmp_payments_total = $tmp_payments_total - $single_invoice_total;

            if (!empty($invoice_data)) {
                DBInvoice::where('id', $invoice->id)->update($invoice_data);
            }

            $invoices_total = $invoices_total + $single_invoice_total;
        }

        if ($invoices_total) {
            $wallet_id = $ledger->getLedgerId('wallet');
            $receivable_id = $ledger->getLedgerId('accounts_receivable');

            if ($wallet_total) {
                $balance = $wallet_total - $invoices_total;
                if ($balance >= 0) {
                    $journal->journalEntry('Payment By wallet worth ' . abs($invoices_total), -1 * abs($invoices_total), $partner_id, $wallet_id);
                    $invoices_total = 0;
                } else {
                    $invoices_total = $invoices_total - $wallet_total;
                    $journal->journalEntry('Payment By wallet worth' . abs($wallet_total), -1 * abs($wallet_total), $partner_id, $wallet_id);
                }
            }

            $balance = $payments_total - $invoices_total;

            if ($invoice_id && $balance) {
                $this->reconcileInvoices($partner_id);
            }

            if ($balance > 0) {
                $journal->journalEntry('Credit to wallet worth ' . abs($balance), abs($balance), $partner_id, $wallet_id);
            } elseif ($balance < 0) {
                $journal->journalEntry('Partner Debt of ' . abs($balance), abs($balance), $partner_id, $receivable_id);
            }
        }
    }
    public function getInvoices($partner_id, $invoice_id = false)
    {
        if ($invoice_id) {
            DBInvoice::where('id', $invoice_id)->update(['status' => 'pending']);
        }

        $invoices_qry = DBInvoice::where('partner_id', $partner_id)
            ->where('status', '<>', 'draft')
            ->where('is_posted', false);

        if ($invoice_id) {
            $invoices_qry->where('id', $invoice_id);
        }

        $invoices = $invoices_qry->orderByDesc('id')->get();

        return $invoices;
    }

    public function processInvoices()
    {
        $partners = DBPartner::take(100)->get();

        foreach ($partners as $partner_key => $partner) {
            $this->reconcileInvoices($partner->id);
        }
    }

    public function deleteInvoices($invoice_id)
    {
        DBInvoice::where('id', $invoice_id)->delete();
        DBInvoiceItem::where('invoice_id', $invoice_id)->delete();
    }
}
