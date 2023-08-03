<?php
namespace Modules\Account\Classes;

use Illuminate\Support\Facades\Cache;
use Modules\Account\Classes\Ledger;
use Modules\Account\Entities\Journal as DBJournal;

class Journal
{

    /**
     * Adding Entry to Journal
     *
     * @param string $title
     * @param float $amount
     * @param int $partner_id
     * @param int $ledger_id
     * @param int $grouping_id
     * @param int $payment_id
     * @param int $invoice_id
     *
     * @return void
     */
    public function journalEntry($title, $amount, $partner_id, $ledger_id, $grouping_id = null, $payment_id = null, $invoice_id = null)
    {
        $ledger_cls = new Ledger();

        $ledger = $ledger_cls->getLedger($ledger_id);

        $chart = $ledger->chart_slug;

        $credit_debit = ($chart == 'asset' || $chart == 'expense') ? 'debit' : 'credit';

        if ($amount < 0) {
            $credit_debit = ($chart == 'asset' || $chart == 'expense') ? 'credit' : 'debit';
        }

        if ($grouping_id == '') {
            $grouping_id = $this->getGroupingId();
        }

        $data = [
            'title' => $title,
            'partner_id' => $partner_id,
            'ledger_id' => $ledger_id,
            $credit_debit => abs($amount),
        ];

        if ($grouping_id) {
            $data['grouping_id'] = $grouping_id;
        }

        if ($payment_id) {
            $data['payment_id'] = $payment_id;
        }

        if ($invoice_id) {
            $data['invoice_id'] = $invoice_id;
        }

        DBJournal::create($data);

        Cache::forget("account_ledger_total_" . $ledger_id . '_' . $partner_id);

    }

    /**
     * Generate a unique Grouping ID
     *
     * @return string
     */
    public function getGroupingId()
    {

        $seed = str_split('abcdefghijklmnopqrstuvwxyz');

        # code...
        $rand = implode('',
            array_map(
                function ($n) use ($seed) {
                    return $seed[$n];
                }, array_rand($seed, 3)
            )
        );

        $unique = strtoupper(uniqid($rand . '-'));

        return $unique;
    }
}
