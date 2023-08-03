<?php
namespace Modules\Account\Classes;

use Modules\Account\Classes\Journal;

class Transactions
{
    /**
     * Post a transaction
     *
     * @param string $title
     * @param float $amount
     * @param int $partner_id
     * @param int $left_ledger_id
     * @param int $right_ledger_id
     *
     * @return void
     */
    public function postTransaction($title, $amount, $partner_id, $left_ledger_id, $right_ledger_id):void
    {
        $journal = new Journal();

        $journal->journalEntry($title, $amount, $partner_id, $left_ledger_id);
        $journal->journalEntry($title, $amount, $partner_id, $right_ledger_id);
    }
}
