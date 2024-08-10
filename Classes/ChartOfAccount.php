<?php
namespace Modules\Account\Classes;

use Illuminate\Support\Facades\Cache;
use Modules\Account\Entities\ChartOfAccount as DBChartOfAccount;
use Modules\Account\Entities\Journal as DBJournal;

class ChartOfAccount
{
    /**
     * Get Chart of Account
     *
     * @param int $chart_id
     *
     * @return DBChartOfAccount
     */
    public function getChart($chart_id)
    {

        if (Cache::has("account_chart_" . $chart_id)) {
            $chart = Cache::get("account_chart_" . $chart_id);
            return $chart;
        } else {
            try {
                $chart = DBChartOfAccount::where('id', $chart_id)->first();

                if (empty($chart)) {
                    throw new \Exception("Chart of Account not found", 1);
                }
                Cache::put("account_chart_" . $chart_id, $chart, 3600);
                return $chart;
            } catch (\Throwable $th) {
                throw $th;
            }
        }

    }

    /**
     * Get Chart of Account by ID
     *
     * @param int $chart_id
     *
     * @return DBChartOfAccount
     */
    public function getChartById($chart_id)
    {
        return $this->getChart($chart_id);
    }

    /**
     * Get Chart of Account by Slug
     *
     * @param string $slug
     *
     * @return int
     */
    public function getChartId($slug)
    {
        if (Cache::has("account_chart_" . $slug . "_id")) {
            $chart_id = Cache::get("account_chart_" . $slug . "_id");
            return intval($chart_id);
        } else {
            try {
                $chart = DBChartOfAccount::where('slug', $slug)->first();

                $chart_id = intval($chart->id);

                Cache::put("account_chart_" . $slug . "_id", $chart->id, 3600);

                return intval($chart_id);
                //code...
            } catch (\Throwable $th) {
                throw $th;
            }
        }
    }

    /**
     * Get Chart of Account by Slug
     *
     * @param string $slug
     * @param array<string> $data
     *
     * @return array<string,float>
     */
    public function getChartTotalBySlug($slug, $data)
    {
        $chart_id = $this->getChartId($slug);

        return $this->getChartTotal($chart_id, $data);
    }

    /**
     * Get Chart of Account total
     *
     * @param int $chart_id
     * @param array<string,int> $data
     *
     * @return array<string,float>
     */
    public function getChartTotal($chart_id, $data)
    {
        if (Cache::has("account_chart_total_" . $chart_id) && empty($data)) {
            $chart_total = Cache::get("account_chart_total_" . $chart_id);
            return (array) $chart_total;
        } else {
            try {

                $separator = (isset($data['separator'])) ? $data['separator'] : '';

                $chart = $this->getChart($chart_id);

                $query = DBJournal::from('account_journal AS aj')
                    ->where('al.chart_id', $chart_id)
                    ->leftJoin('account_ledger AS al', 'al.id', '=', 'aj.ledger_id');

                if (isset($data['start_date']) && $data['start_date'] != '' && isset($data['end_date']) && $data['end_date'] != '') {
                    $query->whereBetween('aj.created_at', [$data['start_date'], $data['end_date']]);
                } elseif (isset($data['end_date']) && $data['end_date'] != '') {
                    $query->whereDate('aj.created_at', '>=', $data['end_date']);
                } elseif (isset($data['start_date']) && $data['start_date'] != '') {
                    $query->whereDate('aj.created_at', '<=', $data['start_date']);
                }

                $debit = $query->sum('aj.debit');
                $credit = $query->sum('aj.credit');
                $total = $credit - $debit;

                if ($chart->slug == 'asset' || $chart->slug == 'expense') {
                    $total = $debit - $credit;
                }

                $result = [
                    'debit' => number_format($debit, 2, '.', $separator),
                    'credit' => number_format($credit, 2, '.', $separator),
                    'total' => number_format($total, 2, '.', $separator),
                ];

                Cache::put("account_chart_total_" . $chart_id, $result, 3600);

                return $result;
            } catch (\Throwable $th) {
                throw $th;
            }
        }

    }

}
