<?php

namespace App\Exports\Patients\base;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PatientReport implements WithMultipleSheets
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

        $patients = $company->beneficiaryPatients()
                                    ->select('beneficiary_patients.*')
                                    ->leftJoin('beneficiaries', function($join)
                                    {
                                        $join->on('beneficiary_patients.beneficiary_id', '=', 'beneficiaries.id');
                                    })
                                    ->leftJoin('barangays', function($join)
                                    {
                                        $join->on('beneficiaries.barangay_id', '=', 'barangays.id');
                                    })
                                    ->where(function($q) use ($request)
                                    {
                                        if($request->has('filter')):
                                            
                                            if(isset($request->get('filter')['firstName'])):
                                                $q->where('first_name', 'LIKE', '%'.$request->get('filter')['firstName'].'%');
                                            endif;

                                            if(isset($request->get('filter')['middleName'])):
                                                $q->where('middle_name', 'LIKE', '%'.$request->get('filter')['middleName'].'%');
                                            endif;

                                            if(isset($request->get('filter')['lastName'])):
                                                $q->where('last_name', 'LIKE', '%'.$request->get('filter')['lastName'].'%');
                                            endif;

                                            if(isset($request->get('filter')['problemPresented'])):
                                                $q->where('problem_presented', 'LIKE', '%'.$request->get('filter')['problemPresented'].'%');
                                            endif;

                                            if(isset($request->get('filter')['findings'])):
                                                $q->where('findings', 'LIKE', '%'.$request->get('filter')['findings'].'%');
                                            endif;

                                            if(isset($request->get('filter')['assessmentRecommendation'])):
                                                $q->where('assessment_recommendation', 'LIKE', '%'.$request->get('filter')['assessmentRecommendation'].'%');
                                            endif;

                                            if(isset($request->get('filter')['needs'])):
                                                $q->where('needs', 'LIKE', '%'.$request->get('filter')['needs'].'%');
                                            endif;

                                            if(isset($request->get('filter')['remarks'])):
                                                $q->where('remarks', 'LIKE', '%'.$request->get('filter')['remarks'].'%');
                                            endif;

                                            if(isset($request->get('filter')['status'])):
                                                $q->where('status', $request->get('filter')['status']);
                                            endif;

                                            if(isset($request->get('filter')['relationToPatient'])):
                                                $q->where('relation_to_patient', 'LIKE', '%'.$request->get('filter')['relationToPatient'].'%');
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
                                        $q->whereDate('patient_date', $request->get('from'))
                                            ->orWhereDate('patient_date', $request->get('to'))
                                            ->orWhereBetween('patient_date', [$request->get('from'), $request->get('to')]);
                                    })
                                    ->orderBy('barangays.name', 'asc')
                                    ->get();

        $sheets[] = new \App\Exports\Patients\base\Sheets\PatientSheet([
            'company' => $this->company,
            'request' => $this->request,
            'title' => 'PATIENT LIST',
            'patients' => $patients,

        ]);

        return $sheets;
    }
}
