<?php

namespace App\Http\Repositories\MyCompany;

use Illuminate\Support\Facades\Auth;

use App\Models\BeneficiaryAssistance;

use App\Http\Repositories\Base\BeneficiaryAssistanceRepository as BaseRepository;
use App\Http\Repositories\Base\CompanyRepository;
use App\Http\Repositories\Base\BeneficiaryRepository;
use App\Http\Repositories\Base\PDFRepository;

class AssistanceRepository
{
    public function __construct()
    {
        $this->baseRepository = new BaseRepository;
        $this->companyRepository = new CompanyRepository;
        $this->beneficiaryRepository = new BeneficiaryRepository;
        $this->pdfRepository = new PDFRepository;
    }

    public function downloadReport($request)
    {
        ini_set('max_execution_time', 300);

        $company = Auth::user()->company();

        $file = $company->report_file;

        $fileName = $this->pdfRepository->fileName(
            $request->get('from'),
            $request->get('to'),
            strtolower($company->name) . '-assistances-report',
            '.xlsx'
        );

        $namespace = '\App\Exports\Assistances\\'.$file.'\\AssistanceReport';

        \Excel::store(
            new $namespace($request, $company),
            'storage/exports/' . $fileName,
            env('STORAGE_DISK', 'public')
        );

        return [
            'path' => env('CDN_URL') . '/storage/exports/' . $fileName
        ];
    }

    public function downloadByBarangayReport($request)
    {
        ini_set('max_execution_time', 300);

        $company = Auth::user()->company();

        $file = $company->report_file;

        $fileName = $this->pdfRepository->fileName(
            $request->get('from'),
            $request->get('to'),
            strtolower($company->name) . '-assistance-by-barangay-report',
            '.xlsx'
        );

        if ($company->id == 4): // MAYAP
            $namespace = '\App\Exports\Assistances\\mayap\\AssistanceByBarangayReport';
        else:
            $namespace = '\App\Exports\Assistances\\'.$file.'\\AssistanceByBarangayReport';
        endif;
        

        \Excel::store(
            new $namespace($request, $company),
            'storage/exports/' . $fileName,
            env('STORAGE_DISK', 'public')
        );

        return [
            'path' => env('CDN_URL') . '/storage/exports/' . $fileName
        ];
    }

    public function downloadByPurokReport($request)
    {
        ini_set('max_execution_time', 300);

        $company = Auth::user()->company();

        $file = $company->report_file;

        $fileName = $this->pdfRepository->fileName(
            $request->get('from'),
            $request->get('to'),
            strtolower($company->name) . '-assistance-by-purok-sitio-report',
            '.xlsx'
        );

        if ($company->id == 4): // MAYAP
            $namespace = '\App\Exports\Assistances\\mayap\\AssistanceByPurokReport';
        else:
            $namespace = '\App\Exports\Assistances\\'.$file.'\\AssistanceByPurokReport';
        endif;

        

        \Excel::store(
            new $namespace($request, $company),
            'storage/exports/' . $fileName,
            env('STORAGE_DISK', 'public')
        );

        return [
            'path' => env('CDN_URL') . '/storage/exports/' . $fileName
        ];
    }

    public function downloadByFromReport($request)
    {
        ini_set('max_execution_time', 300);
        
        $company = Auth::user()->company();

        $file = $company->report_file;

        $fileName = $this->pdfRepository->fileName(
            $request->get('from'),
            $request->get('to'),
            strtolower($company->name) . '-assistance-by-from-report',
            '.xlsx'
        );

        $namespace = '\App\Exports\Assistances\\'.$file.'\\AssistanceByFromReport';

        \Excel::store(
            new $namespace($request, $company),
            'storage/exports/' . $fileName,
            env('STORAGE_DISK', 'public')
        );

        return [
            'path' => env('CDN_URL') . '/storage/exports/' . $fileName
        ];
    }

    public function store($request)
    {
        $company = Auth::user()->company();

        $newAssistance = $this->baseRepository->store($request, $company);

        $this->beneficiaryRepository->updateAssistancesCount($newAssistance->beneficiary);

        return $newAssistance;
    }

    public function update($request, $id)
    {
        $company = Auth::user()->company();

        $assistance = $this->companyRepository->isAssistanceRelated($company, $id);

        $assistance->update(
            $this->baseRepository->update($request)
        );

        return (BeneficiaryAssistance::find($assistance->id))->toArrayAssistancesRelated();
    }

    public function destroy($request, $id)
    {
        $company = Auth::user()->company();

        $assistance = $this->companyRepository->isAssistanceRelated($company, $id);

        $beneficiary = $assistance->beneficiary;

        $assistance->delete();

        $this->beneficiaryRepository->updateAssistancesCount($beneficiary);

        return response([
            'message' => 'Data deleted successfully.'
        ], 200);
    }

    public function showOtherAssistances($request, $id)
    {
        $company = Auth::user()->company();

        $assistance = $this->companyRepository->isAssistanceRelated($company, $id);

        $beneficiary = $assistance->beneficiary;

        return $beneficiary->assistances()->orderBy('assistance_date', 'desc')->get();
    }

    public function showAssistancesLocationsList($request)
    {
        $company = Auth::user()->company();
        
        $slugType = addslashes('App\Models\Beneficiary');

        $sql = "SELECT ";
        $sql .= "CASE ";
        $sql .= "WHEN bAssistance.assistance_type LIKE '%guaran%' THEN 'brown' ";
        $sql .= "WHEN bAssistance.assistance_type LIKE '%medic%' THEN 'blue' ";
        $sql .= "WHEN bAssistance.assistance_type LIKE '%financ%' THEN 'green' ";
        $sql .= "WHEN bAssistance.assistance_type LIKE '%burial%' THEN 'gray' ";
        $sql .= "WHEN bAssistance.assistance_type LIKE '%scholar%' THEN 'pink' ";
        $sql .= "WHEN bAssistance.assistance_type LIKE '%train%' THEN 'orange' ";
        $sql .= "WHEN bAssistance.assistance_type LIKE '%infra%' THEN 'yellow' ";
        $sql .= "END AS marker_color, ";
        $sql .= "bAssistance.assistance_type AS assistance_type, ";
        $sql .= "bAssistance.assistance_date AS assistance_date, ";
        $sql .= "bAssistance.assistance_amount AS assistance_amount, ";
        $sql .= "bAssistance.assistance_from AS assistance_from, ";
        $sql .= "CONCAT(ben.last_name, ', ', ben.first_name, ' ', COALESCE(ben.middle_name, '')) AS full_name, ";
        $sql .= "ben.date_of_birth AS date_of_birth, ";
        $sql .= "CASE ";
        $sql .= "WHEN ben.gender = 1 THEN 'MALE' ";
        $sql .= "WHEN ben.gender = 2 THEN 'FEMALE' ";
        $sql .= "END AS gender_name, ";
        $sql .= "ben.latitude AS latitude, ";
        $sql .= "ben.longitude AS longitude, ";
        $sql .= "slug.code AS slug_code ";
        $sql .= "FROM beneficiary_assistances bAssistance ";
        $sql .= "LEFT JOIN beneficiaries ben ON ben.id = bAssistance.beneficiary_id ";
        $sql .= "LEFT JOIN slugs slug ON slug.slug_id = ben.id AND slug_type = '{$slugType}' ";
        $sql .= "WHERE (bAssistance.company_id = {$company->id} ";
        
        if (isset($request->get('filter')['cityCode'])):
            $sql .= "AND bAssistance.city_id = '{$request->get('filter')['cityCode']}' ";
        endif;

        if (isset($request->get('filter')['assistanceYear'])):
            $sql .= "AND YEAR(bAssistance.assistance_date) = {$request->get('filter')['assistanceYear']} ";
        endif;

        $sql .= "AND bAssistance.deleted_at IS NULL) ";
        $sql .= "AND(ben.latitude IS NOT NULL ";
        $sql .= "AND ben.longitude IS NOT NULL ";
        $sql .= "AND ben.deleted_at IS NULL) ";

        $data = \DB::select($sql);

        return $data;
    }

    public function showAssistancesByBarangayList($request)
    {
        
        $company = Auth::user()->company();

        $provinces = $company->barangay_report_provinces
            ? explode(',', $company->barangay_report_provinces)
            : [];

        return $this->companyRepository->barangaysAssistancesSummaryReport($request, $company, $provinces[0]);
    }

    public function storeBeneficiaryOption($request)
	{
        $company = Auth::user()->company();

        $newBeneficiary = $this->beneficiaryRepository->store($request, $company);

        $this->beneficiaryRepository->updateAddress($newBeneficiary);

        return $newBeneficiary;
    }
}
?>