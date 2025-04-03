<?php
// Changed By Richard
namespace App\Http\Controllers\API\MyCompany;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\BeneficiaryPatient;
use App\Models\BeneficiaryAssistance;
use App\Models\Province;
use App\Models\City;
use App\Models\Barangay;

use App\Http\Requests\MyCompany\Dashboard\UpdateProfileRequest;
use App\Http\Requests\MyCompany\Dashboard\UpdatePhotoRequest;
use App\Http\Requests\MyCompany\Dashboard\UpdatePatientRequest;
use App\Http\Requests\MyCompany\Dashboard\UpdateAssistanceRequest;

use App\Http\Repositories\MyCompany\DashboardRepository as Repository;

class DashboardController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth.permission');

        $this->repository = new Repository;
    }

    public function showProfile(Request $request)
    {
        $request->merge([
            'my-company-related' => true,
        ]);

        $company = Auth::user()->company();

        return $company->toArrayMyCompanyRelated();
    }

    public function updateProfile(Request $request, UpdateProfileRequest $formRequest)
    {
        $request->merge([
            'my-company-related' => true
        ]);

        return $this->repository->updateProfile($formRequest);
    }

    public function updateLogo(Request $request, UpdatePhotoRequest $formRequest)
    {
        $request->merge([
            'my-company-related' => true
        ]);

        return $this->repository->updateLogo($formRequest);
    }

    public function updateSubLogo(Request $request, UpdatePhotoRequest $formRequest)
    {
        $request->merge([
            'my-company-related' => true
        ]);

        return $this->repository->updateSubLogo($formRequest);
    }

    public function summaryTotal(Request $request)
    {
        $request->merge([
            'my-company-related' => true,
        ]);

        $dates = $this->queryDates($request);

        return $this->repository->summaryTotal($request, $dates);
    }

    public function viewSummaryOfBeneficiariesTotal(Request $request)
    {
        $request->merge([
            'my-company-related' => true,
        ]);

        $dates = $this->queryDates($request);

        return $this->repository->viewSummaryOfBeneficiariesTotal($request, $dates);
    }

    public function viewSummaryOfVerifiedVotersTotal(Request $request)
    {
        $request->merge([
            'my-company-related' => true,
        ]);

        $dates = $this->queryDates($request);

        return $this->repository->viewSummaryOfVerifiedVotersTotal($request, $dates);
    }

    public function viewSummaryOfCrossMatchedVotersTotal(Request $request)
    {
        $request->merge([
            'my-company-related' => true,
        ]);

        $dates = $this->queryDates($request);

        return $this->repository->viewSummaryOfCrossMatchedVotersTotal($request, $dates);
    }

    public function viewSummaryOfOfficersTotal(Request $request)
    {
        $request->merge([
            'my-company-related' => true,
        ]);

        $dates = $this->queryDates($request);

        return $this->repository->viewSummaryOfOfficersTotal($request, $dates);
    }

    public function viewSummaryOfVoterTypesTotal(Request $request)
    {
        $request->merge([
            'my-company-related' => true,
        ]);

        $dates = $this->queryDates($request);

        return $this->repository->viewSummaryOfVoterTypesTotal($request, $dates);
    }

    public function viewSummaryOfNetworksTotal(Request $request)
    {
        $request->merge([
            'my-company-related' => true,
        ]);

        $dates = $this->queryDates($request);

        return $this->repository->viewSummaryOfNetworksTotal($request, $dates);
    }

    public function viewSummaryOfOfficerNetworksTotal(Request $request)
    {
        $request->merge([
            'my-company-related' => true,
        ]);

        $dates = $this->queryDates($request);

        return $this->repository->viewSummaryOfOfficerNetworksTotal($request, $dates);
    }


    public function summaryOfBeneficiariesPerWeekTotal(Request $request)
    {

        $request->merge([
            'my-company-related' => true,
        ]);

        $dates = $this->queryDates($request);

        return $this->repository->summaryOfBeneficiariesPerWeekTotal($request, $dates);
    }

    public function summaryOfBeneficiariesPerMonthTotal(Request $request)
    {

        $request->merge([
            'my-company-related' => true,
        ]);

        $dates = $this->queryDates($request);

        return $this->repository->summaryOfBeneficiariesPerMonthTotal($request, $dates);
    }


    public function summaryOfAssistancesPerWeekTotal(Request $request)
    {
        $request->merge([
            'my-company-related' => true,
        ]);

        $dates = $this->queryDates($request);

        return $this->repository->summaryOfAssistancesPerWeekTotal($request, $dates);
    }

    public function summaryOfAssistancesPerMonthTotal(Request $request)
    {



        $request->merge([
            'my-company-related' => true,
        ]);

        $dates = $this->queryDates($request);

        return $this->repository->summaryOfAssistancesPerMonthTotal($request, $dates);
    }

    public function summaryOfPatientsPerWeekTotal(Request $request)
    {
        $request->merge([
            'my-company-related' => true,
        ]);

        $dates = $this->queryDates($request);

        return $this->repository->summaryOfPatientsPerWeekTotal($request, $dates);
    }

    public function summaryOfPatientsPerMonthTotal(Request $request)
    {
        $request->merge([
            'my-company-related' => true,
        ]);

        $dates = $this->queryDates($request);

        return $this->repository->summaryOfPatientsPerMonthTotal($request, $dates);
    }

    public function summaryOfNetworksPerWeekTotal(Request $request)
    {
        $request->merge([
            'my-company-related' => true,
        ]);

        $dates = $this->queryDates($request);

        return $this->repository->summaryOfNetworksPerWeekTotal($request, $dates);
    }

    public function summaryOfNetworksPerMonthTotal(Request $request)
    {
        $request->merge([
            'my-company-related' => true,
        ]);

        $dates = $this->queryDates($request);

        return $this->repository->summaryOfNetworksPerMonthTotal($request, $dates);
    }

    public function summaryOfAssistedOverAssistancesTotal(Request $request)
    {
        $request->merge([
            'my-company-related' => true,
        ]);

        $dates = $this->queryDates($request);

        return $this->repository->summaryOfAssistedOverAssistancesTotal($request, $dates);
    }

    public function summaryOfWeeklyRangeProgress(Request $request)
    {
        $request->merge([
            'my-company-related' => true
        ]);

        return $this->repository->summaryOfWeeklyRangeProgress($request);
    }



    public function summaryOfWeeklyProgress(Request $request)
    {
        $request->merge([
            'my-company-related' => true
        ]);

        return $this->repository->summaryOfWeeklyProgress($request);
    }


    public function summaryOfMonthlyProgress(Request $request)
    {
        $request->merge([
            'my-company-related' => true
        ]);

        return $this->repository->summaryOfMonthlyProgress($request);
    }


    public function summaryOfAssistancesByTypeWeeklyRangeProgress(Request $request)
    {
        $request->merge([
            'my-company-related' => true
        ]);

        return $this->repository->summaryOfAssistancesByTypeWeeklyRangeProgress($request);
    }

    public function summaryOfAssistancesByTypeWeeklyProgress(Request $request)
    {
        $request->merge([
            'my-company-related' => true
        ]);

        return $this->repository->summaryOfAssistancesByTypeWeeklyProgress($request);
    }

    public function summaryOfAssistancesByTypeMonthlyProgress(Request $request)
    {
        $request->merge([
            'my-company-related' => true
        ]);

        return $this->repository->summaryOfAssistancesByTypeMonthlyProgress($request);
    }

    public function summaryOfTopBeneficiaryNetworks(Request $request)
    {
        $request->merge([
            'my-company-related' => true
        ]);

        return $this->repository->summaryOfTopBeneficiaryNetworks($request);
    }

    public function t(Request $request)
    {
        $request->merge([
            'my-company-related' => true
        ]);

        return $this->repository->t($request);
    }

    public function downloadSummaryReport(Request $request)
    {
        $request->merge([
            'my-company-related' => true,
        ]);

        return $this->repository->downloadSummaryReport($request);
    }

    public function downloadOfficersNetworksReport(Request $request)
    {
        $request->merge([
            'my-company-related' => true,
        ]);

        return $this->repository->downloadOfficersNetworksReport($request);
    }

    public function downloadWeeklyNetworksReport(Request $request)
    {
        $request->merge([
            'my-company-related' => true,
        ]);

        return $this->repository->downloadWeeklyNetworksReport($request);
    }

    public function patientsList(Request $request)
    {
        $company = Auth::user()->company();

        $filters = [
            'companyCode' => $company->slug->code
        ];

        $sorts = [
            'patientDate' => 'desc'
        ];

        $request->merge([
            'dashboard-patients-related' => true,
            'filter' => $this->handleQueries('filter', $request, $filters),
            'sort' => $this->handleQueries('sort', $request, $sorts),
        ]);

        $model = new BeneficiaryPatient;

        return $model->build();
    }

    public function updatePatient(Request $request, UpdatePatientRequest $formRequest, $patientId)
    {
        $request->merge([
            'dashboard-patients-related' => true,
        ]);

        return $this->repository->updatePatient($formRequest, $patientId);
    }

    public function approvePatient(Request $request, $patientId)
    {
        $request->merge([
            'dashboard-patients-related' => true,
        ]);

        return $this->repository->updatePatientStatus($request, $patientId, 1);
    }

    public function inProgressPatient(Request $request, $patientId)
    {
        $request->merge([
            'dashboard-patients-related' => true,
        ]);

        return $this->repository->updatePatientStatus($request, $patientId, 2);
    }

    public function completePatient(Request $request, $patientId)
    {
        $request->merge([
            'dashboard-patients-related' => true,
        ]);

        return $this->repository->updatePatientStatus($request, $patientId, 3);
    }

    public function cancelPatient(Request $request, $patientId)
    {
        $request->merge([
            'dashboard-patients-related' => true,
        ]);

        return $this->repository->updatePatientStatus($request, $patientId, 4);
    }

    public function destroyPatient(Request $request, $patientId)
    {
        $request->merge([
            'dashboard-patient-deletion' => true
        ]);

        return $this->repository->destroyPatient($request, $patientId);
    }

    public function assistancesList(Request $request)
    {
        $company = Auth::user()->company();

        $filters = [
            'companyCode' => $company->slug->code
        ];

        $sorts = [
            'isAssisted' => 'asc',
            'assistanceDate' => 'desc',
        ];

        $request->merge([
            'dashboard-assistances-related' => true,
            'filter' => $this->handleQueries('filter', $request, $filters),
            'sort' => $this->handleQueries('sort', $request, $sorts),
        ]);

        $model = new BeneficiaryAssistance;

        return $model->build();
    }

    public function updateAssistance(Request $request, UpdateAssistanceRequest $formRequest, $assistanceId)
    {
        $request->merge([
            'dashboard-assistances-related' => true
        ]);

        return $this->repository->updateAssistance($formRequest, $assistanceId);
    }

    public function destroyAssistance(Request $request, $assistanceId)
    {
        $request->merge([
            'dashboard-assistance-deletion' => true
        ]);

        return $this->repository->destroyAssistance($request, $assistanceId);
    }

    public function provincesOptions(Request $request)
    {
        $sorts = [
            'name' => 'asc'
        ];

        $request->merge([
            'dashboard-options' => true,
            'sort' => $this->handleQueries('sort', $request, $sorts),
            'all' => true
        ]);

        $model = new Province;

        return $model->build();
    }

    public function citiesOptions(Request $request)
    {
        $sorts = [
            'name' => 'asc'
        ];

        $request->merge([
            'dashboard-options' => true,
            'sort' => $this->handleQueries('sort', $request, $sorts),
            'all' => true
        ]);

        $model = new City;

        return $model->build();
    }

    public function barangaysOptions(Request $request)
    {
        $sorts = [
            'name' => 'asc'
        ];

        $request->merge([
            'dashboard-options' => true,
            'sort' => $this->handleQueries('sort', $request, $sorts),
            'all' => true
        ]);

        $model = new Barangay;

        return $model->build();
    }
}
