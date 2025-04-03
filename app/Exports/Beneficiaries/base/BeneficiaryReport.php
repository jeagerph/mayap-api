<?php

namespace App\Exports\Beneficiaries\base;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class BeneficiaryReport implements WithMultipleSheets
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

        $beneficiaries = $company->beneficiaries()
            ->select('beneficiaries.*')
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

        return $sheets;
    }
}
