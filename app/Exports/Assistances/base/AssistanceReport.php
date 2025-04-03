<?php

namespace App\Exports\Assistances\base;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AssistanceReport implements WithMultipleSheets
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

        $assistances = $company->beneficiaryAssistances()
            ->select('beneficiary_assistances.*')
            ->leftJoin('beneficiaries', function($join)
            {
                $join->on('beneficiary_assistances.beneficiary_id', '=', 'beneficiaries.id');
            })
            ->leftJoin('barangays', function($join)
            {
                $join->on('beneficiaries.barangay_id', '=', 'barangays.id');
            })
            ->where(function($q) use ($request)
            {
                if($request->has('filter')):
                    
                    if(isset($request->get('filter')['isAssisted'])):
                        $q->where('is_assisted', $request->get('filter')['isAssisted']);
                    endif;

                    if(isset($request->get('filter')['assistedBy'])):
                        $q->where('assisted_by', 'LIKE', '%'.$request->get('filter')['assistedBy'].'%');
                    endif;

                    if(isset($request->get('filter')['assistanceFrom'])):
                        $q->where('assistance_from', 'LIKE', '%'.$request->get('filter')['assistanceFrom'].'%');
                    endif;

                    if(isset($request->get('filter')['assistanceType'])):
                        $q->where('assistance_type', 'LIKE', '%'.$request->get('filter')['assistanceType'].'%');
                    endif;

                    if (
                        isset($request->get('filter')['benefFirstName']) ||
                        isset($request->get('filter')['benefMiddleName']) ||
                        isset($request->get('filter')['benefLastName']) ||
                        isset($request->get('filter')['benefProvCode']) ||
                        isset($request->get('filter')['benefCityCode']) ||
                        isset($request->get('filter')['benefBarangay']) ||
                        isset($request->get('filter')['benefPurok']) ||
                        isset($request->get('filter')['benefStreet']) ||
                        isset($request->get('filter')['benefZone'])
                    ):
                        $q->whereHas('beneficiary', function($q) use ($request)
                        {
                            if(isset($request->get('filter')['benefFirstName'])):
                                $q->where('first_name', 'LIKE', '%'.$request->get('filter')['benefFirstName'].'%');
                            endif;

                            if(isset($request->get('filter')['benefMiddleName'])):
                                $q->where('middle_name', 'LIKE', '%'.$request->get('filter')['benefMiddleName'].'%');
                            endif;

                            if(isset($request->get('filter')['benefLastName'])):
                                $q->where('last_name', 'LIKE', '%'.$request->get('filter')['benefLastName'].'%');
                            endif;
                            
                            if(isset($request->get('filter')['benefProvCode'])):
                                $q->where('province_id', $request->get('filter')['benefProvCode']);
                            endif;

                            if(isset($request->get('filter')['benefCityCode'])):
                                $q->where('city_id', $request->get('filter')['benefCityCode']);
                            endif;

                            if(isset($request->get('filter')['benefBarangay'])):
                                $q->where('barangay_id', $request->get('filter')['benefBarangay']);
                            endif;

                            if(isset($request->get('filter')['benefPurok'])):
                                $q->where('purok', 'LIKE', '%'.$request->get('filter')['benefPurok'].'%');
                            endif;

                            if(isset($request->get('filter')['benefStreet'])):
                                $q->where('street', 'LIKE', '%'.$request->get('filter')['benefStreet'].'%');
                            endif;

                            if(isset($request->get('filter')['benefZone'])):
                                $q->where('zone', 'LIKE', '%'.$request->get('filter')['benefZone'].'%');
                            endif;
                        });
                    endif;

                endif;
            })
            ->where(function($q) use ($request)
            {
                $q->whereDate('assistance_date', $request->get('from'))
                    ->orWhereDate('assistance_date', $request->get('to'))
                    ->orWhereBetween('assistance_date', [$request->get('from'), $request->get('to')]);
            })
            ->orderBy('barangays.name', 'asc')
            ->get();

        $sheets[] = new \App\Exports\Assistances\base\Sheets\AssistanceSheet([
            'company' => $this->company,
            'request' => $this->request,
            'assistances' => $assistances,

        ]);

        return $sheets;
    }
}
