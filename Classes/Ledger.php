<?php

namespace Modules\Account\Classes;

use Illuminate\Support\Facades\Cache;
use Modules\Account\Entities\Journal as DBJournal;
use Modules\Account\Entities\Ledger as DBLedger;

class Ledger
{
    public function getLedger($ledger_id)
    {
        if (Cache::has("account_ledger_" . $ledger_id)) {
            $ledger = Cache::get("account_ledger_" . $ledger_id);
            return $ledger;
        } else {
            try {
                $query = $this->getLedgerQuery();
                $ledger = $query->where('al.id', $ledger_id)->first();

                Cache::put("account_ledger_" . $ledger_id, $ledger, 3600);
                //code...
                return $ledger;
            } catch (\Throwable$th) {
                throw $th;
            }
        }

        return false;
    }

    public function getLedgerById($ledger_id)
    {
        return $this->getLedger($ledger_id);
    }

    public function getLedgerBySlug($ledger_slug)
    {
        Cache::forget("account_ledger_" . $ledger_slug);

        if (Cache::has("account_ledger_" . $ledger_slug)) {
            $ledger = Cache::get("account_ledger_" . $ledger_slug);
            return $ledger;
        } else {
            try {
                console_log($ledger_slug);
                $query = $this->getLedgerQuery();
                $ledger = $query->where('al.slug', $ledger_slug)->first();
               
                Cache::put("account_ledger_" . $ledger_slug, $ledger, 3600);
                
                return $ledger;
                //code...
            } catch (\Throwable$th) {
                throw $th;
            }

            return false;
        }
    }

    public function getLedgerQuery()
    {
        return DBLedger::from('account_ledger AS al')
            ->select('al.*', 'ac.slug AS chart_slug')
            ->leftJoin('account_chart_of_account AS ac', 'ac.id', '=', 'al.chart_id');
    }

    public function getLedgerId($ledger_slug)
    {
        if (Cache::has("account_ledger_" . $ledger_slug . "_id")) {
            $ledger_id = Cache::get("account_ledger_" . $ledger_slug . "_id");
            return $ledger_id;
        } else {
            try {
                $ledger = DBLedger::where('slug', $ledger_slug)->first();
                $ledger_id = $ledger->id;
                Cache::put("account_ledger_" . $ledger_slug . "_id", $ledger->id, 3600);

                return $ledger_id;
                //code...
            } catch (\Throwable$th) {
                throw $th;
            }
        }
        return false;
    }

    public function getLedgerTotal($ledger_id, $partner_id = '', $payment_id = '')
    {

        if (Cache::has("account_ledger_total_" . $ledger_id . '_' . $partner_id . '_' . $payment_id)) {
            $ledger_total = Cache::get("account_ledger_total_" . $ledger_id . '_' . $partner_id . '_' . $payment_id);
            return $ledger_total;
        } else {
            try {
                $ledger = $this->getLedger($ledger_id);
                $query = DBJournal::where('ledger_id', $ledger_id);

                if ($partner_id) {
                    $query->where('partner_id', $partner_id);
                }

                if ($payment_id) {
                    $query->where('payment_id', $payment_id);
                }

                $debit = $query->sum('debit');
                $credit = $query->sum('credit');
                $total = $credit - $debit;

                if ($ledger->chart_slug == 'asset' || $ledger->chart_slug == 'expense') {
                    $total = $debit - $credit;
                }

                Cache::put("account_ledger_total_" . $ledger_id . '_' . $partner_id . '_' . $payment_id, [
                    'debit' => $debit,
                    'credit' => $credit,
                    'total' => $total,
                ], 3600);

                return [
                    'debit' => $debit,
                    'credit' => $credit,
                    'total' => $total,
                ];
            } catch (\Throwable$th) {
                throw $th;
            }
        }

        return false;
    }

    public function getAccountBalance($partner_id)
    {
        // Get wallet total
        $wallet_id = $this->getLedgerId('wallet');
        $wallet_ledger = $this->getLedgerTotal($wallet_id, $partner_id);
        $wallet_total = (isset($wallet_ledger['total'])) ? $wallet_ledger['total'] : 0;

        // Get receivable total
        $receivable_id = $this->getLedgerId('accounts_receivable');
        $receivable_ledger = $this->getLedgerTotal($receivable_id, $partner_id);
        $receivable_total = (isset($receivable_ledger['total'])) ? $receivable_ledger['total'] : 0;

        $balance = $wallet_total - $receivable_total;

        return [
            'receivable' => $receivable_total,
            'wallet' => $wallet_total,
            'balance' => $balance,
        ];
    }
}
