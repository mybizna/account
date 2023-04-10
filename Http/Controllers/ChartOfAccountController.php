<?php

namespace Modules\Account\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Account\Classes\ChartOfAccount;
use Modules\Base\Http\Controllers\BaseController;

class ChartOfAccountController extends BaseController
{

    public function chartsummary(Request $request, $slug)
    {
        $chart_of_account = new ChartOfAccount();

        $data = $request->all();

        $start = new \DateTime('now - 1 year');
        $end = new \DateTime();

        $interval = new \DateInterval('P1M');
        $period = new \DatePeriod($start, $interval, $end);

        $data['start_date'] = $start->modify('first day of this month')->format('Y-m-d 00:00:00');
        $data['end_date'] = $end->modify('last day of this month')->format('Y-m-d 23.59.59');
        $data['separator'] = ',';

        $result = $chart_of_account->getChartTotalBySlug($slug, $data);

        $labels = [];
        $r_data = [];
        foreach ($period as $dt) {
            $data['separator'] = '';
            $data['start_date'] = $dt->modify('first day of this month')->format('Y-m-d 00:00:00');
            $data['end_date'] = $dt->modify('last day of this month')->format('Y-m-d 23.59.59');
            $tmp_result = $chart_of_account->getChartTotalBySlug($slug, $data);

            $labels[] = $dt->format('M Y');
            $r_data[] = $tmp_result['total'];
        }

        $data['start_date'] = $end->modify('first day of this month')->format('Y-m-d 00:00:00');
        $data['end_date'] = $end->modify('last day of this month')->format('Y-m-d 23.59.59');
        $cur_result = $chart_of_account->getChartTotalBySlug($slug, $data);

        $labels[] = $end->format('M Y');
        $r_data[] = $cur_result['total'];

        $result['labels'] = $labels;
        $result['data'] = $r_data;
        //print_r($result); exit;

        return response()->json($result);

    }

    public function recordselect(Request $request)
    {
        $type = $request->get('type');

        $result = [
            'module' => 'account',
            'model' => 'chart_of_account',
            'status' => 0,
            'total' => 0,
            'error' => 1,
            'records' => [],
            'message' => 'No Records',
        ];

        $query = DB::table('account_chart_of_account');

        if ($type == 'left') {
            $query->where('slug', 'asset')
                ->orWhere('slug', 'income');
        } else {
            $query->where('slug', 'liability')
                ->orWhere('slug', 'equity')
                ->orWhere('slug', 'expense');
        }

        try {
            $records = $query->get();
            $list = collect();
            $list->push(['value' => '', 'label' => '--- Please Select ---']);

            foreach ($records as $key => $record) {
                $list->push(['value' => $record->id, 'label' => $record->name]);
            }

            $result['error'] = 0;
            $result['status'] = 1;
            $result['records'] = $list;
            $result['message'] = 'Records Found Successfully.';
        } catch (\Throwable$th) {
            //throw $th;
        }

        return response()->json($result);
    }
}
