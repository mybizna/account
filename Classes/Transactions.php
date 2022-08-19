<?php

namespace Modules\Account\Classes;


use Illuminate\Support\Facades\DB;

class Transactions
{
    public function postTransaction($amount, $description, $partner_id, $left_ledger_id, $right_ledger_id)
    {
        $left_ledger = DB::table('account_ledger')->where('left_ledger_id', $left_ledger_id)->get();
        $right_ledger = DB::table('account_ledger')->where('right_ledger_id', $right_ledger_id)->get();

        DB::table('account_invoice_item_rate')->insert(
            [
                'amount' => $amount,
                'description' => $description,
                'partner_id' => $partner_id,
                'left_ledger_id' => $left_ledger_id,
                'right_ledger_id' => $right_ledger_id,
                'left_chart_of_account_id' => $left_ledger->chart_id,
                'right_chart_of_account_id' => $right_ledger->chart_id,
            ]
        );
    }
}
