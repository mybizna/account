<?php

namespace Modules\Account\Classes;

use Modules\Account\Classes\Ledger;
use Modules\Account\Entities\Journal as DBJournal;

class Journal
{

    public function journalEntry($title, $amount, $partner_id, $ledger_id)
    {
        $ledger_cls = new Ledger();

        $ledger =  $ledger_cls->getLedger($ledger_id);

        $chart = $ledger->chart_slug;

        $credit_debit = ($chart == 'asset' || $chart == 'expense') ? 'debit' : 'credit';

        if ($amount < 0) {
            $credit_debit = ($chart == 'asset' || $chart == 'expense') ? 'credit' : 'debit';
        }

        DBJournal::create(
            [
                'title' => $title,
                'partner_id' => $partner_id,
                'ledger_id' => $ledger_id,
                $credit_debit => abs($amount)
            ]
        );
    }
}
