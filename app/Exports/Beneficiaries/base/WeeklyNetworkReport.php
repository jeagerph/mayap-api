<?php

namespace App\Exports\Beneficiaries\base;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Carbon\Carbon;

class WeeklyNetworkReport implements WithMultipleSheets
{
    private $company;
    private $request;
    public function __construct($request, $company)
    {
        $this->company = $company;

        $this->request = $request;
    }

    public function sheets(): array
    {
        $sheets = [];

        $company = $this->company;
        $request = $this->request;

        $dates = [
            'from' => $request->get('from'),
            'to' => $request->get('to')
        ];


        $beneficiaries = $company->beneficiaries()
            ->withCount('parentingNetworks')
            ->where('is_officer', 1)
            ->has('parentingNetworks', '>=', 1)
            ->whereHas('parentingNetworks', function ($query) use ($dates) {
              $query->whereBetween('created_at',  $dates);
          })
            ->orderBy('parenting_networks_count', 'desc')
            ->get();

        $sheets[] = new \App\Exports\Beneficiaries\base\Sheets\WeeklyNetworkSheet([
            'company' => $this->company,
            'request' => $this->request,
            'beneficiaries' => $beneficiaries,
            'dates'=>$dates

        ]);

        return $sheets;
    }
}
