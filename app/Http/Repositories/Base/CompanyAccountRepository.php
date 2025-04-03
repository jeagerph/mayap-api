<?php

namespace App\Http\Repositories\Base;

use Illuminate\Support\Facades\Auth;

use App\Models\CompanyAccount;

class CompanyAccountRepository
{
    public function new($data)
    {
        return new CompanyAccount([
            'account_id' => $data['account_id'],
            'company_position_id' => $data['company_position_id'],
            'created_by' => Auth::id() ?: 1
        ]);
    }

    public function beneficiariesTotal($dates, $companyAccount)
    {
        $account = $companyAccount->account;
        $company = $companyAccount->company;

        if (array_key_exists('date', $dates)):

            $date = (new \Carbon\Carbon($dates['date']))->format('Y-m-d');

            $startMonth = (new \Carbon\Carbon($dates['date']))->startOfMonth();
            $endMonth = (new \Carbon\Carbon($dates['date']))->endOfMonth();

            $startYear = (new \Carbon\Carbon($dates['date']))->startOfYear();
            $endYear = (new \Carbon\Carbon($dates['date']))->endOfYear();

            $sql = "SELECT ";
            $sql .= "COUNT(beneficiary.id) AS total, ";
            $sql .= "SUM(CASE ";
            $sql .= "WHEN beneficiary.date_registered = '{$startYear}' OR beneficiary.date_registered = '{$endYear}' OR beneficiary.date_registered BETWEEN '{$startYear}' AND '{$endYear}' THEN 1 ";
            $sql .= "END) AS year, ";
            $sql .= "SUM(CASE ";
            $sql .= "WHEN beneficiary.date_registered = '{$startMonth}' OR beneficiary.date_registered = '{$endMonth}' OR beneficiary.date_registered BETWEEN '{$startMonth}' AND '{$endMonth}' THEN 1 ";
            $sql .= "END) AS month, ";
            $sql .= "SUM(CASE ";
            $sql .= "WHEN beneficiary.date_registered = '{$date}' THEN 1 ";
            $sql .= "END) AS date ";
            $sql .= "FROM beneficiaries beneficiary ";
            $sql .= "WHERE beneficiary.created_by = {$account->user_id} ";
            $sql .= "AND beneficiary.deleted_at IS NULL ";

            $data = \DB::select($sql);

            return [
                'total' => $data[0]->total ?: 0,
                'year' => $data[0]->year ?: 0,
                'month' => $data[0]->month ?: 0,
                'date' => $data[0]->date ?: 0,
            ];

        else:

            $from = (new \Carbon\Carbon($dates['from']))->format('Y-m-d');
            $to = (new \Carbon\Carbon($dates['to']))->format('Y-m-d');

            $sql = "SELECT ";
            $sql .= "COUNT(beneficiary.id) AS total ";
            $sql .= "FROM beneficiaries beneficiary ";
            $sql .= "WHERE beneficiary.created_by = {$account->user_id} ";
            $sql .= "AND (beneficiary.date_registered = '{$from}' OR beneficiary.date_registered = '{$to}' OR beneficiary.date_registered BETWEEN '{$from}' AND '{$to}') ";
            $sql .= "AND beneficiary.deleted_at IS NULL ";

            $data = \DB::select($sql);

            return [
                'total' => $data[0]->total ?: 0,
            ];

        endif;
    }

    public function assistancesTotal($dates, $companyAccount)
    {
        $account = $companyAccount->account;
        $company = $companyAccount->company;

        if (array_key_exists('date', $dates)):

            $date = (new \Carbon\Carbon($dates['date']))->format('Y-m-d');

            $startMonth = (new \Carbon\Carbon($dates['date']))->startOfMonth();
            $endMonth = (new \Carbon\Carbon($dates['date']))->endOfMonth();

            $startYear = (new \Carbon\Carbon($dates['date']))->startOfYear();
            $endYear = (new \Carbon\Carbon($dates['date']))->endOfYear();

            $sql = "SELECT ";
            $sql .= "COUNT(assistance.id) AS total, ";
            $sql .= "SUM(CASE ";
            $sql .= "WHEN assistance.assistance_date = '{$startYear}' OR assistance.assistance_date = '{$endYear}' OR assistance.assistance_date BETWEEN '{$startYear}' AND '{$endYear}' THEN 1 ";
            $sql .= "END) AS year, ";
            $sql .= "SUM(CASE ";
            $sql .= "WHEN assistance.assistance_date = '{$startMonth}' OR assistance.assistance_date = '{$endMonth}' OR assistance.assistance_date BETWEEN '{$startMonth}' AND '{$endMonth}' THEN 1 ";
            $sql .= "END) AS month, ";
            $sql .= "SUM(CASE ";
            $sql .= "WHEN assistance.assistance_date = '{$date}' THEN 1 ";
            $sql .= "END) AS date ";
            $sql .= "FROM beneficiary_assistances assistance ";
            $sql .= "WHERE assistance.created_by = {$account->user_id} ";
            $sql .= "AND assistance.deleted_at IS NULL ";

            $data = \DB::select($sql);

            return [
                'total' => $data[0]->total ?: 0,
                'year' => $data[0]->year ?: 0,
                'month' => $data[0]->month ?: 0,
                'date' => $data[0]->date ?: 0,
            ];

        else:

            $from = (new \Carbon\Carbon($dates['from']))->format('Y-m-d');
            $to = (new \Carbon\Carbon($dates['to']))->format('Y-m-d');

            $sql = "SELECT ";
            $sql .= "COUNT(assistance.id) AS total ";
            $sql .= "FROM beneficiary_assistances assistance ";
            $sql .= "WHERE assistance.created_by = {$account->user_id} ";
            $sql .= "AND (assistance.assistance_date = '{$from}' OR assistance.assistance_date = '{$to}' OR assistance.assistance_date BETWEEN '{$from}' AND '{$to}') ";
            $sql .= "AND assistance.deleted_at IS NULL ";

            $data = \DB::select($sql);

            return [
                'total' => $data[0]->total ?: 0,
            ];

        endif;
    }

    public function patientsTotal($dates, $companyAccount)
    {
        $account = $companyAccount->account;
        $company = $companyAccount->company;

        if (array_key_exists('date', $dates)):

            $date = (new \Carbon\Carbon($dates['date']))->format('Y-m-d');

            $startMonth = (new \Carbon\Carbon($dates['date']))->startOfMonth();
            $endMonth = (new \Carbon\Carbon($dates['date']))->endOfMonth();

            $startYear = (new \Carbon\Carbon($dates['date']))->startOfYear();
            $endYear = (new \Carbon\Carbon($dates['date']))->endOfYear();

            $sql = "SELECT ";
            $sql .= "COUNT(patient.id) AS total, ";
            $sql .= "SUM(CASE ";
            $sql .= "WHEN patient.patient_date = '{$startYear}' OR patient.patient_date = '{$endYear}' OR patient.patient_date BETWEEN '{$startYear}' AND '{$endYear}' THEN 1 ";
            $sql .= "END) AS year, ";
            $sql .= "SUM(CASE ";
            $sql .= "WHEN patient.patient_date = '{$startMonth}' OR patient.patient_date = '{$endMonth}' OR patient.patient_date BETWEEN '{$startMonth}' AND '{$endMonth}' THEN 1 ";
            $sql .= "END) AS month, ";
            $sql .= "SUM(CASE ";
            $sql .= "WHEN patient.patient_date = '{$date}' THEN 1 ";
            $sql .= "END) AS date ";
            $sql .= "FROM beneficiary_patients patient ";
            $sql .= "WHERE patient.created_by = {$account->user_id} ";
            $sql .= "AND patient.deleted_at IS NULL ";

            $data = \DB::select($sql);

            return [
                'total' => $data[0]->total ?: 0,
                'year' => $data[0]->year ?: 0,
                'month' => $data[0]->month ?: 0,
                'date' => $data[0]->date ?: 0,
            ];

        else:

            $from = (new \Carbon\Carbon($dates['from']))->format('Y-m-d');
            $to = (new \Carbon\Carbon($dates['to']))->format('Y-m-d');

            $sql = "SELECT ";
            $sql .= "COUNT(patient.id) AS total ";
            $sql .= "FROM beneficiary_patients patient ";
            $sql .= "WHERE patient.company_id = {$company->id} ";
            $sql .= "AND (patient.patient_date = '{$from}' OR patient.patient_date = '{$to}' OR patient.patient_date BETWEEN '{$from}' AND '{$to}') ";
            $sql .= "AND patient.deleted_at IS NULL ";
            $sql .= "AND patient.created_by = {$account->user_id} ";

            $data = \DB::select($sql);

            return [
                'total' => $data[0]->total ?: 0,
            ];

        endif;
    }
}
?>