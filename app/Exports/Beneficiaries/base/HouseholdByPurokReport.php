<?php

namespace App\Exports\Beneficiaries\base;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class HouseholdByPurokReport implements WithMultipleSheets
{
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

        $puroks = $this->getSummary($request, $company);

        $sheets[] = new \App\Exports\Beneficiaries\base\Sheets\HouseholdByPurokSheet([
            'company' => $this->company,
            'request' => $this->request,
            'puroks' => $puroks,

        ]);

        if ($request->get('includeList')):

            $beneficiaries = $company->beneficiaries()
                                    ->where('is_household', 1)
                                    ->where(function($q) use ($request)
                                    {
                                        if($request->has('filter')):

                                            if(isset($request->get('filter')['provCode'])):
                                                $q->where('province_id', $request->get('filter')['provCode']);
                                            endif;

                                            if(isset($request->get('filter')['cityCode'])):
                                                $q->where('city_id', $request->get('filter')['cityCode']);
                                            endif;

                                            if(isset($request->get('filter')['barangay'])):
                                                $q->where('barangay_id', $request->get('filter')['barangay']);
                                            endif;
                                        endif;
                                    })
                                    ->where(function($q) use ($request)
                                    {
                                        $q->whereDate('date_registered', $request->get('from'))
                                            ->orWhereDate('date_registered', $request->get('to'))
                                            ->orWhereBetween('date_registered', [$request->get('from'), $request->get('to')]);
                                    })
                                    // ->orderBy('is_priority', 'desc')
                                    ->orderBy('last_name', 'asc')
                                    ->orderBy('first_name', 'asc')
                                    ->orderBy('created_at', 'desc')
                                    ->get();

            $sheets[] = new \App\Exports\Beneficiaries\base\Sheets\BeneficiarySheet([
                'company' => $this->company,
                'request' => $this->request,
                'beneficiaries' => $beneficiaries,
    
            ]);
            
        endif;

        return $sheets;
    }

    private function getSummary($request, $company)
    {
        $from = (new \Carbon\Carbon($request->get('from')))->format('Y-m-d');
        $to = (new \Carbon\Carbon($request->get('to')))->format('Y-m-d');
        
        $sql = "SELECT ";
        $sql .= "COALESCE(COUNT(*), 0) AS total, ";
        $sql .= "benef.purok AS purok, ";
        $sql .= "benef.province_id, ";
        $sql .= "benef.city_id, ";
        $sql .= "benef.barangay_id, ";
        $sql .= "(SELECT name FROM provinces WHERE provinces.prov_code = benef.province_id) AS province_name, ";
        $sql .= "(SELECT name FROM cities WHERE cities.city_code = benef.city_id) AS city_name, ";
        $sql .= "(SELECT name FROM barangays WHERE barangays.id = benef.barangay_id) AS barangay_name ";
        $sql .= "FROM beneficiaries benef ";
        $sql .= "LEFT JOIN barangays brgy ON brgy.id = benef.barangay_id ";
        $sql .= "WHERE benef.company_id = {$company->id} ";
        $sql .= "AND benef.is_household = 1 ";

        if (isset($request->get('filter')['provCode'])):
            $sql .= "AND benef.province_id = {$request->get('filter')['provCode']} ";
        endif;

        if (isset($request->get('filter')['cityCode'])):
            $sql .= "AND benef.city_id = {$request->get('filter')['cityCode']} ";
        endif;

        if (isset($request->get('filter')['barangay'])):
            $sql .= "AND benef.barangay_id = {$request->get('filter')['barangay']} ";
        endif;

        $sql .= "AND (benef.date_registered = '{$from}' OR benef.date_registered = '{$to}' OR benef.date_registered BETWEEN '{$from}' AND '{$to}') ";
        $sql .= "AND benef.deleted_at IS NULL ";
        $sql .= "GROUP BY benef.province_id, benef.city_id, benef.barangay_id, benef.purok ";
        $sql .= "ORDER BY brgy.name ASC, total DESC";

        $data = \DB::select($sql);

        return $data;
    }
}
