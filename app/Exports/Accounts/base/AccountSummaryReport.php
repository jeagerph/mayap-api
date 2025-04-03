<?php

namespace App\Exports\Accounts\base;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

use App\Http\Repositories\Base\CompanyRepository;

class AccountSummaryReport implements WithMultipleSheets
{
    public function __construct($request, $companyAccount)
    {
        $this->companyAccount = $companyAccount;

        $this->request = $request;
    }

    public function sheets(): array
    {
        $sheets = [];

        $companyAccount = $this->companyAccount;
        $account = $companyAccount->account;
        $request = $this->request;

        $dates = [
            'from' => $request->get('from'),
            'to' => $request->get('to')
        ];

        $summaryDates = $this->getSummary($request, $companyAccount);

        $sheets[] = new \App\Exports\Accounts\base\Sheets\DateSummarySheet([
            'companyAccount' => $companyAccount,
            'company' => $companyAccount->company,
            'request' => $this->request,
            'summary' => $summaryDates,
        ]);


        if ($request->get('includeBeneficiaries')):

            $beneficiaries = $this->getBeneficiaries($request, $companyAccount);

            $sheets[] = new \App\Exports\Accounts\base\Sheets\BeneficiarySheet([
                'companyAccount' => $companyAccount,
                'company' => $companyAccount->company,
                'request' => $this->request,
                'beneficiaries' => $beneficiaries,
            ]);
        endif;


        if ($request->get('includeAssistances')):

            $assistances = $this->getAssistances($request, $companyAccount);

            $sheets[] = new \App\Exports\Accounts\base\Sheets\AssistanceSheet([
                'companyAccount' => $companyAccount,
                'company' => $companyAccount->company,
                'request' => $this->request,
                'assistances' => $assistances,
            ]);
        endif;

        if ($request->get('includePatients')):

            $patients = $this->getPatients($request, $companyAccount);

            $sheets[] = new \App\Exports\Accounts\base\Sheets\PatientSheet([
                'companyAccount' => $companyAccount,
                'company' => $companyAccount->company,
                'request' => $this->request,
                'patients' => $patients,
            ]);
        endif;

        return $sheets;
    }

    private function getSummary($request, $companyAccount)
    {
        $account = $companyAccount->account;

        $from = (new \Carbon\Carbon($request->get('from')))->format('Y-m-d');
        $to = (new \Carbon\Carbon($request->get('to')))->format('Y-m-d');
        
        $beneficiarySql = "SELECT ";
        $beneficiarySql .= "benef.date_registered AS date, ";
        $beneficiarySql .= "COALESCE(COUNT(*), 0) AS total ";
        $beneficiarySql .= "FROM beneficiaries benef ";
        $beneficiarySql .= "WHERE benef.created_by = {$account->user_id} ";
        $beneficiarySql .= "AND (benef.date_registered = '{$from}' OR benef.date_registered = '{$to}' OR benef.date_registered BETWEEN '{$from}' AND '{$to}') ";
        $beneficiarySql .= "AND benef.deleted_at IS NULL ";
        $beneficiarySql .= "GROUP BY benef.date_registered ";
        $beneficiarySql .= "ORDER BY benef.date_registered ASC";

        $beneficiaryData = \DB::select($beneficiarySql);

        $assistanceSql = "SELECT ";
        $assistanceSql .= "assistance.assistance_date AS date, ";
        $assistanceSql .= "COALESCE(COUNT(*), 0) AS total ";
        $assistanceSql .= "FROM beneficiary_assistances assistance ";
        $assistanceSql .= "WHERE assistance.created_by = {$account->user_id} ";
        $assistanceSql .= "AND (assistance.assistance_date = '{$from}' OR assistance.assistance_date = '{$to}' OR assistance.assistance_date BETWEEN '{$from}' AND '{$to}') ";
        $assistanceSql .= "AND assistance.deleted_at IS NULL ";
        $assistanceSql .= "GROUP BY assistance.assistance_date ";
        $assistanceSql .= "ORDER BY assistance.assistance_date ASC";

        $assistanceData = \DB::select($assistanceSql);

        $patientSql = "SELECT ";
        $patientSql .= "patient.patient_date AS date, ";
        $patientSql .= "COALESCE(COUNT(*), 0) AS total ";
        $patientSql .= "FROM beneficiary_patients patient ";
        $patientSql .= "WHERE patient.created_by = {$account->user_id} ";
        $patientSql .= "AND (patient.patient_date = '{$from}' OR patient.patient_date = '{$to}' OR patient.patient_date BETWEEN '{$from}' AND '{$to}') ";
        $patientSql .= "AND patient.deleted_at IS NULL ";
        $patientSql .= "GROUP BY patient.patient_date ";
        $patientSql .= "ORDER BY patient.patient_date ASC";

        $patientData = \DB::select($patientSql);


        return [
            'beneficiaries' => $beneficiaryData,
            'assistances' => $assistanceData,
            'patients' => $patientData,
        ];
    }

    private function getBeneficiaries($request, $companyAccount)
    {
        $company = $companyAccount->company;

        return $company->beneficiaries()
            ->select('beneficiaries.*')
            ->leftJoin('barangays', function($join)
            {
                $join->on('beneficiaries.barangay_id', '=', 'barangays.id');
            })
            ->where('beneficiaries.created_by', $companyAccount->account->user_id)
            ->where(function($q) use ($request)
            {
                $q->whereDate('date_registered', $request->get('from'))
                    ->orWhereDate('date_registered', $request->get('to'))
                    ->orWhereBetween('date_registered', [$request->get('from'), $request->get('to')]);
            })
            // ->orderBy('is_priority', 'desc')
            ->orderBy('barangays.name', 'asc')
            ->get();
    }

    private function getAssistances($request, $companyAccount)
    {
        $company = $companyAccount->company;

        return $company->beneficiaryAssistances()
            ->select('beneficiary_assistances.*')
            ->leftJoin('beneficiaries', function($join)
            {
                $join->on('beneficiary_assistances.beneficiary_id', '=', 'beneficiaries.id');
            })
            ->leftJoin('barangays', function($join)
            {
                $join->on('beneficiaries.barangay_id', '=', 'barangays.id');
            })
            ->where('beneficiary_assistances.created_by', $companyAccount->account->user_id)
            ->where(function($q) use ($request)
            {
                $q->whereDate('assistance_date', $request->get('from'))
                    ->orWhereDate('assistance_date', $request->get('to'))
                    ->orWhereBetween('assistance_date', [$request->get('from'), $request->get('to')]);
            })
            ->orderBy('barangays.name', 'asc')
            ->get();
    }

    private function getPatients($request, $companyAccount)
    {
        $company = $companyAccount->company;

        return $company->beneficiaryPatients()
            ->select('beneficiary_patients.*')
            ->leftJoin('beneficiaries', function($join)
            {
                $join->on('beneficiary_patients.beneficiary_id', '=', 'beneficiaries.id');
            })
            ->leftJoin('barangays', function($join)
            {
                $join->on('beneficiaries.barangay_id', '=', 'barangays.id');
            })
            ->where('beneficiary_patients.created_by', $companyAccount->account->user_id)
            ->where(function($q) use ($request)
            {
                $q->whereDate('patient_date', $request->get('from'))
                    ->orWhereDate('patient_date', $request->get('to'))
                    ->orWhereBetween('patient_date', [$request->get('from'), $request->get('to')]);
            })
            ->orderBy('barangays.name', 'asc')
            ->get();
    }
}
