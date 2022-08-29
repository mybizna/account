<?php

namespace Modules\Account\Classes;


use Illuminate\Support\Facades\DB;
use Modules\Account\Classes\Journal;

class Transactions
{
    public function postTransaction($title, $amount, $partner_id, $left_ledger_id, $right_ledger_id)
    {
        $journal = new Journal();

        $journal->journalEntry($title, $amount, $partner_id, $left_ledger_id);
        $journal->journalEntry($title, $amount, $partner_id, $right_ledger_id);
    }
}
