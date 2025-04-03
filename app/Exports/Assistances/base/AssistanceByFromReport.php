<?php

namespace App\Exports\Assistances\base;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AssistanceByFromReport implements WithMultipleSheets
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

        $assistancesFrom = $this->getSummary($request, $company);

        $sheets[] = new \App\Exports\Assistances\base\Sheets\AssistanceByFromSheet([
            'company' => $this->company,
            'request' => $this->request,
            'assistancesFrom' => $assistancesFrom,

        ]);

        if ($request->get('includeList')):

            $assistances = $company->beneficiaryAssistances()
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

                                            if(isset($request->get('filter')['remarks'])):
                                                $q->where('remarks', 'LIKE', '%'.$request->get('filter')['remarks'].'%');
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
                ->orderBy('assistance_date', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();

            $sheets[] = new \App\Exports\Assistances\base\Sheets\AssistanceSheet([
                'company' => $this->company,
                'request' => $this->request,
                'assistances' => $assistances,

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
        $sql .= "bAssistance.assistance_from AS assistanceFrom, ";
        $sql .= "COALESCE(SUM(CASE WHEN bAssistance.is_assisted = 0 THEN 1 END), 0) AS requested, ";
        $sql .= "COALESCE(SUM(CASE WHEN bAssistance.is_assisted = 1 THEN 1 END), 0) AS assisted ";
        $sql .= "FROM beneficiary_assistances bAssistance ";
        $sql .= "LEFT JOIN beneficiaries benef ON benef.id = bAssistance.beneficiary_id ";
        $sql .= "WHERE (bAssistance.company_id = {$company->id} ";

        if (isset($request->get('filter')['isAssisted'])):
            $sql .= "AND bAssistance.is_assisted = {$request->get('filter')['isAssisted']} ";
        endif;

        if (isset($request->get('filter')['assistedBy'])):
            $sql .= "AND bAssistance.assisted_by LIKE '%{$request->get('filter')['assistedBy']}%' ";
        endif;

        if (isset($request->get('filter')['assistanceFrom'])):
            $sql .= "AND bAssistance.assistance_from LIKE '%{$request->get('filter')['assistanceFrom']}%' ";
        endif;

        if (isset($request->get('filter')['assistanceType'])):
            $sql .= "AND bAssistance.assistance_type LIKE '%{$request->get('filter')['assistanceType']}%' ";
        endif;

        if (isset($request->get('filter')['remarks'])):
            $sql .= "AND bAssistance.remarks LIKE '%{$request->get('filter')['remarks']}%' ";
        endif;

        $sql .= "AND (bAssistance.assistance_date = '{$from}' OR bAssistance.assistance_date = '{$to}' OR bAssistance.assistance_date BETWEEN '{$from}' AND '{$to}') ";
        $sql .= "AND bAssistance.deleted_at IS NULL) ";

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


        $sql .= "GROUP BY bAssistance.assistance_from ";
        $sql .= "ORDER BY total DESC, assistanceFrom ASC";

        $data = \DB::select($sql);

        return $data;
    }
}
