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
    public function generateInvoice($description, $partner_id, $items = [], $amount_paid = 0.00, $payment_method = '')
    {

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
                    'lerger_id' => $item['lerger_id'],
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
    }
}
