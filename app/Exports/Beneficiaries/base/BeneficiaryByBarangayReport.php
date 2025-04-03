<?php

namespace App\Exports\Beneficiaries\base;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class BeneficiaryByBarangayReport implements WithMultipleSheets
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

        $barangays = $this->getSummary($request, $company);

        $sheets[] = new \App\Exports\Beneficiaries\base\Sheets\BeneficiaryByBarangaySheet([
            'company' => $this->company,
            'request' => $this->request,
            'barangays' => $barangays,

        ]);

        if ($request->get('includeList')):

            $beneficiaries = $company->beneficiaries()
                ->leftJoin('barangays', function($join)
                {
                    $join->on('beneficiaries.barangay_id', '=', 'barangays.id');
                })
                ->where(function($q) use ($request)
                {
                    if($request->has('filter')):
                        
                        if(isset($request->get('filter')['isHousehold'])):
                            $q->where('is_household', $request->get('filter')['isHousehold']);
                        endif;
                        
                        if(isset($request->get('filter')['isPriority'])):
                            $q->where('is_priority', $request->get('filter')['isPriority']);
                        endif;
                        
                        if(isset($request->get('filter')['isOfficer'])):
                            $q->where('is_officer', $request->get('filter')['isOfficer']);
                        endif;
                        
                        if(isset($request->get('filter')['voterType'])):
                            $q->where('voter_type', $request->get('filter')['voterType']);
                        endif;
                        
                        if(isset($request->get('filter')['gender'])):
                            $q->where('gender', $request->get('filter')['gender']);
                        endif;

                        if(isset($request->get('filter')['provCode'])):
                            $q->where('province_id', $request->get('filter')['provCode']);
                        endif;

                        if(isset($request->get('filter')['cityCode'])):
                            $q->where('city_id', $request->get('filter')['cityCode']);
                        endif;

                        if(isset($request->get('filter')['barangay'])):
                            $q->where('barangay_id', $request->get('filter')['barangay']);
                        endif;

                        if(isset($request->get('filter')['purok'])):
                            $q->where('purok', 'LIKE', '%'.$request->get('filter')['purok'].'%');
                        endif;

                        if(isset($request->get('filter')['street'])):
                            $q->where('street', 'LIKE', '%'.$request->get('filter')['street'].'%');
                        endif;

                        if(isset($request->get('filter')['zone'])):
                            $q->where('zone', 'LIKE', '%'.$request->get('filter')['zone'].'%');
                        endif;

                        if(isset($request->get('filter')['age'])):
                            $arrAgeRange = explode(',', $request->get('filter')['age']);

                            $minDate = \Carbon\Carbon::today()->subYears($arrAgeRange[0])->format('Y');
                            $maxDate = \Carbon\Carbon::today()->subYears($arrAgeRange[1])->format('Y');

                            $q->whereBetween(\DB::raw('YEAR(date_of_birth)'), [$maxDate, $minDate]);
                            
                        endif;

                        if(isset($request->get('filter')['hasNetwork'])):
                            
                            $q->has('parentingNetworks', '>=', 1);
                            
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
                ->orderBy('barangays.name', 'asc')
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
        $sql .= "benef.province_id, ";
        $sql .= "benef.city_id, ";
        $sql .= "benef.barangay_id, ";
        $sql .= "(SELECT name FROM provinces WHERE provinces.prov_code = benef.province_id) AS province_name, ";
        $sql .= "(SELECT name FROM cities WHERE cities.city_code = benef.city_id) AS city_name, ";
        $sql .= "(SELECT name FROM barangays WHERE barangays.id = benef.barangay_id) AS barangay_name ";
        $sql .= "FROM beneficiaries benef ";
        $sql .= "LEFT JOIN barangays brgy ON brgy.id = benef.barangay_id ";
        $sql .= "WHERE benef.company_id = {$company->id} ";

        if (isset($request->get('filter')['isHousehold'])):
            $sql .= "AND benef.is_household = {$request->get('filter')['isHousehold']} ";
        endif;

        if (isset($request->get('filter')['isOfficer'])):
            $sql .= "AND benef.is_officer = {$request->get('filter')['isOfficer']} ";
        endif;

        if (isset($request->get('filter')['voterType'])):
            $sql .= "AND benef.voter_type = {$request->get('filter')['voterType']} ";
        endif;

        if (isset($request->get('filter')['gender'])):
            $sql .= "AND benef.gender = {$request->get('filter')['gender']} ";
        endif;

        if (isset($request->get('filter')['provCode'])):
            $sql .= "AND benef.province_id = {$request->get('filter')['provCode']} ";
        endif;

        if (isset($request->get('filter')['cityCode'])):
            $sql .= "AND benef.city_id = {$request->get('filter')['cityCode']} ";
        endif;

        if (isset($request->get('filter')['barangay'])):
            $sql .= "AND benef.barangay_id = {$request->get('filter')['barangay']} ";
        endif;

        if (isset($request->get('filter')['purok'])):
            $sql .= "AND benef.purok = {$request->get('filter')['purok']} ";
        endif;

        if (isset($request->get('filter')['street'])):
            $sql .= "AND benef.street = {$request->get('filter')['street']} ";
        endif;

        if (isset($request->get('filter')['zone'])):
            $sql .= "AND benef.zone = {$request->get('filter')['zone']} ";
        endif;

        if (isset($request->get('filter')['age'])):
            $arrAgeRange = explode(',', $request->get('filter')['age']);

            $minDate = \Carbon\Carbon::today()->subYears($arrAgeRange[0])->format('Y');
            $maxDate = \Carbon\Carbon::today()->subYears($arrAgeRange[1])->format('Y');

            $sql .= "AND YEAR(benef.date_of_birth) BETWEEN '{$maxDate}' AND '{$minDate}' ";
        endif;

        $sql .= "AND (benef.date_registered = '{$from}' OR benef.date_registered = '{$to}' OR benef.date_registered BETWEEN '{$from}' AND '{$to}') ";
        $sql .= "AND benef.deleted_at IS NULL ";
        $sql .= "GROUP BY benef.province_id, benef.city_id, benef.barangay_id ";
        $sql .= "ORDER BY brgy.name ASC";

        $data = \DB::select($sql);

        return $data;
    }
}
