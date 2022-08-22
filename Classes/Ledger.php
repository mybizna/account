<?php

namespace Modules\Account\Classes;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class Ledger
{
    public function getLedger($ledger_id)
    {

        $ledger = Cache::get("account_ledger_" + $ledger_id);

        try {
            if (!$ledger) {
                $query = $this->getLedgerQuery();
                $query->where('al.id', $ledger_id)->first();

                Cache::set("account_ledger_" + $ledger_id, $ledger);
            }
            //code...
        } catch (\Throwable $th) {
            return false;
            //throw $th;
        }

        return $ledger;
    }

    public function getLedgerById($ledger_id)
    {
        return  $this->getLedger($ledger_id);
    }

    public function getLedgerBySlug($ledger_slug)
    {
        $ledger = Cache::get("account_ledger_" + $ledger_slug);

        try {
            if (!$ledger) {
                $query = $this->getLedgerQuery();
                $query->where('al.slug', $ledger_slug)->first();

                Cache::set("account_ledger_" + $ledger_slug, $ledger);
            }
            //code...
        } catch (\Throwable $th) {
            return false;
            //throw $th;
        }

        return $ledger;
    }

    public function getLedgerQuery()
    {

        return DB::table('account_ledger AS al')
            ->select('al.*, ac.slug AS chart_slug')
            ->leftJoin('account_chart_of_account AS ac', 'ac.id', '=', 'al.chart_id');
    }


    public function getLedgerId($ledger_slug)
    {

        $ledger_id = Cache::get("account_ledger_" + $ledger_slug + "_id");

        try {
            if (!$ledger_id) {
                $ledger = DB::table('account_ledger')->where('slug', $ledger_slug)->first();

                $ledger_id = $ledger->id;
                Cache::set("account_ledger_" + $ledger_slug + "_id", $ledger->id);
            }
            //code...
        } catch (\Throwable $th) {
            throw $th;
        }

        return $ledger_id;
    }

    public function getLedgerTotal($ledger_id, $partner_id = '')
    {
        $ledger_total = Cache::get("account_ledger_total_" + $ledger_id + '_' + $partner_id);

        try {
            $ledger = $this->getLedger($ledger_id);
            $query =  DB::table('account_journal');

            $query->where('ledger_id', $ledger_id);

            if ($partner_id) {
                $query->where('partner_id', $partner_id);
            }

            $debit = $query->sum('debit');
            $credit = $query->sum('credit');
            $total =   $credit - $debit;

            if ($ledger->chart_slug == 'asset' || $ledger->chart_slug == 'expense') {
                $total = $debit - $credit;
            }

            Cache::set("account_ledger_total_" + $ledger_id + '_' + $partner_id, [
                'debit' => $debit,
                'credit' => $credit,
                'total' => $total,
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }

        return  $ledger_total;
    }
}
