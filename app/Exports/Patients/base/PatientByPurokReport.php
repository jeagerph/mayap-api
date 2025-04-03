<?php

namespace App\Exports\Patients\base;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PatientByPurokReport implements WithMultipleSheets
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

        $sheets[] = new \App\Exports\Patients\base\Sheets\PatientByPurokSheet([
            'company' => $this->company,
            'request' => $this->request,
            'barangays' => $barangays,

        ]);

        if ($request->get('includeList')):

            $patients = $company->beneficiaryPatients()
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
                'patients' => $patients,

            ]);

            // $currentId = null;

            // $arrBarangayList = [];

            // foreach ($patients as $key => $patient):

            //     $beneficiary = $patient->beneficiary;
            //     $benefBarangay = $beneficiary->barangay;

            //     if ($patient->beneficiary->barangay_id != $currentId):

            //         $currentId = $patient->beneficiary->barangay_id;

            //         $arrBarangayList[$benefBarangay->name]['data'] = [];

            //         $arrBarangayList[$benefBarangay->name]['data'][] = $patient;
            //     else:
            //         $arrBarangayList[$benefBarangay->name]['data'][] = $patient;
            //     endif;

            // endforeach;

            // foreach ($arrBarangayList as $key => $barangay):
            //     $sheets[] = new \App\Exports\Patients\base\Sheets\PatientSheet([
            //         'company' => $this->company,
            //         'request' => $this->request,
            //         'title' => $key,
            //         'patients' => $barangay['data'],
    
            //     ]);
            // endforeach;
            
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
        $sql .= "benef.purok, ";
        $sql .= "(SELECT name FROM provinces WHERE provinces.prov_code = benef.province_id) AS province_name, ";
        $sql .= "(SELECT name FROM cities WHERE cities.city_code = benef.city_id) AS city_name, ";
        $sql .= "(SELECT name FROM barangays WHERE barangays.id = benef.barangay_id) AS barangay_name ";
        $sql .= "FROM beneficiary_patients bPatient ";
        $sql .= "LEFT JOIN beneficiaries benef ON benef.id = bPatient.beneficiary_id ";
        $sql .= "LEFT JOIN barangays brgy ON brgy.id = benef.barangay_id ";
        $sql .= "WHERE (bPatient.company_id = {$company->id} ";

        if (isset($request->get('filter')['firstName'])):
            $sql .= "AND bPatient.first_name LIKE '%{$request->get('filter')['firstName']}%' ";
        endif;

        if (isset($request->get('filter')['middleName'])):
            $sql .= "AND bPatient.middle_name LIKE '%{$request->get('filter')['middleName']}%' ";
        endif;

        if (isset($request->get('filter')['lastName'])):
            $sql .= "AND bPatient.last_name LIKE '%{$request->get('filter')['lastName']}%' ";
        endif;

        if (isset($request->get('filter')['problemPresented'])):
            $sql .= "AND bPatient.problem_presented LIKE '%{$request->get('filter')['problemPresented']}%' ";
        endif;

        if (isset($request->get('filter')['findings'])):
            $sql .= "AND bPatient.findings LIKE '%{$request->get('filter')['findings']}%' ";
        endif;

        if (isset($request->get('filter')['assessmentRecommendation'])):
            $sql .= "AND bPatient.assessment_recommendation LIKE '%{$request->get('filter')['assessmentRecommendation']}%' ";
        endif;

        if (isset($request->get('filter')['needs'])):
            $sql .= "AND bPatient.needs LIKE '%{$request->get('filter')['needs']}%' ";
        endif;

        if (isset($request->get('filter')['remarks'])):
            $sql .= "AND bPatient.remarks LIKE '%{$request->get('filter')['remarks']}%' ";
        endif;

        if (isset($request->get('filter')['relationToPatient'])):
            $sql .= "AND bPatient.relation_to_patient LIKE '%{$request->get('filter')['relationToPatient']}%' ";
        endif;

        if (isset($request->get('filter')['status'])):
            $sql .= "AND bPatient.status = {$request->get('filter')['status']} ";
        endif;

        $sql .= "AND (bPatient.patient_date = '{$from}' OR bPatient.patient_date = '{$to}' OR bPatient.patient_date BETWEEN '{$from}' AND '{$to}') ";
        $sql .= "AND bPatient.deleted_at IS NULL) ";

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
            if (isset($request->get('filter')['benefFirstName'])):
                $sql .= "AND benef.first_name LIKE '%{$request->get('filter')['benefFirstName']}%' ";
            endif;

            if (isset($request->get('filter')['benefMiddleName'])):
                $sql .= "AND benef.middle_name LIKE '%{$request->get('filter')['benefMiddleName']}%' ";
            endif;

            if (isset($request->get('filter')['benefLastName'])):
                $sql .= "AND benef.last_name LIKE '%{$request->get('filter')['benefLastName']}%' ";
            endif;

            if (isset($request->get('filter')['benefProvCode'])):
                $sql .= "AND benef.province_id = {$request->get('filter')['benefProvCode']} ";
            endif;

            if (isset($request->get('filter')['benefCityCode'])):
                $sql .= "AND benef.city_id = {$request->get('filter')['benefCityCode']} ";
            endif;

            if (isset($request->get('filter')['benefBarangay'])):
                $sql .= "AND benef.barangay_id = {$request->get('filter')['benefBarangay']} ";
            endif;
        endif;


        $sql .= "GROUP BY benef.province_id, benef.city_id, benef.barangay_id, benef.purok ";
        $sql .= "ORDER BY brgy.name ASC";

        $data = \DB::select($sql);

        return $data;
    }
}
