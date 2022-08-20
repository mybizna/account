<?php

namespace Modules\Account\Classes;


use Illuminate\Support\Facades\DB;

class Journal
{
    public function journalEntry($amount, $title, $partner_id, $ledger_id)
    {
        $ledger =  DB::table('account_ledger AS al, ac.slug AS chart_slug')
            ->leftJoin('account_chart_of_account AS ac', 'ac.id', '=', 'al.chart_id')
            ->where('id', $ledger_id)->first();

        $chart = $ledger->chart_slug;

        $credit_debit = ($chart == 'asset' || $chart == 'expense') ? 'debit' : 'credit';

        if ($amount < 0) {
            $credit_debit = ($chart == 'asset' || $chart == 'expense') ? 'credit' : 'debit';
        }

        DB::table('account_journal')->insert(
            [
                'title' => $title,
                'partner_id' => $partner_id,
                'ledger_id' => $ledger_id,
                $credit_debit => abs($amount)
            ]
        );
    }
}
