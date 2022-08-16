<?php

namespace Modules\Account\Classes;

use Illuminate\Support\Facades\DB;

class Invoice
{
    /**
     * Get all sales transactions
     *
     * @param array $args Transaction Filters
     *
     * @return mixed
     */
    public function generateInvoice($partner_id, $items = [], $description = 'Invoice #', $amount_paid = 0.00, $payment_method = '')
    {

        DB::beginTransaction();

        try {
            $invoice_id = DB::table('account_invoice')->insertGetId(
                [
                    'partner_id' => $partner_id,
                    'description' => $description,
                    'status' => 'draft',
                ]
            );

            foreach ($items as $item_key => $item) {


                $invoice_item_id = DB::table('account_invoice_item')->insertGetId(
                    [
                        'invoice_id' => $invoice_id,
                        'ledger_id' => $item['ledger_id'],
                        'price' => $item['price'],
                        'amount' => $item['total'],
                        'description' => $item['title'],
                        'quantity' => $item['quantity'],
                    ]
                );
                foreach ($item['rates'] as $rate_key => $rate) {
                    DB::table('account_invoice_item_rate')->insert(
                        [
                            'invoice_item_id' => $invoice_item_id,
                            'rate_id' => $rate['id'],
                            'title' => $rate['title'],
                            'slug' => $rate['slug'],
                            'value' => $rate['value'],
                            'is_percent' => $rate['is_percent'],
                        ]
                    );
                }
            }
        } catch (\Throwable $th) {
            throw $th;
            DB::rollback();
        }
    }
}
