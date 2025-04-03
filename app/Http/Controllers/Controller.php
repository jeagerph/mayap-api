<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function handleQueries($type, $request, $mergeArr = [])
    {
        $requestArr = $request->get($type) ?: [];

        return array_merge($requestArr, $mergeArr);
    }

    public function queryDates($request)
	{
		$from = now()->startOfMonth()->format('Y-m-d') . ' 00:00:00';
		$to = now()->endOfMonth()->format('Y-m-d') . ' 23:59:59';

		if($request->has('from') && $request->has('to')):
			$from = $request->query('from') . ' 00:00:00';
			$to = $request->query('to') . ' 23:59:59';
		endif;

		return ['from' => $from, 'to' => $to];
	}

	public function queryQuarters($request)
	{
		$year = $request->has('year') ? $request->input('year') : now()->format('Y');
		$quarter = $request->has('quarter') ? $request->input('quarter') : now()->quarter;

		if($quarter == 'all') return ['from' => formQuarterDates($year)[1]['from'], 'to' => formQuarterDates($year)[4]['to']];
		else return formQuarterDates($year)[$quarter];
	}

	public function queryStartEndDates($request)
	{
		$year = now()->format('Y');
		$from = $year . '-01-01 00:00:00';
		$to = $year . '-12-31 23:59:59';

		if($request->has('from') && $request->has('to')):
			$from = $request->query('from') . ' 00:00:00';
			$to = $request->query('to') . ' 23:59:59';
		endif;

		return ['from' => $from, 'to' => $to];
	}

	public function queryToday($request)
	{
		$today = now()->format('Y-m-d');
		$from = $today . ' 00:00:00';
		$to = $today . ' 23:59:59';

		if($request->has('from') && $request->has('to')):
			$from = $request->query('from') . ' 00:00:00';
			$to = $request->query('to') . ' 23:59:59';
		endif;

		return ['from' => $from, 'to' => $to];
	}
}
