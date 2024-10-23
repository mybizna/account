<?php
namespace Modules\Account\Classes;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\Account\Classes\Ledger;
use Modules\Account\Models\Journal as DBJournal;
use Modules\Core\Models\Currency;

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

    public function changeCurrency($old, $new)
    {
        $usdcurrency = Currency::where('code', 'USD')->first();
        $oldcurrency = Currency::where('id', $old)->first();
        $newcurrency = Currency::where('id', $new)->first();

        $rate = 1;

        if ($usdcurrency->id == $oldcurrency->id) {
            $rate = $newcurrency->rate;
        } else if ($usdcurrency->id != $oldcurrency->id) {
            if ($newcurrency->id != $usdcurrency->id) {
                $rate = $usdcurrency->rate / $oldcurrency->rate * $newcurrency->rate;
            } else {
                $rate = $usdcurrency->rate / $oldcurrency->rate;
            }
        }

        DB::table('account_journal')->update([
            'debit' => DB::raw('debit * ' . $rate),
            'credit' => DB::raw('credit * ' . $rate),
        ]);

        DB::table('account_payment')->update([
            'amount' => DB::raw('amount * ' . $rate),
        ]);

        DB::table('account_invoice')->update([
            'total' => DB::raw('total * ' . $rate),
            'amount' => DB::raw('amount * ' . $rate),
        ]);

        DB::table('account_invoice_item')->update([
            'price' => DB::raw('price * ' . $rate),
            'amount' => DB::raw('amount * ' . $rate),
        ]);

        DB::table('account_invoice_item_rate')->update([
            'value' => DB::raw('value * ' . $rate),
        ]);

        DB::table('account_opening_balance')->update([
            'debit' => DB::raw('debit * ' . $rate),
            'credit' => DB::raw('credit * ' . $rate),
        ]);

        DB::table('account_transaction')->update([
            'amount' => DB::raw('amount * ' . $rate),
        ]);
    }
}
