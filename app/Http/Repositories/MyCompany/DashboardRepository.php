<?php
// Changed By Richard
namespace App\Http\Repositories\MyCompany;

use Illuminate\Support\Facades\Auth;

use App\Models\Company;
use App\Models\BeneficiaryPatient;
use App\Models\BeneficiaryAssistance;

use App\Http\Repositories\Base\CompanyRepository;
use App\Http\Repositories\Base\PDFRepository;
use App\Http\Repositories\Base\BeneficiaryPatientRepository;
use App\Http\Repositories\Base\BeneficiaryAssistanceRepository;

class DashboardRepository
{
    public function __construct()
    {
        $this->companyRepository = new CompanyRepository;
        $this->pdfRepository = new PDFRepository;
        $this->patientRepository = new BeneficiaryPatientRepository;
        $this->assistanceRepository = new BeneficiaryAssistanceRepository;
    }

    public function updateProfile($request)
    {
        $company = Auth::user()->company();

        $company->update([
            'name' => strtoupper($request->input('name')),
            'address' => $request->input('address'),
            'contact_no' => $request->input('contact_no'),
            'updated_by' => Auth::id()
        ]);

        return (Company::find($company->id))->toArrayMyCompanyRelated();
    }

    public function updateLogo($request)
    {
        $company = Auth::user()->company();

        $filePath = $this->companyRepository->uploadLogo(
            $company->logo,
            $request,
            'logo/company'
        );

        $company->update([
            'logo' => $filePath,
            'updated_by' => Auth::id()
        ]);

        return (Company::find($company->id))->toArrayMyCompanyRelated();
    }

    public function updateSubLogo($request)
    {
        $company = Auth::user()->company();

        $filePath = $this->companyRepository->uploadLogo(
            $company->sub_logo,
            $request,
            'logo/company'
        );

        $company->update([
            'sub_logo' => $filePath,
            'updated_by' => Auth::id()
        ]);

        return (Company::find($company->id))->toArrayMyCompanyRelated();
    }

    public function summaryTotal($request, $dates)
    {
        $company = Auth::user()->company();

        $from = (new \Carbon\Carbon($dates['from']))->format('Y-m-d');
        $to = (new \Carbon\Carbon($dates['to']))->format('Y-m-d');

        $dates = [
            'date' => $request->get('date')
        ];

        $beneficiaries = $this->companyRepository->beneficiariesTotal($dates, $company);
        $patients = $this->companyRepository->patientsTotal($dates, $company);
        $incentives = $this->companyRepository->incentivesTotal($dates, $company);
        $household = $this->companyRepository->householdTotal($dates, $company);
        $documents = $this->companyRepository->documentsTotal($dates, $company);
        $assistances = $this->companyRepository->assistancesTotal($dates, $company);
        $officers = $this->companyRepository->officersTotal($dates, $company);
        $voterTypes = $this->companyRepository->voterTypesTotal($dates, $company);
        $networks = $this->companyRepository->networksTotal($dates, $company);
        $messages = $this->companyRepository->messagesTotal($dates, $company);
        $calls = $this->companyRepository->callsTotal($dates, $company);
        $verifiedVotersTotal = $this->companyRepository->verifiedVotersTotal($dates, $company);
        $crossMatchedVotersTotal = $this->companyRepository->crossMatchedVotersTotal($dates, $company);

        return [
            'beneficiaries' => $beneficiaries,
            'patients' => $patients,
            'incentives' => $incentives,
            'household' => $household,
            'documents' => $documents,
            'assistances' => $assistances,
            'officers' => $officers,
            'verifiedVoters' => $verifiedVotersTotal,
            'crossMatchedVoters' => $crossMatchedVotersTotal,
            'voterTypes' => $voterTypes,
            'networks' => $networks,
            'messages' => $messages,
            'calls' => $calls,
        ];
    }

    public function viewSummaryOfVerifiedVotersTotal($request, $dates)
    {
        $company = Auth::user()->company();

        $from = (new \Carbon\Carbon($dates['from']))->format('Y-m-d');
        $to = (new \Carbon\Carbon($dates['to']))->format('Y-m-d');

        $dates = [
            'date' => $request->get('date')
        ];

        return $this->companyRepository->verifiedVotersTotal($dates, $company);
    }

    public function viewSummaryOfCrossMatchedVotersTotal($request, $dates)
    {
        $company = Auth::user()->company();

        $from = (new \Carbon\Carbon($dates['from']))->format('Y-m-d');
        $to = (new \Carbon\Carbon($dates['to']))->format('Y-m-d');

        $dates = [
            'date' => $request->get('date')
        ];

        return $this->companyRepository->crossMatchedVotersTotal($dates, $company);
    }
    
    public function viewSummaryOfBeneficiariesTotal($request, $dates)
    {
        $company = Auth::user()->company();

        $from = (new \Carbon\Carbon($dates['from']))->format('Y-m-d');
        $to = (new \Carbon\Carbon($dates['to']))->format('Y-m-d');

        $dates = [
            'date' => $request->get('date')
        ];

        return $this->companyRepository->beneficiariesTotal($dates, $company);
    }

    public function viewSummaryOfOfficersTotal($request, $dates)
    {
        $company = Auth::user()->company();

        $from = (new \Carbon\Carbon($dates['from']))->format('Y-m-d');
        $to = (new \Carbon\Carbon($dates['to']))->format('Y-m-d');

        $dates = [
            'date' => $request->get('date')
        ];

        return $this->companyRepository->officersTotal($dates, $company);
    }

    public function viewSummaryOfVoterTypesTotal($request, $dates)
    {
        $company = Auth::user()->company();

        $from = (new \Carbon\Carbon($dates['from']))->format('Y-m-d');
        $to = (new \Carbon\Carbon($dates['to']))->format('Y-m-d');

        $dates = [
            'date' => $request->get('date')
        ];

        return $this->companyRepository->voterTypesTotal($dates, $company);
    }

    public function viewSummaryOfNetworksTotal($request, $dates)
    {
        $company = Auth::user()->company();

        $from = (new \Carbon\Carbon($dates['from']))->format('Y-m-d');
        $to = (new \Carbon\Carbon($dates['to']))->format('Y-m-d');

        $dates = [
            'date' => $request->get('date')
        ];

        $networks = $this->companyRepository->networksTotal($dates, $company);
        $incentives = $this->companyRepository->incentivesTotal($dates, $company);

        return [
            'networks' => $networks,
            'incentives' => $incentives
        ];
    }

    public function viewSummaryOfPatientsTotal($request, $dates)
    {
        $company = Auth::user()->company();

        $from = (new \Carbon\Carbon($dates['from']))->format('Y-m-d');
        $to = (new \Carbon\Carbon($dates['to']))->format('Y-m-d');

        $dates = [
            'date' => $request->get('date')
        ];

        return $this->companyRepository->patientsTotal($dates, $company);
    }

    public function viewSummaryOfHouseholdTotal($request, $dates)
    {
        $company = Auth::user()->company();

        $from = (new \Carbon\Carbon($dates['from']))->format('Y-m-d');
        $to = (new \Carbon\Carbon($dates['to']))->format('Y-m-d');

        $dates = [
            'date' => $request->get('date')
        ];

        $household = $this->companyRepository->householdTotal($dates, $company);

        $householdByBarangay = $this->companyRepository->householdByBarangayTotal($company);

        $householdByPurok = $this->companyRepository->householdByPurokTotal($company);

        return [
            'household' => $household,
            'householdByBarangay' => $householdByBarangay,
            'householdByPurok' => $householdByPurok,
        ];
    }

    public function viewSummaryOfOfficerNetworksList($request, $dates)
    {
        $company = Auth::user()->company();

        $from = (new \Carbon\Carbon($dates['from']))->format('Y-m-d');
        $to = (new \Carbon\Carbon($dates['to']))->format('Y-m-d');

        $officers = $this->companyRepository->officersNetworksList($request, $company);

        return $officers;
    }
    public function summaryOfBeneficiariesPerWeekTotal($request, $dates)
    {
        $company = Auth::user()->company();
        return $this->companyRepository->beneficiariesPerWeekTotal($dates, $company);
    }
    public function summaryOfBeneficiariesPerMonthTotal($request, $dates)
    {
        $company = Auth::user()->company();
        return $this->companyRepository->beneficiariesPerMonthTotal($dates, $company);
    }

    public function summaryOfAssistancesPerWeekTotal($request, $dates)
    {
        $company = Auth::user()->company();
        return $this->companyRepository->assistancesPerWeekTotal($dates, $company);
    }

    public function summaryOfAssistancesPerMonthTotal($request, $dates)
    {
        $company = Auth::user()->company();
        return $this->companyRepository->assistancesPerMonthTotal($dates, $company);
    }
    public function summaryOfPatientsPerWeekTotal($request, $dates)
    {
        $company = Auth::user()->company();
        return $this->companyRepository->patientsPerWeekTotal($dates, $company);
    }
    public function summaryOfPatientsPerMonthTotal($request, $dates)
    {
        $company = Auth::user()->company();
        return $this->companyRepository->patientsPerMonthTotal($dates, $company);
    }

    public function summaryOfNetworksPerWeekTotal($request, $dates)
    {
        $company = Auth::user()->company();
        return $this->companyRepository->networksPerWeekTotal($dates, $company);
    }

    public function summaryOfNetworksPerMonthTotal($request, $dates)
    {
        $company = Auth::user()->company();
        return $this->companyRepository->networksPerMonthTotal($dates, $company);
    }

    public function summaryOfAssistedOverAssistancesTotal($request, $dates)
    {
        $company = Auth::user()->company();

        $from = (new \Carbon\Carbon($dates['from']))->format('Y-m-d');
        $to = (new \Carbon\Carbon($dates['to']))->format('Y-m-d');


        return $this->companyRepository->assistedOverAssistancesTotal($dates, $company);
    }

    public function downloadSummaryReport($request)
    {
        $company = Auth::user()->company();

        $file = $company->report_file;

        $fileName = $this->pdfRepository->fileName(
            $request->get('date'),
            $request->get('date'),
            strtolower($company->name) . '-summary-report',
            '.xlsx'
        );

        $namespace = '\App\Exports\SummaryReport\\' . $file . '\\SummaryReport';

        \Excel::store(
            new $namespace($request, $company),
            'storage/exports/' . $fileName,
            env('STORAGE_DISK', 'public')
        );

        return [
            'path' => env('CDN_URL') . '/storage/exports/' . $fileName
        ];
    }

    public function downloadOfficersNetworksReport($request)
    {
        $company = Auth::user()->company();

        $file = $company->report_file;

        $fileName = $this->pdfRepository->fileName(
            $request->get('date'),
            $request->get('date'),
            strtolower($company->name) . '-officers-networks-summary-report',
            '.xlsx'
        );

        $namespace = '\App\Exports\Beneficiaries\base\OfficerNetworkReport';

        \Excel::store(
            new $namespace($request, $company),
            'storage/exports/' . $fileName,
            env('STORAGE_DISK', 'public')
        );

        return [
            'path' => env('CDN_URL') . '/storage/exports/' . $fileName
        ];
    }

    public function downloadWeeklyNetworksReport($request)
    {
        $company = Auth::user()->company();

        $file = $company->report_file;

        $fileName = $this->pdfRepository->fileName(
            $request->get('from'),
            $request->get('to'),
            strtolower($company->name) . '-weekly-networks-summary-report',
            '.xlsx'
        );

        $namespace = '\App\Exports\Beneficiaries\base\WeeklyNetworkReport';

        \Excel::store(
            new $namespace($request, $company),
            'storage/exports/' . $fileName,
            env('STORAGE_DISK', 'public')
        );

        return [
            'path' => env('CDN_URL') . '/storage/exports/' . $fileName
        ];
    }
    public function updatePatient($request, $id)
    {
        $company = Auth::user()->company();

        $patient = $this->companyRepository->isPatientRelated($company, $id);

        $patient->update(
            $this->patientRepository->update($request)
        );

        return (BeneficiaryPatient::find($patient->id))->toArrayDashboardPatientsRelated();
    }

    public function updatePatientStatus($request, $id, $status)
    {
        $company = Auth::user()->company();

        $patient = $this->companyRepository->isPatientRelated($company, $id);

        $patient->update([
            'status' => $status,
            'updated_by' => Auth::id()
        ]);

        return (BeneficiaryPatient::find($patient->id))->toArrayDashboardPatientsRelated();
    }

    public function destroyPatient($request, $id)
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

    public function updateAssistance($request, $id)
    {
        $company = Auth::user()->company();

        $assistance = $this->companyRepository->isAssistanceRelated($company, $id);

        $assistance->update(
            $this->assistanceRepository->update($request)
        );

        return (BeneficiaryAssistance::find($assistance->id))->toArrayDashboardAssistancesRelated();
    }

    public function destroyAssistance($request, $id)
    {
        $company = Auth::user()->company();

        $assistance = $this->companyRepository->isAssistanceRelated($company, $id);

        $assistance->delete();

        return response([
            'message' => 'Data deleted successfully.'
        ], 200);
    }


    public function summaryOfWeeklyRangeProgress($request)
    {
        $company = Auth::user()->company();

        $dates = [
            'from' => (new \Carbon\Carbon($request->get('from')))->format('Y-m-d'),
            'to' => (new \Carbon\Carbon($request->get('to')))->format('Y-m-d'),
        ];

        $beneficiaries = $this->companyRepository->beneficiariesGroupByDay($request, $dates, $company);
        $assistances = $this->companyRepository->assistancesGroupByDay($request, $dates, $company);
        $patients = $this->companyRepository->patientsGroupByDay($request, $dates, $company);


        return [
            'from' => $dates['from'],
            'to' => $dates['to'],
            'beneficiaries' => $beneficiaries,
            'assistances' => $assistances,
            'patients' => $patients,
        ];
    }

    public function summaryOfWeeklyProgress($request)
    {
        $company = Auth::user()->company();

        $dates = [
            'from' => (new \Carbon\Carbon($request->get('from')))->format('Y-m-d'),
            'to' => (new \Carbon\Carbon($request->get('to')))->format('Y-m-d'),
        ];

        $beneficiaries = $this->companyRepository->beneficiariesGroupByWeek($request, $dates, $company);
        $assistances = $this->companyRepository->assistancesGroupByWeek($request, $dates, $company);
        $patients = $this->companyRepository->patientsGroupByWeek($request, $dates, $company);


        return [
            'from' => $dates['from'],
            'to' => $dates['to'],
            'beneficiaries' => $beneficiaries,
            'assistances' => $assistances,
            'patients' => $patients,
        ];
    }

    public function summaryOfMonthlyProgress($request)
    {
        $company = Auth::user()->company();

        $dates = [
            'from' => (new \Carbon\Carbon($request->get('from')))->format('Y-m-d'),
            'to' => (new \Carbon\Carbon($request->get('to')))->format('Y-m-d'),
        ];

        $beneficiaries = $this->companyRepository->beneficiariesGroupByMonth($request, $dates, $company);
        $assistances = $this->companyRepository->assistancesGroupByMonth($request, $dates, $company);
        $patients = $this->companyRepository->patientsGroupByMonth($request, $dates, $company);


        return [
            'from' => $dates['from'],
            'to' => $dates['to'],
            'beneficiaries' => $beneficiaries,
            'assistances' => $assistances,
            'patients' => $patients,
        ];
    }


    public function summaryOfAssistancesByTypeWeeklyRangeProgress($request)
    {
        $company = Auth::user()->company();

        $dates = [
            'from' => (new \Carbon\Carbon($request->get('from')))->format('Y-m-d'),
            'to' => (new \Carbon\Carbon($request->get('to')))->format('Y-m-d'),
        ];

        $listOfDates = listDatesFromDateRange($dates['from'], $dates['to']);

        $assistances = $this->companyRepository->assistancesByTypeGroupByDay($request, $dates, $company);


        return [
            'from' => $dates['from'],
            'to' => $dates['to'],
            'assistances' => $assistances,
        ];
    }



    public function summaryOfAssistancesByTypeWeeklyProgress($request)
    {
        $company = Auth::user()->company();

        $dates = [
            'from' => (new \Carbon\Carbon($request->get('from')))->format('Y-m-d'),
            'to' => (new \Carbon\Carbon($request->get('to')))->format('Y-m-d'),
        ];

        $listOfDates = listDatesFromDateRange($dates['from'], $dates['to']);

        $assistances = $this->companyRepository->assistancesByTypeGroupByWeek($request, $dates, $company);


        return [
            'from' => $dates['from'],
            'to' => $dates['to'],
            'assistances' => $assistances,
        ];
    }

    public function summaryOfAssistancesByTypeMonthlyProgress($request)
    {
        $company = Auth::user()->company();

        $dates = [
            'from' => (new \Carbon\Carbon($request->get('from')))->format('Y-m-d'),
            'to' => (new \Carbon\Carbon($request->get('to')))->format('Y-m-d'),
        ];

        $listOfDates = listDatesFromDateRange($dates['from'], $dates['to']);

        $assistances = $this->companyRepository->assistancesByTypeGroupByMonth($request, $dates, $company);


        return [
            'from' => $dates['from'],
            'to' => $dates['to'],
            'assistances' => $assistances,
        ];
    }

    public function summaryOfTopBeneficiaryNetworks($request)
    {
        $company = Auth::user()->company();

        $beneficiaries = $this->companyRepository->topBeneficiaryNetworks($request, $company);

        return $beneficiaries;
    }
}
