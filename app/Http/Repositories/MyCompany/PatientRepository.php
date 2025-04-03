<?php

namespace App\Http\Repositories\MyCompany;

use Illuminate\Support\Facades\Auth;

use App\Models\BeneficiaryPatient;

use App\Http\Repositories\Base\BeneficiaryPatientRepository as BaseRepository;
use App\Http\Repositories\Base\CompanyRepository;
use App\Http\Repositories\Base\BeneficiaryRepository;
use App\Http\Repositories\Base\PDFRepository;

class PatientRepository
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
        $company = Auth::user()->company();

        $file = $company->report_file;

        $fileName = $this->pdfRepository->fileName(
            $request->get('from'),
            $request->get('to'),
            strtolower($company->name) . '-patients-report',
            '.xlsx'
        );

        $namespace = '\App\Exports\Patients\\'.$file.'\\PatientReport';

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
        $company = Auth::user()->company();

        $file = $company->report_file;

        $fileName = $this->pdfRepository->fileName(
            $request->get('from'),
            $request->get('to'),
            strtolower($company->name) . '-patient-by-barangay-report',
            '.xlsx'
        );

        $namespace = '\App\Exports\Patients\\'.$file.'\\PatientByBarangayReport';

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
        $company = Auth::user()->company();

        $file = $company->report_file;

        $fileName = $this->pdfRepository->fileName(
            $request->get('from'),
            $request->get('to'),
            strtolower($company->name) . '-patient-by-purok-sitio-report',
            '.xlsx'
        );

        $namespace = '\App\Exports\Patients\\'.$file.'\\PatientByPurokReport';

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

        return $this->baseRepository->store($request, $company);
    }

    public function update($request, $id)
    {
        $company = Auth::user()->company();

        $patient = $this->companyRepository->isPatientRelated($company, $id);

        $patient->update(
            $this->baseRepository->update($request)
        );

        return (BeneficiaryPatient::find($patient->id))->toArrayPatientsRelated();
    }

    public function updateStatus($request, $id, $status)
    {
        $company = Auth::user()->company();

        $patient = $this->companyRepository->isPatientRelated($company, $id);

        $patient->update([
            'status' => $status,
            'updated_by' => Auth::id()
        ]);

        return (BeneficiaryPatient::find($patient->id))->toArrayPatientsRelated();
    }

    public function destroy($request, $id)
    {
        $company = Auth::user()->company();

        $patient = $this->companyRepository->isPatientRelated($company, $id);

        $patient->update([
            'first_name' => 'DELETED',
            'last_name' => 'DELETED',
            'updated_by' => Auth::id()
        ]);

        $patient->delete();

        return response([
            'message' => 'Data deleted successfully.'
        ], 200);
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