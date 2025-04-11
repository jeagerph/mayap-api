<?php
// Changed By Richard
namespace App\Http\Repositories\Base;

use Illuminate\Support\Facades\Auth;

use App\Models\Company;

use App\Http\Repositories\Base\SlugRepository;
use App\Http\Repositories\Base\CompanyClassificationRepository;

use App\Traits\FileStorage;

class CompanyRepository
{
    use FileStorage;

    public function __construct()
    {
        $this->slugRepository = new SlugRepository;
        $this->classificationRepository = new CompanyClassificationRepository;
    }

    public function store($data)
    {
        $model = new Company;

        $model->name = $data['name'];
        $model->address = $data['address'];
        $model->contact_no = $data['contact_no'];
        $model->status = 1;
        $model->barangay_report_provinces = '0371';
        $model->created_by = Auth::id() ?: 1;
        $model->save();

        $model->slug()->save(
            $this->slugRepository->new(
                $model->name . ' Company'
            )
        );

        return $model;
    }

    public function uploadLogo($currentPath, $data, $folderDir)
    {
        if ($currentPath):
            $this->deleteFile($currentPath);
        endif;

        $photo = $data['photo'];

        $fileName = randomFileName();

        $filePath = randomFilePath($folderDir, $fileName);

        $this->saveFile($filePath, self::base64ToFile($photo), true);

        return $filePath;
    }

    public function refreshCreditAmount($company)
    {
        $replenish = $company->smsCredits()->where('credit_mode', 1)->sum('amount');
        $withdrawal = $company->smsCredits()->where('credit_mode', 2)->sum('amount');

        $total = $replenish - $withdrawal;

        $smsSetting = $company->smsSetting;

        $smsSetting->update([
            'sms_credit' => $total,
            'updated_by' => Auth::id() ?: 1
        ]);
    }

    public function refreshCallCreditAmount($company)
    {
        $replenish = $company->callCredits()->where('credit_mode', 1)->sum('amount');
        $withdrawal = $company->callCredits()->where('credit_mode', 2)->sum('amount');

        $total = $replenish - $withdrawal;

        $callSetting = $company->callSetting;

        $callSetting->update([
            'call_credit' => $total,
            'updated_by' => Auth::id() ?: 1
        ]);
    }

    public function isAllowedToDelete($company)
    {
        // if($company->invoices->count()) return abort(403, 'Company has related invoice records.');

        // if($company->smsTransactions->count()) return abort(403, 'Company has related sms transaction records.');
    }

    public function beneficiariesTotal($dates, $company)
    {
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
            $sql .= "WHERE beneficiary.company_id = {$company->id} ";
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
            $sql .= "WHERE beneficiary.company_id = {$company->id} ";
            $sql .= "AND (beneficiary.date_registered = '{$from}' OR beneficiary.date_registered = '{$to}' OR beneficiary.date_registered BETWEEN '{$from}' AND '{$to}') ";
            $sql .= "AND beneficiary.deleted_at IS NULL ";

            $data = \DB::select($sql);

            return [
                'total' => $data[0]->total ?: 0,
            ];

        endif;
    }

    public function issuedSdnIds($dates, $company)
    {
        if (array_key_exists('date', $dates)):

            $date = (new \Carbon\Carbon($dates['date']))->format('Y-m-d');

            $startMonth = (new \Carbon\Carbon($dates['date']))->startOfMonth();
            $endMonth = (new \Carbon\Carbon($dates['date']))->endOfMonth();

            $startYear = (new \Carbon\Carbon($dates['date']))->startOfYear();
            $endYear = (new \Carbon\Carbon($dates['date']))->endOfYear();

            $sql = "SELECT ";
            $sql .= "COUNT(identification.id) AS total, ";
            $sql .= "SUM(CASE ";
            $sql .= "WHEN DATE(identification.created_at) = '{$startYear}' OR DATE(identification.created_at) = '{$endYear}' OR DATE(identification.created_at) BETWEEN '{$startYear}' AND '{$endYear}' THEN 1 ";
            $sql .= "END) AS year, ";
            $sql .= "SUM(CASE ";
            $sql .= "WHEN DATE(identification.created_at) = '{$startMonth}' OR DATE(identification.created_at) = '{$endMonth}' OR DATE(identification.created_at) BETWEEN '{$startMonth}' AND '{$endMonth}' THEN 1 ";
            $sql .= "END) AS month, ";
            $sql .= "SUM(CASE ";
            $sql .= "WHEN DATE(identification.created_at) = '{$date}' THEN 1 ";
            $sql .= "END) AS date ";
            $sql .= "FROM beneficiary_identifications identification ";
            $sql .= "WHERE identification.company_id = {$company->id} ";
            $sql .= "AND identification.name LIKE '%beneficiary id%' ";
            $sql .= "AND identification.is_printed = 1 ";
            $sql .= "AND identification.deleted_at IS NULL ";

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
            $sql .= "COUNT(identification.id) AS total ";
            $sql .= "FROM beneficiary_identifications identification ";
            $sql .= "WHERE identification.company_id = {$company->id} ";
            $sql .= "AND (DATE(identification.created_at) = '{$from}' OR DATE(identification.created_at) = '{$to}' OR DATE(identification.created_at) BETWEEN '{$from}' AND '{$to}') ";
            $sql .= "AND identification.name LIKE '%beneficiary id%' ";
            $sql .= "AND identification.is_printed = 1 ";
            $sql .= "AND identification.deleted_at IS NULL ";

            $data = \DB::select($sql);

            return [
                'total' => $data[0]->total ?: 0,
            ];

        endif;
    }

    public function verifiedVotersTotal($dates, $company)
    {
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
            $sql .= "WHERE beneficiary.company_id = {$company->id} ";
            $sql .= "AND beneficiary.verify_voter = 2 ";
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
            $sql .= "WHERE beneficiary.company_id = {$company->id} ";
            $sql .= "AND (beneficiary.date_registered = '{$from}' OR beneficiary.date_registered = '{$to}' OR beneficiary.date_registered BETWEEN '{$from}' AND '{$to}') ";
            $sql .= "AND beneficiary.verify_voter = 2 ";
            $sql .= "AND beneficiary.deleted_at IS NULL ";

            $data = \DB::select($sql);

            return [
                'total' => $data[0]->total ?: 0,
            ];

        endif;
    }

    public function crossMatchedVotersTotal($dates, $company)
    {
        if (array_key_exists('date', $dates)):

            $date = (new \Carbon\Carbon($dates['date']))->format('Y-m-d');

            $startMonth = (new \Carbon\Carbon($dates['date']))->startOfMonth();
            $endMonth = (new \Carbon\Carbon($dates['date']))->endOfMonth();

            $startYear = (new \Carbon\Carbon($dates['date']))->startOfYear();
            $endYear = (new \Carbon\Carbon($dates['date']))->endOfYear();

            $sql = "SELECT ";
            $sql .= "COUNT(beneficiary.id) AS total, ";
            $sql .= "SUM(CASE ";
            $sql .= "WHEN beneficiary.updated_at = '{$startYear}' OR beneficiary.updated_at = '{$endYear}' OR beneficiary.updated_at BETWEEN '{$startYear}' AND '{$endYear}' THEN 1 ";
            $sql .= "END) AS year, ";
            $sql .= "SUM(CASE ";
            $sql .= "WHEN beneficiary.updated_at = '{$startMonth}' OR beneficiary.updated_at = '{$endMonth}' OR beneficiary.updated_at BETWEEN '{$startMonth}' AND '{$endMonth}' THEN 1 ";
            $sql .= "END) AS month, ";
            $sql .= "SUM(CASE ";
            $sql .= "WHEN beneficiary.updated_at = '{$date}' THEN 1 ";
            $sql .= "END) AS date ";
            $sql .= "FROM beneficiaries beneficiary ";
            $sql .= "WHERE beneficiary.company_id = {$company->id} ";
            $sql .= "AND beneficiary.verify_voter = 1 ";
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
            $sql .= "WHERE beneficiary.company_id = {$company->id} ";
            $sql .= "AND (beneficiary.updated_at = '{$from}' OR beneficiary.updated_at = '{$to}' OR beneficiary.updated_at BETWEEN '{$from}' AND '{$to}') ";
            $sql .= "AND beneficiary.verify_voter = 1 ";
            $sql .= "AND beneficiary.deleted_at IS NULL ";

            $data = \DB::select($sql);

            return [
                'total' => $data[0]->total ?: 0,
            ];

        endif;
    }

    public function patientsTotal($dates, $company)
    {
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
            $sql .= "WHERE patient.company_id = {$company->id} ";
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

            $data = \DB::select($sql);

            return [
                'total' => $data[0]->total ?: 0,
            ];

        endif;
    }

    public function incentivesTotal($dates, $company)
    {
        if (array_key_exists('date', $dates)):

            $date = (new \Carbon\Carbon($dates['date']))->format('Y-m-d');

            $startMonth = (new \Carbon\Carbon($dates['date']))->startOfMonth();
            $endMonth = (new \Carbon\Carbon($dates['date']))->endOfMonth();

            $startYear = (new \Carbon\Carbon($dates['date']))->startOfYear();
            $endYear = (new \Carbon\Carbon($dates['date']))->endOfYear();

            $sql = "SELECT ";
            $sql .= "SUM(CASE WHEN incentive.mode = 1 THEN incentive.points END) AS total_replenish, ";
            $sql .= "SUM(CASE WHEN incentive.mode = 2 THEN incentive.points END) AS total_withdrawal, ";
            $sql .= "SUM(CASE ";
            $sql .= "WHEN (incentive.incentive_date = '{$startYear}' OR incentive.incentive_date = '{$endYear}' OR incentive.incentive_date BETWEEN '{$startYear}' AND '{$endYear}') AND incentive.mode = 1 THEN incentive.points ";
            $sql .= "END) AS year_replenish, ";
            $sql .= "SUM(CASE ";
            $sql .= "WHEN (incentive.incentive_date = '{$startMonth}' OR incentive.incentive_date = '{$endMonth}' OR incentive.incentive_date BETWEEN '{$startMonth}' AND '{$endMonth}') AND incentive.mode = 2 THEN incentive.points ";
            $sql .= "END) AS year_withdrawal, ";
            $sql .= "SUM(CASE ";
            $sql .= "WHEN (incentive.incentive_date = '{$startMonth}' OR incentive.incentive_date = '{$endMonth}' OR incentive.incentive_date BETWEEN '{$startMonth}' AND '{$endMonth}') AND incentive.mode = 1 THEN incentive.points ";
            $sql .= "END) AS month_replenish, ";
            $sql .= "SUM(CASE ";
            $sql .= "WHEN (incentive.incentive_date = '{$startMonth}' OR incentive.incentive_date = '{$endMonth}' OR incentive.incentive_date BETWEEN '{$startMonth}' AND '{$endMonth}') AND incentive.mode = 2 THEN incentive.points ";
            $sql .= "END) AS month_withdrawal, ";
            $sql .= "SUM(CASE ";
            $sql .= "WHEN incentive.incentive_date = '{$date}' AND incentive.mode = 1 THEN incentive.points ";
            $sql .= "END) AS date_replenish, ";
            $sql .= "SUM(CASE ";
            $sql .= "WHEN incentive.incentive_date = '{$date}' AND incentive.mode = 2 THEN incentive.points ";
            $sql .= "END) AS date_withdrawal ";
            $sql .= "FROM beneficiary_incentives incentive ";
            $sql .= "WHERE incentive.company_id = {$company->id} ";
            $sql .= "AND incentive.deleted_at IS NULL ";

            $data = \DB::select($sql);

            return [
                'total' => ($data[0]->total_replenish - $data[0]->total_withdrawal) ?: 0,
                'month' => ($data[0]->month_replenish - $data[0]->month_withdrawal) ?: 0,
                'year' => ($data[0]->year_replenish - $data[0]->year_withdrawal) ?: 0,
                'date' => ($data[0]->date_replenish - $data[0]->date_withdrawal) ?: 0,
            ];

        else:
            $from = (new \Carbon\Carbon($dates['from']))->format('Y-m-d');
            $to = (new \Carbon\Carbon($dates['to']))->format('Y-m-d');

            $sql = "SELECT ";
            $sql .= "SUM(CASE WHEN incentive.mode = 1 THEN incentive.points END) AS total_replenish, ";
            $sql .= "SUM(CASE WHEN incentive.mode = 2 THEN incentive.points END) AS total_withdrawal ";
            $sql .= "FROM beneficiary_incentives incentive ";
            $sql .= "WHERE incentive.company_id = {$company->id} ";
            $sql .= "AND (incentive.incentive_date = '{$from}' OR incentive.incentive_date = '{$to}' OR incentive.incentive_date BETWEEN '{$from}' AND '{$to}') ";
            $sql .= "AND incentive.deleted_at IS NULL ";

            $data = \DB::select($sql);

            return [
                'total' => ($data[0]->total_replenish - $data[0]->total_withdrawal) ?: 0,
            ];

        endif;
    }

    public function assistancesTotal($dates, $company)
    {
        if (array_key_exists('date', $dates)):

            $date = (new \Carbon\Carbon($dates['date']))->format('Y-m-d');

            $startMonth = (new \Carbon\Carbon($dates['date']))->startOfMonth();
            $endMonth = (new \Carbon\Carbon($dates['date']))->endOfMonth();

            $startYear = (new \Carbon\Carbon($dates['date']))->startOfYear();
            $endYear = (new \Carbon\Carbon($dates['date']))->endOfYear();

            $sql = "SELECT ";
            $sql .= "COUNT(assistance.id) AS total, ";
            $sql .= "SUM(CASE WHEN assistance.assistance_date = '2023-12-31' OR assistance.assistance_date = '2024-12-31' OR (assistance.assistance_date BETWEEN '2023-12-31' AND '2024-12-31') THEN 1 ELSE 0 END) AS year, ";
            $sql .= "SUM(CASE ";
            $sql .= "WHEN assistance.assistance_date BETWEEN '{$startMonth}' AND '{$endMonth}' THEN 1 ";
            $sql .= "END) AS month, ";
            $sql .= "SUM(CASE ";
            $sql .= "WHEN assistance.assistance_date = '{$date}' THEN 1 ";
            $sql .= "END) AS date ";
            $sql .= "FROM beneficiary_assistances assistance ";
            $sql .= "WHERE assistance.company_id = {$company->id} ";
            $sql .= "AND assistance.is_assisted = 1 ";
            $sql .= "AND assistance.deleted_at IS NULL ";

            $data = \DB::select($sql);

            return [
                'total' => $data[0]->total ?: 0,
                'year' => $data[0]->year ?: 0,
                'month' => $data[0]->month ?: 0,
                'date' => $data[0]->date ?: 0,
                'date' => $dates,
                'data' => $data,
                'startYear' => $startYear,
                'endYear' => $endYear

            ];

        else:
            $date = (new \Carbon\Carbon($dates['date']))->format('Y-m-d');

            $startYear = (new \Carbon\Carbon($dates['date']))->startOfYear();
            $endYear = (new \Carbon\Carbon($dates['date']))->endOfYear();

            $sql = "SELECT ";
            $sql .= "COUNT(assistance.id) AS total, ";
            $sql .= "FROM beneficiary_assistances assistance ";
            $sql .= "WHERE assistance.company_id = {$company->id} ";
            $sql .= "AND assistance.assistance_date = '{$startYear}' OR assistance.assistance_date = '{$endYear}' OR assistance.assistance_date BETWEEN '{$startYear}' AND '{$endYear}' ";
            $sql .= "AND assistance.is_assisted = 0 ";
            $sql .= "AND assistance.deleted_at IS NULL ";

            $data = \DB::select($sql);

            return [
                'total' => $data[0]->total ?: 0,
            ];

        endif;



        $data = \DB::select($sql);

        return [
            'total' => $data[0]->total ?: 0,
            'month' => $data[0]->month ?: 0,
            'year' => $data[0]->year ?: 0,
            'date' => $dates,

        ];
    }

    public function requestedAssistancesTotal($dates, $company)
    {
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
            $sql .= "WHERE assistance.company_id = {$company->id} ";
            $sql .= "AND assistance.is_assisted = 0 ";
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
            $sql .= "COUNT(assistance.id) AS total, ";
            $sql .= "FROM beneficiary_assistances assistance ";
            $sql .= "WHERE assistance.company_id = {$company->id} ";
            $sql .= "AND (assistance.assistance_date = '{$from}' OR assistance.assistance_date = '{$to}' OR assistance.assistance_date BETWEEN '{$from}' AND '{$to}') ";
            $sql .= "AND assistance.is_assisted = 0 ";
            $sql .= "AND assistance.deleted_at IS NULL ";

            $data = \DB::select($sql);

            return [
                'total' => $data[0]->total ?: 0,
            ];

        endif;
    }

    public function assistedAssistancesTotal($dates, $company)
    {
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
            $sql .= "WHERE assistance.company_id = {$company->id} ";
            $sql .= "AND assistance.is_assisted = 1 ";
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
            $sql .= "COUNT(assistance.id) AS total, ";
            $sql .= "FROM beneficiary_assistances assistance ";
            $sql .= "WHERE assistance.company_id = {$company->id} ";
            $sql .= "AND (assistance.assistance_date = '{$from}' OR assistance.assistance_date = '{$to}' OR assistance.assistance_date BETWEEN '{$from}' AND '{$to}') ";
            $sql .= "AND assistance.is_assisted = 1 ";
            $sql .= "AND assistance.deleted_at IS NULL ";

            $data = \DB::select($sql);

            return [
                'total' => $data[0]->total ?: 0,
            ];

        endif;
    }

    public function assistancesByTypeTotal($dates, $company)
    {
        if (array_key_exists('date', $dates)):

            $date = (new \Carbon\Carbon($dates['date']))->format('Y-m-d');

            $sql = "SELECT ";
            $sql .= "assistance.assistance_type AS name, ";
            $sql .= "COUNT(assistance.id) AS total ";
            $sql .= "FROM beneficiary_assistances assistance ";
            $sql .= "WHERE assistance.company_id = {$company->id} ";
            $sql .= "AND assistance.assistance_date = '{$date}' ";
            $sql .= "AND assistance.deleted_at IS NULL ";
            $sql .= "GROUP BY assistance.assistance_type ";

            $data = \DB::select($sql);

            return $data;

        else:
            $from = (new \Carbon\Carbon($dates['from']))->format('Y-m-d');
            $to = (new \Carbon\Carbon($dates['to']))->format('Y-m-d');

            $sql = "SELECT ";
            $sql .= "assistance.assistance_type AS name, ";
            $sql .= "COUNT(assistance.id) AS total ";
            $sql .= "FROM beneficiary_assistances assistance ";
            $sql .= "WHERE assistance.company_id = {$company->id} ";
            $sql .= "AND (assistance.assistance_date = '{$from}' OR assistance.assistance_date = '{$to}' OR assistance.assistance_date BETWEEN '{$from}' AND '{$to}') ";
            $sql .= "AND assistance.deleted_at IS NULL ";
            $sql .= "GROUP BY assistance.assistance_type ";

            $data = \DB::select($sql);

            return $data;

        endif;
    }

    public function householdTotal($dates, $company)
    {
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
            $sql .= "WHERE beneficiary.company_id = {$company->id} ";
            $sql .= "AND beneficiary.is_household = 1 ";
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
            $sql .= "WHERE beneficiary.company_id = {$company->id} ";
            $sql .= "AND (beneficiary.date_registered = '{$from}' OR beneficiary.date_registered = '{$to}' OR beneficiary.date_registered BETWEEN '{$from}' AND '{$to}') ";
            $sql .= "AND beneficiary.is_household = 1 ";
            $sql .= "AND beneficiary.deleted_at IS NULL ";

            $data = \DB::select($sql);

            return [
                'total' => $data[0]->total ?: 0,
            ];

        endif;
    }

    public function householdByBarangayTotal($company)
    {
        $sql = "SELECT ";
        $sql .= "beneficiary.province_id, ";
        $sql .= "beneficiary.city_id, ";
        $sql .= "beneficiary.barangay_id, ";
        $sql .= "(SELECT name FROM barangays WHERE barangays.id = beneficiary.barangay_id) AS barangay_name, ";
        $sql .= "(SELECT name FROM cities WHERE cities.city_code = beneficiary.city_id) AS city_name, ";
        $sql .= "(SELECT name FROM provinces WHERE provinces.prov_code = beneficiary.province_id) AS province_name, ";
        $sql .= "COUNT(*) AS total ";
        $sql .= "FROM beneficiaries beneficiary ";
        $sql .= "WHERE beneficiary.company_id = {$company->id} ";
        $sql .= "AND beneficiary.is_household = 1 ";
        $sql .= "AND beneficiary.deleted_at IS NULL ";
        $sql .= "GROUP BY beneficiary.province_id, beneficiary.city_id, beneficiary.barangay_id ";

        $data = \DB::select($sql);

        return $data;
    }

    public function householdByPurokTotal($company)
    {
        $sql = "SELECT ";
        $sql .= "beneficiary.purok, ";
        $sql .= "COUNT(*) AS total ";
        $sql .= "FROM beneficiaries beneficiary ";
        $sql .= "WHERE beneficiary.company_id = {$company->id} ";
        $sql .= "AND beneficiary.is_household = 1 ";
        $sql .= "AND beneficiary.deleted_at IS NULL ";
        $sql .= "GROUP BY beneficiary.purok ";

        $data = \DB::select($sql);

        return $data;
    }

    public function officersTotal($dates, $company)
    {
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
            $sql .= "WHERE beneficiary.company_id = {$company->id} ";
            $sql .= "AND beneficiary.is_officer = 1 ";
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
            $sql .= "WHERE beneficiary.company_id = {$company->id} ";
            $sql .= "AND (beneficiary.date_registered = '{$from}' OR beneficiary.date_registered = '{$to}' OR beneficiary.date_registered BETWEEN '{$from}' AND '{$to}') ";
            $sql .= "AND beneficiary.is_officer = 1 ";
            $sql .= "AND beneficiary.deleted_at IS NULL ";

            $data = \DB::select($sql);

            return [
                'total' => $data[0]->total ?: 0,
            ];

        endif;
    }

    public function voterTypesTotal($dates, $company)
    {
        if (array_key_exists('date', $dates)):

            $date = (new \Carbon\Carbon($dates['date']))->format('Y-m-d');

            $startMonth = (new \Carbon\Carbon($dates['date']))->startOfMonth();
            $endMonth = (new \Carbon\Carbon($dates['date']))->endOfMonth();

            $startYear = (new \Carbon\Carbon($dates['date']))->startOfYear();
            $endYear = (new \Carbon\Carbon($dates['date']))->endOfYear();

            $sql = "SELECT ";
            $sql .= "COUNT(beneficiary.id) AS total, ";
            $sql .= "SUM(CASE WHEN beneficiary.voter_type = 1 THEN 1 END) AS others_total, ";
            $sql .= "SUM(CASE WHEN beneficiary.voter_type = 2 THEN 1 END) AS command_total, ";
            $sql .= "SUM(CASE WHEN beneficiary.voter_type = 3 THEN 1 END) AS sure_total, ";
            $sql .= "SUM(CASE WHEN beneficiary.voter_type = 4 THEN 1 END) AS swing_total, ";
            $sql .= "SUM(CASE WHEN beneficiary.voter_type = 5 THEN 1 END) AS block_total, ";

            $sql .= "SUM(CASE WHEN beneficiary.date_registered = '{$startYear}' OR beneficiary.date_registered = '{$endYear}' OR beneficiary.date_registered BETWEEN '{$startYear}' AND '{$endYear}' THEN 1 END) AS year, ";
            $sql .= "SUM(CASE WHEN beneficiary.voter_type = 1 AND (beneficiary.date_registered = '{$startYear}' OR beneficiary.date_registered = '{$endYear}' OR beneficiary.date_registered BETWEEN '{$startYear}' AND '{$endYear}') THEN 1 END) AS others_year, ";
            $sql .= "SUM(CASE WHEN beneficiary.voter_type = 2 AND (beneficiary.date_registered = '{$startYear}' OR beneficiary.date_registered = '{$endYear}' OR beneficiary.date_registered BETWEEN '{$startYear}' AND '{$endYear}') THEN 1 END) AS command_year, ";
            $sql .= "SUM(CASE WHEN beneficiary.voter_type = 3 AND (beneficiary.date_registered = '{$startYear}' OR beneficiary.date_registered = '{$endYear}' OR beneficiary.date_registered BETWEEN '{$startYear}' AND '{$endYear}') THEN 1 END) AS sure_year, ";
            $sql .= "SUM(CASE WHEN beneficiary.voter_type = 4 AND (beneficiary.date_registered = '{$startYear}' OR beneficiary.date_registered = '{$endYear}' OR beneficiary.date_registered BETWEEN '{$startYear}' AND '{$endYear}') THEN 1 END) AS swing_year, ";
            $sql .= "SUM(CASE WHEN beneficiary.voter_type = 5 AND (beneficiary.date_registered = '{$startYear}' OR beneficiary.date_registered = '{$endYear}' OR beneficiary.date_registered BETWEEN '{$startYear}' AND '{$endYear}') THEN 1 END) AS block_year, ";

            $sql .= "SUM(CASE WHEN beneficiary.date_registered = '{$startMonth}' OR beneficiary.date_registered = '{$endMonth}' OR beneficiary.date_registered BETWEEN '{$startMonth}' AND '{$endMonth}' THEN 1 END) AS month, ";
            $sql .= "SUM(CASE WHEN beneficiary.voter_type = 1 AND (beneficiary.date_registered = '{$startMonth}' OR beneficiary.date_registered = '{$endMonth}' OR beneficiary.date_registered BETWEEN '{$startMonth}' AND '{$endMonth}') THEN 1 END) AS others_month, ";
            $sql .= "SUM(CASE WHEN beneficiary.voter_type = 2 AND (beneficiary.date_registered = '{$startMonth}' OR beneficiary.date_registered = '{$endMonth}' OR beneficiary.date_registered BETWEEN '{$startMonth}' AND '{$endMonth}') THEN 1 END) AS command_month, ";
            $sql .= "SUM(CASE WHEN beneficiary.voter_type = 3 AND (beneficiary.date_registered = '{$startMonth}' OR beneficiary.date_registered = '{$endMonth}' OR beneficiary.date_registered BETWEEN '{$startMonth}' AND '{$endMonth}') THEN 1 END) AS sure_month, ";
            $sql .= "SUM(CASE WHEN beneficiary.voter_type = 4 AND (beneficiary.date_registered = '{$startMonth}' OR beneficiary.date_registered = '{$endMonth}' OR beneficiary.date_registered BETWEEN '{$startMonth}' AND '{$endMonth}') THEN 1 END) AS swing_month, ";
            $sql .= "SUM(CASE WHEN beneficiary.voter_type = 5 AND (beneficiary.date_registered = '{$startMonth}' OR beneficiary.date_registered = '{$endMonth}' OR beneficiary.date_registered BETWEEN '{$startMonth}' AND '{$endMonth}') THEN 1 END) AS block_month, ";

            $sql .= "SUM(CASE WHEN beneficiary.date_registered = '{$date}' THEN 1 END) AS date, ";
            $sql .= "SUM(CASE WHEN beneficiary.voter_type = 1 AND beneficiary.date_registered = '{$date}' THEN 1 END) AS others_date, ";
            $sql .= "SUM(CASE WHEN beneficiary.voter_type = 2 AND beneficiary.date_registered = '{$date}' THEN 1 END) AS command_date, ";
            $sql .= "SUM(CASE WHEN beneficiary.voter_type = 3 AND beneficiary.date_registered = '{$date}' THEN 1 END) AS sure_date, ";
            $sql .= "SUM(CASE WHEN beneficiary.voter_type = 4 AND beneficiary.date_registered = '{$date}' THEN 1 END) AS swing_date, ";
            $sql .= "SUM(CASE WHEN beneficiary.voter_type = 5 AND beneficiary.date_registered = '{$date}' THEN 1 END) AS block_date ";


            $sql .= "FROM beneficiaries beneficiary ";
            $sql .= "WHERE beneficiary.company_id = {$company->id} ";
            $sql .= "AND beneficiary.deleted_at IS NULL ";

            $data = \DB::select($sql);

            return [
                'total' => $data[0]->total ?: 0,
                'others' => $data[0]->others_total ?: 0,
                'command' => $data[0]->command_total ?: 0,
                'sure' => $data[0]->sure_total ?: 0,
                'swing' => $data[0]->swing_total ?: 0,
                'block' => $data[0]->block_total ?: 0,
                'year' => [
                    'total' => $data[0]->year ?: 0,
                    'others' => $data[0]->others_year ?: 0,
                    'command' => $data[0]->command_year ?: 0,
                    'sure' => $data[0]->sure_year ?: 0,
                    'swing' => $data[0]->swing_year ?: 0,
                    'block' => $data[0]->block_year ?: 0,
                ],
                'month' => [
                    'total' => $data[0]->month ?: 0,
                    'others' => $data[0]->others_month ?: 0,
                    'command' => $data[0]->command_month ?: 0,
                    'sure' => $data[0]->sure_month ?: 0,
                    'swing' => $data[0]->swing_month ?: 0,
                    'block' => $data[0]->block_month ?: 0,
                ],
                'date' => [
                    'total' => $data[0]->date ?: 0,
                    'others' => $data[0]->others_date ?: 0,
                    'command' => $data[0]->command_date ?: 0,
                    'sure' => $data[0]->sure_date ?: 0,
                    'swing' => $data[0]->swing_date ?: 0,
                    'block' => $data[0]->block_date ?: 0,
                ],
            ];

        else:

            $from = (new \Carbon\Carbon($dates['from']))->format('Y-m-d');
            $to = (new \Carbon\Carbon($dates['to']))->format('Y-m-d');

            $sql = "SELECT ";
            $sql .= "SUM(CASE WHEN beneficiary.voter_type = 1 THEN 1 END) AS others, ";
            $sql .= "SUM(CASE WHEN beneficiary.voter_type = 2 THEN 1 END) AS command, ";
            $sql .= "SUM(CASE WHEN beneficiary.voter_type = 3 THEN 1 END) AS sure, ";
            $sql .= "SUM(CASE WHEN beneficiary.voter_type = 4 THEN 1 END) AS swing, ";
            $sql .= "SUM(CASE WHEN beneficiary.voter_type = 5 THEN 1 END) AS block, ";
            $sql .= "COUNT(beneficiary.id) AS total ";
            $sql .= "FROM beneficiaries beneficiary ";
            $sql .= "WHERE beneficiary.company_id = {$company->id} ";
            $sql .= "AND (beneficiary.date_registered = '{$from}' OR beneficiary.date_registered = '{$to}' OR beneficiary.date_registered BETWEEN '{$from}' AND '{$to}') ";
            $sql .= "AND beneficiary.deleted_at IS NULL ";

            $data = \DB::select($sql);

            return [
                'total' => $data[0]->total ?: 0,
                'others' => $data[0]->others ?: 0,
                'command' => $data[0]->command ?: 0,
                'sure' => $data[0]->sure ?: 0,
                'swing' => $data[0]->swing ?: 0,
                'block' => $data[0]->block ?: 0,
            ];

        endif;
    }

    public function networksTotal($dates, $company)
    {
        if (array_key_exists('date', $dates)):

            $date = (new \Carbon\Carbon($dates['date']))->format('Y-m-d');

            $startMonth = (new \Carbon\Carbon($dates['date']))->startOfMonth();
            $endMonth = (new \Carbon\Carbon($dates['date']))->endOfMonth();

            $startYear = (new \Carbon\Carbon($dates['date']))->startOfYear();
            $endYear = (new \Carbon\Carbon($dates['date']))->endOfYear();

            $sql = "SELECT ";
            $sql .= "COUNT(network.id) AS total, ";
            $sql .= "SUM(CASE ";
            $sql .= "WHEN DATE(network.created_at) = '{$startYear}' OR DATE(network.created_at) = '{$endYear}' OR DATE(network.created_at) BETWEEN '{$startYear}' AND '{$endYear}' THEN 1 ";
            $sql .= "END) AS year, ";
            $sql .= "SUM(CASE ";
            $sql .= "WHEN DATE(network.created_at) = '{$startMonth}' OR DATE(network.created_at) = '{$endMonth}' OR DATE(network.created_at) BETWEEN '{$startMonth}' AND '{$endMonth}' THEN 1 ";
            $sql .= "END) AS month, ";
            $sql .= "SUM(CASE ";
            $sql .= "WHEN DATE(network.created_at) = '{$date}' THEN 1 ";
            $sql .= "END) AS date ";
            $sql .= "FROM beneficiary_networks network ";
            $sql .= "WHERE network.company_id = {$company->id} ";
            $sql .= "AND network.deleted_at IS NULL ";

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
            $sql .= "COUNT(network.id) AS total ";
            $sql .= "FROM beneficiary_networks network ";
            $sql .= "WHERE network.company_id = {$company->id} ";
            $sql .= "AND (DATE(network.created_at) = '{$from}' OR DATE(network.created_at) = '{$to}' OR DATE(network.created_at) BETWEEN '{$from}' AND '{$to}') ";
            $sql .= "AND network.deleted_at IS NULL ";

            $data = \DB::select($sql);

            return [
                'total' => $data[0]->total ?: 0,
            ];

        endif;
    }

    public function documentsTotal($dates, $company)
    {
        if (array_key_exists('date', $dates)):

            $date = (new \Carbon\Carbon($dates['date']))->format('Y-m-d');

            $startMonth = (new \Carbon\Carbon($dates['date']))->startOfMonth();
            $endMonth = (new \Carbon\Carbon($dates['date']))->endOfMonth();

            $startYear = (new \Carbon\Carbon($dates['date']))->startOfYear();
            $endYear = (new \Carbon\Carbon($dates['date']))->endOfYear();

            $sql = "SELECT ";
            $sql .= "COUNT(document.id) AS total, ";
            $sql .= "SUM(CASE ";
            $sql .= "WHEN document.document_date = '{$startYear}' OR document.document_date = '{$endYear}' OR document.document_date BETWEEN '{$startYear}' AND '{$endYear}' THEN 1 ";
            $sql .= "END) AS year, ";
            $sql .= "SUM(CASE ";
            $sql .= "WHEN document.document_date = '{$startMonth}' OR document.document_date = '{$endMonth}' OR document.document_date BETWEEN '{$startMonth}' AND '{$endMonth}' THEN 1 ";
            $sql .= "END) AS month, ";
            $sql .= "SUM(CASE ";
            $sql .= "WHEN document.document_date = '{$date}' THEN 1 ";
            $sql .= "END) AS date ";
            $sql .= "FROM beneficiary_documents document ";
            $sql .= "WHERE document.company_id = {$company->id} ";
            $sql .= "AND document.deleted_at IS NULL ";

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
            $sql .= "COUNT(document.id) AS total ";
            $sql .= "FROM beneficiary_documents document ";
            $sql .= "WHERE document.company_id = {$company->id} ";
            $sql .= "AND (document.document_date = '{$from}' OR document.document_date = '{$to}' OR document.document_date BETWEEN '{$from}' AND '{$to}') ";
            $sql .= "AND document.deleted_at IS NULL ";

            $data = \DB::select($sql);

            return [
                'total' => $data[0]->total ?: 0,
            ];

        endif;
    }

    public function messagesTotal($dates, $company)
    {
        if (array_key_exists('date', $dates)):

            $date = (new \Carbon\Carbon($dates['date']))->format('Y-m-d');

            $startMonth = (new \Carbon\Carbon($dates['date']))->startOfMonth();
            $endMonth = (new \Carbon\Carbon($dates['date']))->endOfMonth();

            $startYear = (new \Carbon\Carbon($dates['date']))->startOfYear();
            $endYear = (new \Carbon\Carbon($dates['date']))->endOfYear();

            $sql = "SELECT ";
            $sql .= "COUNT(message.id) AS total, ";
            $sql .= "SUM(CASE ";
            $sql .= "WHEN message.message_date = '{$startYear}' OR message.message_date = '{$endYear}' OR message.message_date BETWEEN '{$startYear}' AND '{$endYear}' THEN 1 ";
            $sql .= "END) AS year, ";
            $sql .= "SUM(CASE ";
            $sql .= "WHEN message.message_date = '{$startMonth}' OR message.message_date = '{$endMonth}' OR message.message_date BETWEEN '{$startMonth}' AND '{$endMonth}' THEN 1 ";
            $sql .= "END) AS month, ";
            $sql .= "SUM(CASE ";
            $sql .= "WHEN message.message_date = '{$date}' THEN 1 ";
            $sql .= "END) AS date ";
            $sql .= "FROM beneficiary_messages message ";
            $sql .= "WHERE message.company_id = {$company->id} ";
            $sql .= "AND message.deleted_at IS NULL ";

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
            $sql .= "COUNT(message.id) AS total ";
            $sql .= "FROM beneficiary_messages message ";
            $sql .= "WHERE message.company_id = {$company->id} ";
            $sql .= "AND (message.message_date = '{$from}' OR message.message_date = '{$to}' OR message.message_date BETWEEN '{$from}' AND '{$to}') ";
            $sql .= "AND message.deleted_at IS NULL ";

            $data = \DB::select($sql);

            return [
                'total' => $data[0]->total ?: 0,
            ];

        endif;
    }

    public function callsTotal($dates, $company)
    {
        if (array_key_exists('date', $dates)):

            $date = (new \Carbon\Carbon($dates['date']))->format('Y-m-d');

            $startMonth = (new \Carbon\Carbon($dates['date']))->startOfMonth();
            $endMonth = (new \Carbon\Carbon($dates['date']))->endOfMonth();

            $startYear = (new \Carbon\Carbon($dates['date']))->startOfYear();
            $endYear = (new \Carbon\Carbon($dates['date']))->endOfYear();

            $sql = "SELECT ";
            $sql .= "SUM(benefCall.call_minutes) AS total, ";
            $sql .= "SUM(CASE ";
            $sql .= "WHEN benefCall.call_date = '{$startYear}' OR benefCall.call_date = '{$endYear}' OR benefCall.call_date BETWEEN '{$startYear}' AND '{$endYear}' THEN benefCall.call_minutes ";
            $sql .= "END) AS year, ";
            $sql .= "SUM(CASE ";
            $sql .= "WHEN benefCall.call_date = '{$startMonth}' OR benefCall.call_date = '{$endMonth}' OR benefCall.call_date BETWEEN '{$startMonth}' AND '{$endMonth}' THEN benefCall.call_minutes ";
            $sql .= "END) AS month, ";
            $sql .= "SUM(CASE ";
            $sql .= "WHEN benefCall.call_date = '{$date}' THEN benefCall.call_minutes ";
            $sql .= "END) AS date ";
            $sql .= "FROM beneficiary_calls benefCall ";
            $sql .= "WHERE benefCall.company_id = {$company->id} ";
            $sql .= "AND benefCall.deleted_at IS NULL ";

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
            $sql .= "SUM(benefCall.call_minutes) AS total ";
            $sql .= "FROM beneficiary_calls benefCall ";
            $sql .= "WHERE benefCall.company_id = {$company->id} ";
            $sql .= "AND (benefCall.call_date = '{$from}' OR benefCall.call_date = '{$to}' OR benefCall.call_date BETWEEN '{$from}' AND '{$to}') ";
            $sql .= "AND benefCall.deleted_at IS NULL ";

            $data = \DB::select($sql);

            return [
                'total' => $data[0]->total ?: 0,
            ];

        endif;



        $data = \DB::select($sql);

        return [
            'total' => $data[0]->total ?: 0,
            'month' => $data[0]->month ?: 0,
            'year' => $data[0]->year ?: 0,
        ];
    }
    public function beneficiariesPerWeekTotal($dates, $company)
    {
        $from = (new \Carbon\Carbon($dates['from']))->startOfWeek()->format('Y-m-d');
        $to = (new \Carbon\Carbon($dates['to']))->endOfWeek()->format('Y-m-d');

        $sql = "SELECT ";
        $sql .= "YEARWEEK(beneficiary.date_registered, 1) AS benefWeek, ";
        $sql .= "COUNT(beneficiary.id) AS count ";
        $sql .= "FROM beneficiaries beneficiary ";
        $sql .= "WHERE beneficiary.company_id = {$company->id} ";
        $sql .= "AND (beneficiary.date_registered = '{$from}' OR beneficiary.date_registered = '{$to}' OR beneficiary.date_registered BETWEEN '{$from}' AND '{$to}') ";
        $sql .= "AND beneficiary.deleted_at IS NULL ";
        $sql .= "GROUP BY benefWeek ";
        $sql .= "ORDER BY benefWeek ASC ";

        $data = \DB::select($sql);

        return [
            'from' => $from,
            'to' => $to,
            'data' => $data
        ];
    }
    public function beneficiariesPerMonthTotal($dates, $company)
    {
        $from = (new \Carbon\Carbon($dates['from']))->format('Y-m-d');
        $to = (new \Carbon\Carbon($dates['to']))->format('Y-m-d');

        $sql = "SELECT ";
        $sql .= "YEAR(beneficiary.date_registered) AS benefYear, ";
        $sql .= "MONTH(beneficiary.date_registered) AS benefMonth, ";
        $sql .= "COUNT(beneficiary.id) AS count ";
        $sql .= "FROM beneficiaries beneficiary ";
        $sql .= "WHERE beneficiary.company_id = {$company->id} ";
        $sql .= "AND (beneficiary.date_registered = '{$from}' OR beneficiary.date_registered = '{$to}' OR beneficiary.date_registered BETWEEN '{$from}' AND '{$to}') ";
        $sql .= "AND beneficiary.deleted_at IS NULL ";
        $sql .= "GROUP BY benefYear, benefMonth ";
        $sql .= "ORDER BY benefYear ASC, benefMonth ASC ";

        $data = \DB::select($sql);

        return [
            'year' => (new \Carbon\Carbon($from))->format('Y'),
            'from' => $from,
            'to' => $to,
            'data' => $data
        ];
    }
    public function assistancesPerWeekTotal($dates, $company)
    {
        $from = (new \Carbon\Carbon($dates['from']))->startOfWeek()->format('Y-m-d');
        $to = (new \Carbon\Carbon($dates['to']))->endOfWeek()->format('Y-m-d');

        $sql = "SELECT ";
        $sql .= "YEARWEEK(bAssistance.assistance_date, 1) AS assistanceWeek, ";
        $sql .= "COUNT(bAssistance.id) AS count ";
        $sql .= "FROM beneficiary_assistances bAssistance ";
        $sql .= "WHERE bAssistance.company_id = {$company->id} ";
        $sql .= "AND (bAssistance.assistance_date = '{$from}' OR bAssistance.assistance_date = '{$to}' OR bAssistance.assistance_date BETWEEN '{$from}' AND '{$to}') ";
        $sql .= "AND bAssistance.deleted_at IS NULL ";
        $sql .= "GROUP BY assistanceWeek ";
        $sql .= "ORDER BY assistanceWeek ASC ";

        $data = \DB::select($sql);

        return [
            'from' => $from,
            'to' => $to,
            'data' => $data
        ];
    }
    public function assistancesPerMonthTotal($dates, $company)
    {
        $from = (new \Carbon\Carbon($dates['from']))->format('Y-m-d');
        $to = (new \Carbon\Carbon($dates['to']))->format('Y-m-d');

        $sql = "SELECT ";
        $sql .= "YEAR(bAssistance.assistance_date) AS assistanceYear, ";
        $sql .= "MONTH(bAssistance.assistance_date) AS assistanceMonth, ";
        $sql .= "COUNT(bAssistance.id) AS count ";
        $sql .= "FROM beneficiary_assistances bAssistance ";
        $sql .= "WHERE bAssistance.company_id = {$company->id} ";
        $sql .= "AND (bAssistance.assistance_date = '{$from}' OR bAssistance.assistance_date = '{$to}' OR bAssistance.assistance_date BETWEEN '{$from}' AND '{$to}') ";
        $sql .= "AND bAssistance.deleted_at IS NULL ";
        $sql .= "GROUP BY assistanceYear, assistanceMonth ";
        $sql .= "ORDER BY assistanceYear ASC, assistanceMonth ASC ";

        $data = \DB::select($sql);

        return [
            'year' => (new \Carbon\Carbon($from))->format('Y'),
            'from' => $from,
            'to' => $to,
            'data' => $data,
            'by_week_data' => $this->assistancesPerWeekTotal($dates, $company),
            // 'dates' => $dates
        ];
    }
    public function patientsPerWeekTotal($dates, $company)
    {
        $from = (new \Carbon\Carbon($dates['from']))->startOfWeek()->format('Y-m-d');
        $to = (new \Carbon\Carbon($dates['to']))->endOfWeek()->format('Y-m-d');

        $sql = "SELECT ";
        $sql .= "YEARWEEK(patient.patient_date, 1) AS patientWeek, ";
        $sql .= "COUNT(patient.id) AS count ";
        $sql .= "FROM beneficiary_patients patient ";
        $sql .= "WHERE patient.company_id = {$company->id} ";
        $sql .= "AND (patient.patient_date = '{$from}' OR patient.patient_date = '{$to}' OR patient.patient_date BETWEEN '{$from}' AND '{$to}') ";
        $sql .= "AND patient.deleted_at IS NULL ";
        $sql .= "GROUP BY patientWeek ";
        $sql .= "ORDER BY patientWeek ASC ";

        $data = \DB::select($sql);

        return [
            'from' => $from,
            'to' => $to,
            'data' => $data
        ];
    }
    public function patientsPerMonthTotal($dates, $company)
    {
        $from = (new \Carbon\Carbon($dates['from']))->format('Y-m-d');
        $to = (new \Carbon\Carbon($dates['to']))->format('Y-m-d');

        $sql = "SELECT ";
        $sql .= "YEAR(patient.patient_date) AS patientYear, ";
        $sql .= "MONTH(patient.patient_date) AS patientMonth, ";
        $sql .= "COUNT(patient.id) AS count ";
        $sql .= "FROM beneficiary_patients patient ";
        $sql .= "WHERE patient.company_id = {$company->id} ";
        $sql .= "AND (patient.patient_date = '{$from}' OR patient.patient_date = '{$to}' OR patient.patient_date BETWEEN '{$from}' AND '{$to}') ";
        $sql .= "AND patient.deleted_at IS NULL ";
        $sql .= "GROUP BY patientYear, patientMonth ";
        $sql .= "ORDER BY patientYear ASC, patientMonth ASC ";

        $data = \DB::select($sql);

        return [
            'year' => (new \Carbon\Carbon($from))->format('Y'),
            'from' => $from,
            'to' => $to,
            'data' => $data
        ];
    }
    public function networksPerWeekTotal($dates, $company)
    {
        $from = (new \Carbon\Carbon($dates['from']))->startOfWeek()->format('Y-m-d');
        $to = (new \Carbon\Carbon($dates['to']))->endOfWeek()->format('Y-m-d');

        $sql = "SELECT ";
        $sql .= "YEARWEEK(network.created_at, 1) AS networkWeek, ";
        $sql .= "COUNT(network.id) AS count ";
        $sql .= "FROM beneficiary_networks network ";
        $sql .= "WHERE network.company_id = {$company->id} ";
        $sql .= "AND (DATE(network.created_at) = '{$from}' OR DATE(network.created_at) = '{$to}' OR DATE(network.created_at) BETWEEN '{$from}' AND '{$to}') ";
        $sql .= "AND network.deleted_at IS NULL ";
        $sql .= "GROUP BY networkWeek ";
        $sql .= "ORDER BY networkWeek ASC ";

        $data = \DB::select($sql);

        return [
            'from' => $from,
            'to' => $to,
            'data' => $data
        ];
    }
    public function networksPerMonthTotal($dates, $company)
    {
        $from = (new \Carbon\Carbon($dates['from']))->format('Y-m-d');
        $to = (new \Carbon\Carbon($dates['to']))->format('Y-m-d');

        $sql = "SELECT ";
        $sql .= "YEAR(network.created_at) AS networkYear, ";
        $sql .= "MONTH(network.created_at) AS networkMonth, ";
        $sql .= "COUNT(network.id) AS count ";
        $sql .= "FROM beneficiary_networks network ";
        $sql .= "WHERE network.company_id = {$company->id} ";
        $sql .= "AND (DATE(network.created_at) = '{$from}' OR DATE(network.created_at) = '{$to}' OR DATE(network.created_at) BETWEEN '{$from}' AND '{$to}') ";
        $sql .= "AND network.deleted_at IS NULL ";
        $sql .= "GROUP BY networkYear, networkMonth ";
        $sql .= "ORDER BY networkYear ASC, networkMonth ASC ";

        $data = \DB::select($sql);

        return [
            'year' => (new \Carbon\Carbon($from))->format('Y'),
            'from' => $from,
            'to' => $to,
            'data' => $data
        ];
    }

    public function assistedOverAssistancesTotal($dates, $company)
    {
        $startDate = (new \Carbon\Carbon($dates['from']))->format('Y-m-d');
        $endDate = (new \Carbon\Carbon($dates['to']))->format('Y-m-d');

        $sql = "SELECT ";
        $sql .= "COUNT(assistance.id) AS total, ";
        $sql .= "SUM(CASE ";
        $sql .= "WHEN assistance.is_assisted = 1 THEN 1 ";
        $sql .= "END) AS assisted ";
        $sql .= "FROM beneficiary_assistances assistance ";
        $sql .= "WHERE assistance.company_id = {$company->id} ";
        $sql .= "AND (assistance.assistance_date = '{$startDate}' OR assistance.assistance_date = '{$endDate}' OR assistance.assistance_date BETWEEN '{$startDate}' AND '{$endDate}') ";
        $sql .= "AND assistance.deleted_at IS NULL ";

        $data = \DB::select($sql);

        $total = $data[0]->total ?: 0;
        $assisted = $data[0]->assisted ?: 0;

        return [
            'total' => $total,
            'assisted' => $assisted,
            'requested' => $total - $assisted,
        ];
    }

    public function beneficiariesByBarangayTotal($company)
    {
        $slugType = addslashes('App\Models\Beneficiary');

        $sql = "SELECT ";
        $sql .= "COUNT(*) AS total, ";
        $sql .= "ben.province_id, ";
        $sql .= "ben.city_id, ";
        $sql .= "ben.barangay_id, ";
        $sql .= "(SELECT name FROM provinces WHERE provinces.prov_code = ben.province_id) AS province_name, ";
        $sql .= "(SELECT name FROM cities WHERE cities.city_code = ben.city_id) AS city_name, ";
        $sql .= "(SELECT name FROM barangays WHERE barangays.id = ben.barangay_id) AS barangay_name ";
        $sql .= "FROM beneficiaries ben ";
        $sql .= "LEFT JOIN barangays brgy ON brgy.id = ben.barangay_id ";
        $sql .= "WHERE ben.company_id = {$company->id} ";
        $sql .= "AND ben.deleted_at IS NULL ";
        $sql .= "GROUP BY ben.province_id, ben.city_id, ben.barangay_id ";
        $sql .= "ORDER BY total DESC, brgy.name ASC";

        $data = \DB::select($sql);

        return [
            'total' => count($data)
        ];
    }

    public function beneficiariesByBirthDateList($date, $company)
    {
        $slugType = addslashes('App\Models\Beneficiary');

        $sql = "SELECT ";
        $sql .= "CONCAT(benef.first_name, ' ', COALESCE(benef.middle_name, ''), ' ', benef.last_name) AS full_name, ";
        $sql .= "benef.date_of_birth AS date_of_birth, ";
        $sql .= "benef.mobile_no AS mobile_number ";
        $sql .= "FROM beneficiaries benef ";
        $sql .= "WHERE benef.company_id = {$company->id} ";
        $sql .= "AND MONTH(benef.date_of_birth) = MONTH('{$date}') ";
        $sql .= "AND DAY(benef.date_of_birth) = DAY('{$date}') ";
        $sql .= "AND benef.date_of_birth != '1970-01-01' ";
        $sql .= "AND benef.deleted_at IS NULL ";

        $data = \DB::select($sql);

        return $data;
    }

    public function beneficiariesByBarangayReport($company, $barangayId)
    {
        $sql = "SELECT ";
        $sql .= "COUNT(*) AS total, ";
        $sql .= "SUM(CASE WHEN benef.is_priority = 1 THEN 1 END) AS priorities, ";
        $sql .= "SUM(CASE WHEN benef.is_household = 1 THEN 1 END) AS household, ";
        $sql .= "SUM(CASE WHEN benef.is_officer = 1 THEN 1 END) AS officers ";
        $sql .= "FROM beneficiaries benef ";
        $sql .= "WHERE benef.company_id = {$company->id} ";
        $sql .= "AND benef.barangay_id = {$barangayId} ";

        $sql .= "AND benef.deleted_at IS NULL ";

        $data = \DB::select($sql);

        return [
            'total' => $data[0]->total ?: 0,
            'priorities' => $data[0]->priorities ?: 0,
            'household' => $data[0]->household ?: 0,
            'officers' => $data[0]->officers ?: 0,
        ];
    }

    public function networksByBarangayReport($company, $barangayId)
    {
        $sql = "SELECT ";
        $sql .= "COUNT(*) AS total ";
        $sql .= "FROM beneficiary_networks bNetwork ";
        $sql .= "LEFT JOIN beneficiaries benef ON benef.id = bNetwork.beneficiary_id ";
        $sql .= "WHERE (bNetwork.company_id = {$company->id} ";
        $sql .= "AND bNetwork.deleted_at IS NULL) ";
        $sql .= "AND benef.barangay_id = {$barangayId} ";

        $data = \DB::select($sql);

        return [
            'total' => $data[0]->total ?: 0,
        ];
    }

    public function incentivesByBarangayReport($company, $barangayId)
    {
        $sql = "SELECT ";
        $sql .= "COUNT(*) AS total, ";
        $sql .= "SUM(CASE WHEN bIncentive.mode = 1 THEN bIncentive.points END) AS additional, ";
        $sql .= "SUM(CASE WHEN bIncentive.mode = 2 THEN bIncentive.points END) AS deduction ";
        $sql .= "FROM beneficiary_incentives bIncentive ";
        $sql .= "LEFT JOIN beneficiaries benef ON benef.id = bIncentive.beneficiary_id ";
        $sql .= "WHERE (bIncentive.company_id = {$company->id} ";
        $sql .= "AND bIncentive.deleted_at IS NULL) ";
        $sql .= "AND benef.barangay_id = {$barangayId} ";

        $data = \DB::select($sql);

        $additional = $data[0]->additional ?: 0;
        $deduction = $data[0]->deduction ?: 0;
        $total = $additional - $deduction;

        return [
            'total' => $total ?: 0,
            'addtional' => $additional ?: 0,
            'deduction' => $deduction ?: 0,
        ];
    }

    public function patientsByBarangayReport($company, $barangayId)
    {
        $sql = "SELECT ";
        $sql .= "COUNT(*) AS total ";
        $sql .= "FROM beneficiary_patients bPatient ";
        $sql .= "LEFT JOIN beneficiaries benef ON benef.id = bPatient.beneficiary_id ";
        $sql .= "WHERE (bPatient.company_id = {$company->id} ";
        $sql .= "AND bPatient.deleted_at IS NULL) ";
        $sql .= "AND benef.barangay_id = {$barangayId} ";

        $data = \DB::select($sql);

        return [
            'total' => $data[0]->total ?: 0,
        ];
    }

    public function assistancesByBarangayReport($company, $barangayId)
    {
        $sql = "SELECT ";
        $sql .= "COUNT(*) AS total, ";
        $sql .= "SUM(CASE WHEN bAssistance.is_assisted = 0 THEN 1 END) AS requested, ";
        $sql .= "SUM(CASE WHEN bAssistance.is_assisted = 1 THEN 1 END) AS assisted ";
        $sql .= "FROM beneficiary_assistances bAssistance ";
        $sql .= "LEFT JOIN beneficiaries benef ON benef.id = bAssistance.beneficiary_id ";
        $sql .= "WHERE (bAssistance.company_id = {$company->id} ";
        $sql .= "AND bAssistance.deleted_at IS NULL) ";
        $sql .= "AND benef.barangay_id = {$barangayId} ";

        $data = \DB::select($sql);

        return [
            'total' => $data[0]->total ?: 0,
            'requested' => $data[0]->requested ?: 0,
            'assisted' => $data[0]->assisted ?: 0,
        ];
    }

    public function barangaysSummaryReport($request, $company, $provinceId)
    {
        $beneficiariesCountSql = "SELECT COUNT(*) AS total FROM beneficiaries where company_id = {$company->id} AND province_id = {$provinceId} ";

        if ($provinceId == '0314'):
            $beneficiariesCountSql .= " AND city_id = '031420' ";
        endif;

        $beneficiariesCountData = \DB::select($beneficiariesCountSql);


        $barangayCountSql = "SELECT COUNT(*) AS total FROM barangays where prov_code = {$provinceId} ";

        if ($provinceId == '0314'):
            $barangayCountSql .= " AND city_code = '031420' ";
        endif;

        $barangayCountData = \DB::select($barangayCountSql);

        $page = $request->has('page') ? $request->query('page') : 1;
        $sort = $request->get('sort') ?: [];

        $total = $barangayCountData[0]->total;

        $limit = 10;

        $offSet = ($page * $limit) - $limit;

        $nextPageCount = ($offSet + $limit) < $total
            ? $page + 1
            : null;

        $nextPageUrl = env('APP_URL') . $request->getRequestUri();
        $nextPageUrl = $nextPageCount
            ? str_replace("page={$page}", "page=$nextPageCount", $nextPageUrl)
            : null;


        $sql = "SELECT ";
        $sql .= "barangay.id AS id, ";
        $sql .= "barangay.name AS barangay_name, ";
        $sql .= "(SELECT name FROM provinces WHERE prov_code = barangay.prov_code) AS province_name, ";
        $sql .= "(SELECT name FROM cities WHERE city_code = barangay.city_code) AS city_name, ";
        $sql .= "COUNT(benef.id) AS beneficiaries, ";
        $sql .= "COALESCE(SUM(CASE WHEN benef.is_officer = 1 THEN 1 END), 0) AS officers, ";
        $sql .= "COALESCE(SUM(CASE WHEN benef.is_household = 1 THEN 1 END), 0) AS household, ";
        $sql .= "COALESCE(SUM(CASE WHEN benef.is_priority = 1 THEN 1 END), 0) AS priorities, ";
        $sql .= "SUM(COALESCE(bAssistance.assisted, 0)) AS assisted, ";
        $sql .= "SUM(COALESCE(bAssistance.requested, 0)) AS requested, ";
        $sql .= "SUM(COALESCE(bPatient.total, 0)) AS patients, ";
        $sql .= "SUM(COALESCE(bNetwork.total, 0)) AS networks, ";
        $sql .= "SUM(COALESCE(bIncentive.additional, 0)) AS add_incentives, ";
        $sql .= "SUM(COALESCE(bIncentive.deduction, 0)) AS deduct_incentives ";
        $sql .= "FROM barangays barangay ";
        $sql .= "LEFT JOIN beneficiaries benef ON benef.barangay_id = barangay.id AND benef.company_id = {$company->id} AND benef.deleted_at IS NULL ";

        $sql .= "LEFT JOIN ";
        $sql .= "(SELECT beneficiary_id, SUM(CASE WHEN is_assisted = 1 THEN 1 END) AS assisted, SUM(CASE WHEN is_assisted = 0 THEN 1 END) AS requested ";
        $sql .= "FROM beneficiary_assistances ";
        $sql .= "WHERE deleted_at IS NULL ";
        $sql .= "GROUP BY beneficiary_id) bAssistance ON bAssistance.beneficiary_id = benef.id ";

        $sql .= "LEFT JOIN ";
        $sql .= "(SELECT beneficiary_id, COUNT(*) AS total ";
        $sql .= "FROM beneficiary_patients ";
        $sql .= "WHERE deleted_at IS NULL ";
        $sql .= "GROUP BY beneficiary_id) bPatient ON bPatient.beneficiary_id = benef.id ";

        $sql .= "LEFT JOIN ";
        $sql .= "(SELECT beneficiary_id, COUNT(*) AS total ";
        $sql .= "FROM beneficiary_networks ";
        $sql .= "WHERE deleted_at IS NULL ";
        $sql .= "GROUP BY beneficiary_id) bNetwork ON bNetwork.beneficiary_id = benef.id ";

        $sql .= "LEFT JOIN ";
        $sql .= "(SELECT beneficiary_id, SUM(CASE WHEN mode = 1 THEN points END) AS additional, SUM(CASE WHEN mode = 2 THEN points END) AS deduction ";
        $sql .= "FROM beneficiary_incentives ";
        $sql .= "WHERE deleted_at IS NULL ";
        $sql .= "GROUP BY beneficiary_id) bIncentive ON bIncentive.beneficiary_id = benef.id ";

        $sql .= "WHERE barangay.prov_code = {$provinceId} ";

        if ($request->has('search') && $request->query('search')):
            $sql .= "AND barangay.name LIKE '%{$request->query('search')}%'";
        endif;

        $sql .= "GROUP BY barangay.id ";

        if (array_key_exists('beneficiaries', $sort)):

            $sql .= "ORDER BY beneficiaries {$sort['beneficiaries']} ";

        elseif (array_key_exists('officers', $sort)):

            $sql .= "ORDER BY officers {$sort['officers']} ";

        elseif (array_key_exists('networks', $sort)):

            $sql .= "ORDER BY networks {$sort['networks']} ";

        elseif (array_key_exists('requested', $sort)):

            $sql .= "ORDER BY requested {$sort['requested']} ";

        elseif (array_key_exists('assisted', $sort)):

            $sql .= "ORDER BY assisted {$sort['assisted']} ";

        elseif (array_key_exists('patients', $sort)):

            $sql .= "ORDER BY patients {$sort['patients']} ";

        elseif (array_key_exists('priorities', $sort)):

            $sql .= "ORDER BY priorities {$sort['priorities']} ";

        else:

            $sql .= "ORDER BY barangay.name ASC ";

        endif;

        $sql .= "LIMIT {$limit} OFFSET {$offSet} ";

        $data = \DB::select($sql);

        return [
            'data' => $data,
            'total' => $total,
            'from' => $offSet + 1,
            'to' => $offSet + $limit,
            'current_page' => $page,
            'next_page_url' => $nextPageUrl,
            'beneficiaries' => $beneficiariesCountData[0]->total ?: 0
        ];
    }

    public function barangaysAssistancesSummaryReport($request, $company, $provinceId)
    {
        $assistancesCountSql = "SELECT COUNT(*) AS total FROM beneficiary_assistances where company_id = {$company->id} ";

        if (isset($request->get('filter')['cityCode'])):
            $assistancesCountSql .= "AND city_id = '{$request->get('filter')['cityCode']}' ";
        endif;

        if (isset($request->get('filter')['assistanceYear'])):
            $assistancesCountSql .= "AND YEAR(assistance_date) = {$request->get('filter')['assistanceYear']} ";
        endif;

        $assistancesCountData = \DB::select($assistancesCountSql);

        $barangayCountSql = "SELECT COUNT(*) AS total FROM barangays where prov_code = {$provinceId} ";

        if (isset($request->get('filter')['cityCode'])):
            $barangayCountSql .= "AND city_code = '{$request->get('filter')['cityCode']}' ";
        endif;

        $barangayCountData = \DB::select($barangayCountSql);

        $page = $request->has('page') ? $request->query('page') : 1;
        $sort = $request->get('sort') ?: [];

        $total = $barangayCountData[0]->total;

        $limit = 10;

        $offSet = ($page * $limit) - $limit;

        $nextPageCount = ($offSet + $limit) < $total
            ? $page + 1
            : null;

        $nextPageUrl = env('APP_URL') . $request->getRequestUri();
        $nextPageUrl = $nextPageCount
            ? str_replace("page={$page}", "page=$nextPageCount", $nextPageUrl)
            : null;

        $sql = "SELECT ";
        $sql .= "brgy.id AS id, ";
        $sql .= "brgy.name AS barangay_name, ";
        $sql .= "(SELECT name FROM provinces WHERE prov_code = brgy.prov_code) AS province_name, ";
        $sql .= "(SELECT name FROM cities WHERE city_code = brgy.city_code) AS city_name, ";
        $sql .= "COALESCE(COUNT(bAssistance.id), 0) AS assistances, ";
        $sql .= "COALESCE(SUM(bAssistance.assistance_amount), 0) AS assistances_amount ";
        $sql .= "FROM barangays brgy ";

        $sql .= "LEFT JOIN beneficiary_assistances bAssistance ON bAssistance.barangay_id = brgy.id AND bAssistance.company_id = {$company->id} AND bAssistance.deleted_at IS NULL ";

        $sql .= "WHERE brgy.prov_code = {$provinceId} ";

        if (isset($request->get('filter')['cityCode'])):
            $sql .= "AND bAssistance.city_id = '{$request->get('filter')['cityCode']}' ";
        endif;

        if (isset($request->get('filter')['assistanceYear'])):
            $sql .= "AND YEAR(bAssistance.assistance_date) = {$request->get('filter')['assistanceYear']} ";
        endif;

        if ($request->has('search') && $request->query('search')):
            $sql .= "AND brgy.name LIKE '%{$request->query('search')}%'";
        endif;

        if (isset($request->get('filter')['cityCode'])):
            $sql .= "AND brgy.city_code = '{$request->get('filter')['cityCode']}' ";
        endif;

        $sql .= "GROUP BY brgy.id ";


        if (array_key_exists('assistances', $sort)):

            $sql .= "ORDER BY assistances {$sort['assistances']} ";

        elseif (array_key_exists('barangay', $sort)):

            $sql .= "ORDER BY brgy.name {$sort['barangay']} ";

        else:

            $sql .= "ORDER BY brgy.name ASC ";

        endif;


        $sql .= "LIMIT {$limit} OFFSET {$offSet} ";

        $data = \DB::select($sql);

        return [
            'data' => $data,
            'total' => $total,
            'from' => $offSet + 1,
            'to' => $offSet + $limit,
            'current_page' => $page,
            'next_page_url' => $nextPageUrl,
            'assistances' => $assistancesCountData[0]->total ?: 0,
        ];
    }

    public function beneficiariesGroupByDate($request, $dates, $company)
    {
        $from = (new \Carbon\Carbon($dates['from']))->format('Y-m-d');
        $to = (new \Carbon\Carbon($dates['to']))->format('Y-m-d');

        $filters = $request->get('filter');

        $sql = "SELECT ";
        $sql .= "benef.date_registered AS date, ";
        $sql .= "COUNT(benef.id) AS total ";
        $sql .= "FROM beneficiaries benef ";
        $sql .= "WHERE benef.company_id = {$company->id} ";
        $sql .= "AND (benef.date_registered = '{$from}' OR benef.date_registered = '{$to}' OR benef.date_registered BETWEEN '{$from}' AND '{$to}') ";
        $sql .= "AND benef.deleted_at IS NULL ";

        if ($filters):
            if (array_key_exists('barangay', $filters)):

                $sql .= "AND benef.barangay_id = {$filters['barangay']} ";

            endif;
        endif;


        $sql .= "GROUP BY benef.date_registered ";
        $sql .= "ORDER BY benef.date_registered ASC ";

        $data = \DB::select($sql);

        return $data;
    }

    public function assistancesGroupByDate($request, $dates, $company)
    {
        $from = (new \Carbon\Carbon($dates['from']))->format('Y-m-d');
        $to = (new \Carbon\Carbon($dates['to']))->format('Y-m-d');

        $filters = $request->get('filter');

        $sql = "SELECT ";
        $sql .= "assistance.assistance_date AS date, ";
        $sql .= "COUNT(assistance.id) AS total ";
        $sql .= "FROM beneficiary_assistances assistance ";
        $sql .= "WHERE assistance.company_id = {$company->id} ";
        $sql .= "AND assistance.assistance_date = '{$from}' OR assistance.assistance_date = '{$to}' OR assistance.assistance_date BETWEEN '{$from}' AND '{$to}' ";
        $sql .= "AND assistance.is_assisted = 0 ";
        $sql .= "AND assistance.deleted_at IS NULL ";

        if ($filters):
            if (array_key_exists('barangay', $filters)):

                $sql .= "AND assistance.barangay_id = {$filters['barangay']} ";

            endif;
        endif;

        $sql .= "GROUP BY assistance.assistance_date ";
        $sql .= "ORDER BY assistance.assistance_date ASC ";

        $data = \DB::select($sql);

        return $data;
    }

    public function patientsGroupByDate($request, $dates, $company)
    {
        $from = (new \Carbon\Carbon($dates['from']))->format('Y-m-d');
        $to = (new \Carbon\Carbon($dates['to']))->format('Y-m-d');

        $filters = $request->get('filter');

        $sql = "SELECT ";
        $sql .= "patient.patient_date AS date, ";
        $sql .= "COUNT(patient.id) AS total ";
        $sql .= "FROM beneficiary_patients patient ";
        $sql .= "LEFT JOIN beneficiaries benef ON benef.id = patient.beneficiary_id ";
        $sql .= "WHERE patient.company_id = {$company->id} ";
        $sql .= "AND (patient.patient_date = '{$from}' OR patient.patient_date = '{$to}' OR patient.patient_date BETWEEN '{$from}' AND '{$to}') ";
        $sql .= "AND patient.deleted_at IS NULL ";

        if ($filters):
            if (array_key_exists('barangay', $filters)):

                $sql .= "AND benef.barangay_id = {$filters['barangay']} ";

            endif;
        endif;

        $sql .= "GROUP BY patient.patient_date ";
        $sql .= "ORDER BY patient.patient_date ASC ";

        $data = \DB::select($sql);

        return $data;
    }

    public function beneficiariesGroupByDay($request, $dates, $company)
    {
        $from = (new \Carbon\Carbon($dates['from']))->format('Y-m-d');
        $to = (new \Carbon\Carbon($dates['to']))->format('Y-m-d');

        $filters = $request->get('filter');

        $sql = "SELECT ";
        $sql .= "DATE(benef.date_registered) as day, ";
        $sql .= "COUNT(benef.id) AS total ";
        $sql .= "FROM beneficiaries benef ";
        $sql .= "WHERE benef.company_id = {$company->id} ";
        $sql .= "AND (benef.date_registered = '{$from}' OR benef.date_registered = '{$to}' OR benef.date_registered BETWEEN '{$from}' AND '{$to}') ";
        $sql .= "AND benef.deleted_at IS NULL ";

        if ($filters):
            if (array_key_exists('barangay', $filters)):

                $sql .= "AND benef.barangay_id = {$filters['barangay']} ";

            endif;
        endif;

        $sql .= "GROUP BY DATE(benef.date_registered) ";
        $sql .= "ORDER BY DATE(benef.date_registered) ASC ";

        $data = \DB::select($sql);

        return $data;
    }

    public function beneficiariesGroupByWeek($request, $dates, $company)
    {
        $from = (new \Carbon\Carbon($dates['from']))->format('Y-m-d');
        $to = (new \Carbon\Carbon($dates['to']))->format('Y-m-d');

        $filters = $request->get('filter');

        $sql = "SELECT ";
        $sql .= "YEARWEEK(benef.date_registered, 1) as week, ";
        $sql .= "COUNT(benef.id) AS total ";
        $sql .= "FROM beneficiaries benef ";
        $sql .= "WHERE benef.company_id = {$company->id} ";
        $sql .= "AND (benef.date_registered = '{$from}' OR benef.date_registered = '{$to}' OR benef.date_registered BETWEEN '{$from}' AND '{$to}') ";
        $sql .= "AND benef.deleted_at IS NULL ";

        if ($filters):
            if (array_key_exists('barangay', $filters)):

                $sql .= "AND benef.barangay_id = {$filters['barangay']} ";

            endif;
        endif;

        $sql .= "GROUP BY YEARWEEK(benef.date_registered, 1) ";
        $sql .= "ORDER BY YEARWEEK(benef.date_registered, 1) ASC ";

        $data = \DB::select($sql);

        return $data;
    }
    public function beneficiariesGroupByMonth($request, $dates, $company)
    {
        $from = (new \Carbon\Carbon($dates['from']))->format('Y-m-d');
        $to = (new \Carbon\Carbon($dates['to']))->format('Y-m-d');

        $filters = $request->get('filter');

        $sql = "SELECT ";
        $sql .= "YEAR(benef.date_registered) as year, ";
        $sql .= "MONTH(benef.date_registered) as month, ";
        $sql .= "COUNT(benef.id) AS total ";
        $sql .= "FROM beneficiaries benef ";
        $sql .= "WHERE benef.company_id = {$company->id} ";
        $sql .= "AND (benef.date_registered = '{$from}' OR benef.date_registered = '{$to}' OR benef.date_registered BETWEEN '{$from}' AND '{$to}') ";
        $sql .= "AND benef.deleted_at IS NULL ";

        if ($filters):
            if (array_key_exists('barangay', $filters)):

                $sql .= "AND benef.barangay_id = {$filters['barangay']} ";

            endif;
        endif;


        $sql .= "GROUP BY YEAR(benef.date_registered), MONTH(benef.date_registered) ";
        $sql .= "ORDER BY YEAR(benef.date_registered) ASC, MONTH(benef.date_registered) ASC ";

        $data = \DB::select($sql);

        return $data;
    }

    public function assistancesGroupByDay($request, $dates, $company)
    {
        $from = (new \Carbon\Carbon($dates['from']))->format('Y-m-d');
        $to = (new \Carbon\Carbon($dates['to']))->format('Y-m-d');

        $filters = $request->get('filter');

        $sql = "SELECT ";
        $sql .= "DATE(assistance.assistance_date) as day, ";
        $sql .= "COUNT(assistance.id) AS total ";
        $sql .= "FROM beneficiary_assistances assistance ";
        $sql .= "WHERE assistance.company_id = {$company->id} ";
        $sql .= "AND (assistance.assistance_date = '{$from}' OR assistance.assistance_date = '{$to}' OR assistance.assistance_date BETWEEN '{$from}' AND '{$to}') ";
        $sql .= "AND assistance.deleted_at IS NULL ";

        if ($filters):
            if (array_key_exists('barangay', $filters)):

                $sql .= "AND assistance.barangay_id = {$filters['barangay']} ";

            endif;
        endif;

        $sql .= "GROUP BY DATE(assistance.assistance_date) ";
        $sql .= "ORDER BY DATE(assistance.assistance_date) ASC ";

        $data = \DB::select($sql);

        return $data;
    }
    public function assistancesGroupByWeek($request, $dates, $company)
    {
        $from = (new \Carbon\Carbon($dates['from']))->format('Y-m-d');
        $to = (new \Carbon\Carbon($dates['to']))->format('Y-m-d');

        $filters = $request->get('filter');

        $sql = "SELECT ";
        $sql .= "YEARWEEK(assistance.assistance_date, 1) as week, ";
        $sql .= "COUNT(assistance.id) AS total ";
        $sql .= "FROM beneficiary_assistances assistance ";
        $sql .= "WHERE assistance.company_id = {$company->id} ";
        $sql .= "AND (assistance.assistance_date = '{$from}' OR assistance.assistance_date = '{$to}' OR assistance.assistance_date BETWEEN '{$from}' AND '{$to}') ";
        $sql .= "AND assistance.deleted_at IS NULL ";

        if ($filters):
            if (array_key_exists('barangay', $filters)):

                $sql .= "AND assistance.barangay_id = {$filters['barangay']} ";

            endif;
        endif;

        $sql .= "GROUP BY YEARWEEK(assistance.assistance_date, 1) ";
        $sql .= "ORDER BY YEARWEEK(assistance.assistance_date, 1) ASC ";

        $data = \DB::select($sql);

        return $data;
    }
    public function assistancesGroupByMonth($request, $dates, $company)
    {
        $from = (new \Carbon\Carbon($dates['from']))->format('Y-m-d');
        $to = (new \Carbon\Carbon($dates['to']))->format('Y-m-d');

        $filters = $request->get('filter');

        $sql = "SELECT ";
        $sql .= "YEAR(assistance.assistance_date) as year, ";
        $sql .= "MONTH(assistance.assistance_date) as month, ";
        $sql .= "COUNT(assistance.id) AS total ";
        $sql .= "FROM beneficiary_assistances assistance ";
        $sql .= "WHERE assistance.company_id = {$company->id} ";
        $sql .= "AND (assistance.assistance_date = '{$from}' OR assistance.assistance_date = '{$to}' OR assistance.assistance_date BETWEEN '{$from}' AND '{$to}') ";
        $sql .= "AND assistance.deleted_at IS NULL ";

        if ($filters):
            if (array_key_exists('barangay', $filters)):

                $sql .= "AND assistance.barangay_id = {$filters['barangay']} ";

            endif;
        endif;

        $sql .= "GROUP BY YEAR(assistance.assistance_date), MONTH(assistance.assistance_date) ";
        $sql .= "ORDER BY YEAR(assistance.assistance_date) ASC, MONTH(assistance.assistance_date) ASC ";

        $data = \DB::select($sql);

        return $data;
    }

    public function patientsGroupByDay($request, $dates, $company)
    {
        $from = (new \Carbon\Carbon($dates['from']))->format('Y-m-d');
        $to = (new \Carbon\Carbon($dates['to']))->format('Y-m-d');

        $filters = $request->get('filter');

        $sql = "SELECT ";
        $sql .= "DATE(patient.patient_date) as day, ";
        $sql .= "COUNT(patient.id) AS total ";
        $sql .= "FROM beneficiary_patients patient ";
        $sql .= "LEFT JOIN beneficiaries benef ON benef.id = patient.beneficiary_id ";
        $sql .= "WHERE patient.company_id = {$company->id} ";
        $sql .= "AND (patient.patient_date = '{$from}' OR patient.patient_date = '{$to}' OR patient.patient_date BETWEEN '{$from}' AND '{$to}') ";
        $sql .= "AND patient.deleted_at IS NULL ";

        if ($filters):
            if (array_key_exists('barangay', $filters)):

                $sql .= "AND benef.barangay_id = {$filters['barangay']} ";

            endif;
        endif;

        $sql .= "GROUP BY DATE(patient.patient_date) ";
        $sql .= "ORDER BY DATE(patient.patient_date) ASC ";

        $data = \DB::select($sql);

        return $data;
    }
    public function patientsGroupByWeek($request, $dates, $company)
    {
        $from = (new \Carbon\Carbon($dates['from']))->startOfWeek()->format('Y-m-d');
        $to = (new \Carbon\Carbon($dates['to']))->endOfWeek()->format('Y-m-d');

        $filters = $request->get('filter');

        $sql = "SELECT ";
        $sql .= "YEARWEEK(patient.patient_date, 1) as week, ";
        $sql .= "COUNT(patient.id) AS total ";
        $sql .= "FROM beneficiary_patients patient ";
        $sql .= "LEFT JOIN beneficiaries benef ON benef.id = patient.beneficiary_id ";
        $sql .= "WHERE patient.company_id = {$company->id} ";
        $sql .= "AND (patient.patient_date = '{$from}' OR patient.patient_date = '{$to}' OR patient.patient_date BETWEEN '{$from}' AND '{$to}') ";
        $sql .= "AND patient.deleted_at IS NULL ";

        if ($filters):
            if (array_key_exists('barangay', $filters)):

                $sql .= "AND benef.barangay_id = {$filters['barangay']} ";

            endif;
        endif;

        $sql .= "GROUP BY YEARWEEK(patient.patient_date, 1) ";
        $sql .= "ORDER BY YEARWEEK(patient.patient_date, 1) ASC ";

        $data = \DB::select($sql);

        return $data;
    }
    public function patientsGroupByMonth($request, $dates, $company)
    {
        $from = (new \Carbon\Carbon($dates['from']))->format('Y-m-d');
        $to = (new \Carbon\Carbon($dates['to']))->format('Y-m-d');

        $filters = $request->get('filter');

        $sql = "SELECT ";
        $sql .= "YEAR(patient.patient_date) as year, ";
        $sql .= "MONTH(patient.patient_date) as month, ";
        $sql .= "COUNT(patient.id) AS total ";
        $sql .= "FROM beneficiary_patients patient ";
        $sql .= "LEFT JOIN beneficiaries benef ON benef.id = patient.beneficiary_id ";
        $sql .= "WHERE patient.company_id = {$company->id} ";
        $sql .= "AND (patient.patient_date = '{$from}' OR patient.patient_date = '{$to}' OR patient.patient_date BETWEEN '{$from}' AND '{$to}') ";
        $sql .= "AND patient.deleted_at IS NULL ";

        if ($filters):
            if (array_key_exists('barangay', $filters)):

                $sql .= "AND benef.barangay_id = {$filters['barangay']} ";

            endif;
        endif;

        $sql .= "GROUP BY YEAR(patient.patient_date), MONTH(patient.patient_date) ";
        $sql .= "ORDER BY YEAR(patient.patient_date) ASC, MONTH(patient.patient_date) ASC ";

        $data = \DB::select($sql);

        return $data;
    }
    public function assistancesByTypeGroupByDay($request, $dates, $company)
    {
        $from = (new \Carbon\Carbon($dates['from']))->format('Y-m-d');
        $to = (new \Carbon\Carbon($dates['to']))->format('Y-m-d');

        $filters = $request->get('filter');

        $sql = "SELECT ";
        $sql .= "DATE(assistance.assistance_date) as day, ";
        $sql .= "COALESCE(SUM(CASE WHEN assistance.assistance_type LIKE '%guaran%' THEN 1 END), 0) AS guarantee, ";
        $sql .= "COALESCE(SUM(CASE WHEN assistance.assistance_type LIKE '%medic%' THEN 1 END), 0) AS medical, ";
        $sql .= "COALESCE(SUM(CASE WHEN assistance.assistance_type LIKE '%financ%' THEN 1 END), 0) AS financial, ";
        $sql .= "COALESCE(SUM(CASE WHEN assistance.assistance_type LIKE '%burial%' THEN 1 END), 0) AS burial, ";
        $sql .= "COALESCE(SUM(CASE WHEN assistance.assistance_type LIKE '%scholar%' THEN 1 END), 0) AS scholar, ";
        $sql .= "COALESCE(SUM(CASE WHEN assistance.assistance_type LIKE '%train%' THEN 1 END), 0) AS training, ";
        $sql .= "COALESCE(SUM(CASE WHEN assistance.assistance_type LIKE '%infra%' THEN 1 END), 0) AS infrastructure, ";
        $sql .= "COUNT(assistance.id) AS total ";
        $sql .= "FROM beneficiary_assistances assistance ";
        $sql .= "WHERE assistance.company_id = {$company->id} ";
        $sql .= "AND (assistance.assistance_date = '{$from}' OR assistance.assistance_date = '{$to}' OR assistance.assistance_date BETWEEN '{$from}' AND '{$to}') ";
        $sql .= "AND assistance.deleted_at IS NULL ";

        if ($filters):
            if (array_key_exists('barangay', $filters)):

                $sql .= "AND assistance.barangay_id = {$filters['barangay']} ";

            endif;
        endif;

        $sql .= "GROUP BY DATE(assistance.assistance_date) ";
        $sql .= "ORDER BY DATE(assistance.assistance_date) ASC ";
        // \Log::info($sql);
        $data = \DB::select($sql);

        return $data;
    }
    public function assistancesByTypeGroupByWeek($request, $dates, $company)
    {
        $from = (new \Carbon\Carbon($dates['from']))->startOfWeek()->format('Y-m-d');
        $to = (new \Carbon\Carbon($dates['to']))->endOfWeek()->format('Y-m-d');

        $filters = $request->get('filter');

        $sql = "SELECT ";
        $sql .= "YEARWEEK(assistance.assistance_date, 1) as week, ";
        $sql .= "COALESCE(SUM(CASE WHEN assistance.assistance_type LIKE '%guaran%' THEN 1 END), 0) AS guarantee, ";
        $sql .= "COALESCE(SUM(CASE WHEN assistance.assistance_type LIKE '%medic%' THEN 1 END), 0) AS medical, ";
        $sql .= "COALESCE(SUM(CASE WHEN assistance.assistance_type LIKE '%financ%' THEN 1 END), 0) AS financial, ";
        $sql .= "COALESCE(SUM(CASE WHEN assistance.assistance_type LIKE '%burial%' THEN 1 END), 0) AS burial, ";
        $sql .= "COALESCE(SUM(CASE WHEN assistance.assistance_type LIKE '%scholar%' THEN 1 END), 0) AS scholar, ";
        $sql .= "COALESCE(SUM(CASE WHEN assistance.assistance_type LIKE '%train%' THEN 1 END), 0) AS training, ";
        $sql .= "COALESCE(SUM(CASE WHEN assistance.assistance_type LIKE '%infra%' THEN 1 END), 0) AS infrastructure, ";
        $sql .= "COUNT(assistance.id) AS total ";
        $sql .= "FROM beneficiary_assistances assistance ";
        $sql .= "WHERE assistance.company_id = {$company->id} ";
        $sql .= "AND (assistance.assistance_date = '{$from}' OR assistance.assistance_date = '{$to}' OR assistance.assistance_date BETWEEN '{$from}' AND '{$to}') ";
        $sql .= "AND assistance.deleted_at IS NULL ";

        if ($filters):
            if (array_key_exists('barangay', $filters)):

                $sql .= "AND assistance.barangay_id = {$filters['barangay']} ";

            endif;
        endif;

        $sql .= "GROUP BY YEARWEEK(assistance.assistance_date, 1) ";
        $sql .= "ORDER BY YEARWEEK(assistance.assistance_date, 1) ASC ";

        $data = \DB::select($sql);

        return $data;
    }
    public function assistancesByTypeGroupByMonth($request, $dates, $company)
    {
        $from = (new \Carbon\Carbon($dates['from']))->format('Y-m-d');
        $to = (new \Carbon\Carbon($dates['to']))->format('Y-m-d');

        $filters = $request->get('filter');

        $sql = "SELECT ";
        $sql .= "YEAR(assistance.assistance_date) as year, ";
        $sql .= "MONTH(assistance.assistance_date) as month, ";
        $sql .= "COALESCE(SUM(CASE WHEN assistance.assistance_type LIKE '%guaran%' THEN 1 END), 0) AS guarantee, ";
        $sql .= "COALESCE(SUM(CASE WHEN assistance.assistance_type LIKE '%medic%' THEN 1 END), 0) AS medical, ";
        $sql .= "COALESCE(SUM(CASE WHEN assistance.assistance_type LIKE '%financ%' THEN 1 END), 0) AS financial, ";
        $sql .= "COALESCE(SUM(CASE WHEN assistance.assistance_type LIKE '%burial%' THEN 1 END), 0) AS burial, ";
        $sql .= "COALESCE(SUM(CASE WHEN assistance.assistance_type LIKE '%scholar%' THEN 1 END), 0) AS scholar, ";
        $sql .= "COALESCE(SUM(CASE WHEN assistance.assistance_type LIKE '%train%' THEN 1 END), 0) AS training, ";
        $sql .= "COALESCE(SUM(CASE WHEN assistance.assistance_type LIKE '%infra%' THEN 1 END), 0) AS infrastructure, ";
        $sql .= "COUNT(assistance.id) AS total ";
        $sql .= "FROM beneficiary_assistances assistance ";
        $sql .= "WHERE assistance.company_id = {$company->id} ";
        $sql .= "AND (assistance.assistance_date = '{$from}' OR assistance.assistance_date = '{$to}' OR assistance.assistance_date BETWEEN '{$from}' AND '{$to}') ";
        $sql .= "AND assistance.deleted_at IS NULL ";

        if ($filters):
            if (array_key_exists('barangay', $filters)):

                $sql .= "AND assistance.barangay_id = {$filters['barangay']} ";

            endif;
        endif;

        $sql .= "GROUP BY YEAR(assistance.assistance_date), MONTH(assistance.assistance_date) ";
        $sql .= "ORDER BY YEAR(assistance.assistance_date) ASC, MONTH(assistance.assistance_date) ASC ";

        $data = \DB::select($sql);

        return $data;
    }

    public function topBeneficiaryNetworks($request, $company)
    {
        $slugType = addslashes('App\Models\Beneficiary');
        $filters = $request->get('filter');

        $sql = "SELECT ";
        $sql .= "benef.id, ";
        $sql .= "slug.code AS slugCode, ";
        $sql .= "benef.code AS code, ";
        $sql .= "CONCAT(benef.last_name, ', ', benef.first_name, ' ', COALESCE(benef.middle_name, '')) AS full_name, ";
        $sql .= "CASE WHEN benef.gender = 1 THEN 'MALE' ";
        $sql .= "WHEN benef.gender = 2 THEN 'FEMALE' END AS gender, ";
        $sql .= "benef.date_of_birth AS date_of_birth, ";
        $sql .= "benef.incentive AS incentive, ";
        $sql .= "COUNT(bNetwork.id) AS total ";
        $sql .= "FROM beneficiaries benef ";
        $sql .= "LEFT JOIN beneficiary_networks bNetwork ON bNetwork.parent_beneficiary_id = benef.id AND bNetwork.deleted_at IS NULL ";
        $sql .= "LEFT JOIN slugs slug ON slug.slug_id = benef.id AND slug_type = '{$slugType}' ";
        $sql .= "WHERE benef.company_id = {$company->id} ";
        $sql .= "AND benef.deleted_at IS NULL ";
        $sql .= "GROUP BY benef.id, slug.code ";
        $sql .= "HAVING total > 0 ";
        $sql .= "ORDER BY total DESC ";
        $sql .= "LIMIT 50 ";

        $data = \DB::select($sql);

        return $data;
    }

    public function officersNetworksList($request, $company)
    {
        $slugType = addslashes('App\Models\Beneficiary');
        $filters = $request->get('filter');

        $sql = "SELECT ";
        $sql .= "benef.id, ";
        $sql .= "slug.code AS slugCode, ";
        $sql .= "benef.code AS code, ";
        $sql .= "CONCAT(benef.last_name, ', ', benef.first_name, ' ', COALESCE(benef.middle_name, '')) AS full_name, ";
        $sql .= "CASE WHEN benef.gender = 1 THEN 'MALE' ";
        $sql .= "WHEN benef.gender = 2 THEN 'FEMALE' END AS gender, ";
        $sql .= "benef.date_of_birth AS date_of_birth, ";
        $sql .= "benef.incentive AS incentive, ";
        $sql .= "COUNT(bNetwork.id) AS total ";
        $sql .= "FROM beneficiaries benef ";
        $sql .= "LEFT JOIN beneficiary_networks bNetwork ON bNetwork.parent_beneficiary_id = benef.id AND bNetwork.deleted_at IS NULL ";
        $sql .= "LEFT JOIN slugs slug ON slug.slug_id = benef.id AND slug_type = '{$slugType}' ";
        $sql .= "WHERE benef.company_id = {$company->id} ";
        $sql .= "AND benef.officer_id = 1 ";
        $sql .= "AND benef.deleted_at IS NULL ";
        $sql .= "GROUP BY benef.id, slug.code ";
        $sql .= "HAVING total > 0 ";
        $sql .= "ORDER BY total DESC ";

        $data = \DB::select($sql);

        return $data;
    }

    public function isAccountRelated($company, $companyAccountId)
    {
        $checking = $company->companyAccounts()->where('id', $companyAccountId)->first();

        if (!$checking)
            return abort(404, 'Company account is not related record.');

        return $checking;
    }

    public function isBillingRelated($company, $billingId)
    {
        $checking = $company->invoices()->where('id', $billingId)->first();

        if (!$checking)
            return abort(404, 'Billing is not related record.');

        return $checking;
    }

    public function isSmsTransactionRelated($company, $transactionId)
    {
        $checking = $company->smsTransactions()->where('id', $transactionId)->first();

        if (!$checking)
            return abort(404, 'SMS transaction is not related record.');

        return $checking;
    }

    public function isSmsCreditRelated($company, $creditId)
    {
        $checking = $company->smsCredits()->where('id', $creditId)->first();

        if (!$checking)
            return abort(404, 'SMS credit is not related record.');

        return $checking;
    }

    public function isCallCreditRelated($company, $creditId)
    {
        $checking = $company->callCredits()->where('id', $creditId)->first();

        if (!$checking)
            return abort(404, 'Call credit is not related record.');

        return $checking;
    }

    public function isCallTransactionRelated($company, $transactionId)
    {
        $checking = $company->callTransactions()->where('id', $transactionId)->first();

        if (!$checking)
            return abort(404, 'Call transaction is not related record.');

        return $checking;
    }

    public function isClassificationRelated($company, $classificationId)
    {
        $checking = $company->classifications()->where('id', $classificationId)->first();

        if (!$checking)
            return abort(404, 'Classification is not related record.');

        return $checking;
    }

    public function isOfficerClassificationRelated($company, $classificationId)
    {
        $checking = $company->officerClassifications()->where('id', $classificationId)->first();

        if (!$checking)
            return abort(404, 'Classification is not related record.');

        return $checking;
    }

    public function isMemberRelated($company, $memberId)
    {
        $checking = $company->members()->where('id', $memberId)->first();

        if (!$checking)
            return abort(404, 'Member is not related record.');

        return $checking;
    }

    public function isBeneficiaryRelated($company, $beneficiaryId)
    {
        $checking = $company->beneficiaries()->where('id', $beneficiaryId)->first();

        if (!$checking)
            return abort(404, 'Beneficiary is not related record.');

        return $checking;
    }

    public function isPatientRelated($company, $patientId)
    {
        $checking = $company->beneficiaryPatients()->where('id', $patientId)->first();

        if (!$checking)
            return abort(404, 'Patient is not related record.');

        return $checking;
    }

    public function isAssistanceRelated($company, $assistanceId)
    {
        $checking = $company->beneficiaryAssistances()->where('id', $assistanceId)->first();

        if (!$checking)
            return abort(404, 'Assistance is not related record.');

        return $checking;
    }

    public function isIncentiveRelated($company, $incentiveId)
    {
        $checking = $company->beneficiaryIncentives()->where('id', $incentiveId)->first();

        if (!$checking)
            return abort(404, 'Incentive is not related record.');

        return $checking;
    }

    public function isBarangayRelated($company, $barangayId)
    {
        $checking = $company->barangays()->where('id', $barangayId)->first();

        if (!$checking)
            return abort(404, 'Barangay is not related record.');

        return $checking;
    }

    public function isBarangayAlreadyExist($company, $barangayId)
    {
        $checking = $company->barangays()->where('barangay_id', $barangayId)->first();

        if ($checking)
            return abort(403, 'Barangay already exists.');
    }

    public function isQuestionnaireRelated($company, $questionId)
    {
        $checking = $company->questionnaires()->where('id', $questionId)->first();

        if (!$checking)
            return abort(404, 'Questionnaire is not related record.');

        return $checking;
    }

    public function isAssignatoryRelated($company, $assignatoryId)
    {
        $checking = $company->assignatories()->where('id', $assignatoryId)->first();

        if (!$checking)
            return abort(404, 'Assignatory is not related record.');

        return $checking;
    }

    public function isIdTemplateRelated($company, $templateId)
    {
        $checking = $company->idTemplates()->where('id', $templateId)->first();

        if (!$checking)
            return abort(404, 'ID Template is not related record.');

        return $checking;
    }

    public function isDocumentTemplateRelated($company, $templateId)
    {
        $checking = $company->documentTemplates()->where('id', $templateId)->first();

        if (!$checking)
            return abort(404, 'Document Template is not related record.');

        return $checking;
    }

    public function isVoterRelated($company, $voterId)
    {
        $checking = $company->voters()->where('id', $voterId)->first();

        if (!$checking)
            return abort(404, 'Voter is not related record.');

        return $checking;
    }

    public function checkAndCreateClassification($name, $company)
    {
        $checking = $company->classifications()->where('name', 'LIKE', '%' . $name . '%')->first();

        if (!$checking):

            $company->classifications()->save(
                $this->classificationRepository->new([
                    'name' => $name,
                    'description' => null,
                ], $company)
            );
        endif;
    }

    public function checkSmsServiceStatus($company)
    {
        $smsSetting = $company->smsSetting;

        if (!$smsSetting || !$smsSetting->sms_status)
            return abort(403, $smsSetting->branding_sender_name . 'SMS service is not available on your account. Kindly contact your System Provider to inquire.');
    }

    public function checkDiafaanSmsServiceStatus($company)
    {
        $smsSetting = $company->smsSetting;

        if (!$smsSetting || !$smsSetting->diafaan_status)
            return abort(403, 'Regular SMS service is not available on your account. Kindly contact your System Provider to inquire.');
    }

    public function isCompanySmsServiceActive($company)
    {
        $smsSetting = $company->smsSetting;

        return $smsSetting && $smsSetting->sms_status;
    }

    public function isCompanyDiafaanSmsServiceActive($company)
    {
        $smsSetting = $company->smsSetting;

        return $smsSetting && $smsSetting->diafaan_status;
    }

    public function checkCreditBalance($company, $noOfSms, $message)
    {
        $smsSetting = $company->smsSetting;

        $loadAmount = $company->sms_credit + $smsSetting->credit_threshold;

        $forSendingAmount = $smsSetting->credit_per_branding_sms * $noOfSms;

        if ($loadAmount <= $forSendingAmount):
            return abort(403, 'SMS credit balance is insufficient. Kindly contact your System Provider to reload your account.');
        endif;
    }

    public function isCreditBalanceEnough($company, $noOfSms, $message)
    {
        $smsSetting = $company->smsSetting;

        $loadAmount = $company->sms_credit + $smsSetting->credit_threshold;

        $forSendingAmount = $smsSetting->credit_per_branding_sms * $noOfSms;

        if ($loadAmount <= $forSendingAmount)
            return false;

        return true;
    }

    public function checkCallServiceStatus($company)
    {
        $setting = $company->callSetting;

        if (!$setting || !$setting->call_status)
            return abort(403, 'Call service is not available on your account. Kindly contact your System Provider to inquire.');
    }

    public function isCompanyCallServiceActive($company)
    {
        $callSetting = $company->callSetting;

        return $callSetting && $callSetting->call_service_status;
    }
}
