<?php

namespace Modules\Account\Classes;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class Ledger
{
    public function getLedger($ledger_id)
    {
        if (Cache::has("account_ledger_" + $ledger_id)) {
            $ledger = Cache::get("account_ledger_" + $ledger_id);
            return $ledger;
        } else {
            try {
                $query = $this->getLedgerQuery();
                $ledger = $query->where('al.id', $ledger_id)->first();

                Cache::put("account_ledger_" + $ledger_id, $ledger);
                //code...
                return $ledger;
            } catch (\Throwable $th) {
                return false;
                //throw $th;
            }
        }

        return false;
    }

    public function getLedgerById($ledger_id)
    {
        return  $this->getLedger($ledger_id);
    }

    public function getLedgerBySlug($ledger_slug)
    {
        if (Cache::has("account_ledger_" + $ledger_slug)) {
            $ledger = Cache::get("account_ledger_" + $ledger_slug);
            return $ledger;
        } else {
            try {
                $query = $this->getLedgerQuery();
                $ledger = $query->where('al.slug', $ledger_slug)->first();
                $ledger = Cache::put("account_ledger_" + $ledger_slug, $ledger);
                return $ledger;
                //code...
            } catch (\Throwable $th) {
                return false;
                //throw $th;
            }

            return false;
        }
    }

    public function getLedgerQuery()
    {
        return DB::table('account_ledger AS al')
            ->select('al.*, ac.slug AS chart_slug')
            ->leftJoin('account_chart_of_account AS ac', 'ac.id', '=', 'al.chart_id');
    }


    public function getLedgerId($ledger_slug)
    {
        if (Cache::has("account_ledger_" + $ledger_slug + "_id")) {
            $ledger_id = Cache::get("account_ledger_" + $ledger_slug + "_id");
        } else {
            try {
                $ledger = DB::table('account_ledger')->where('slug', $ledger_slug)->first();
                $ledger_id = $ledger->id;
                Cache::put("account_ledger_" + $ledger_slug + "_id", $ledger->id);

                return $ledger_id;
                //code...
            } catch (\Throwable $th) {
                throw $th;
            }
        }
        return false;
    }

    public function getLedgerTotal($ledger_id, $partner_id = '')
    {

        if (Cache::has("account_ledger_total_" + $ledger_id + '_' + $partner_id)) {
            $ledger_total = Cache::get("account_ledger_total_" + $ledger_id + '_' + $partner_id);
        } else {
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

                Cache::put("account_ledger_total_" + $ledger_id + '_' + $partner_id, [
                    'debit' => $debit,
                    'credit' => $credit,
                    'total' => $total,
                ]);
            } catch (\Throwable $th) {
                throw $th;
            }
        }

        return false;
    }
}
