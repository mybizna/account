<?php

namespace Modules\Account\Classes;

use Illuminate\Support\Facades\DB;
use Modules\Account\Classes\Ledger;

class Journal
{

    public function journalEntry($amount, $title, $partner_id, $ledger_id)
    {
        $ledger_cls = new Ledger();

        $ledger =  $ledger_cls->getLedger($ledger_id);

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
