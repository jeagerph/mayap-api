<?php

namespace App\Http\Controllers\API\MyCompany;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Slug;
use App\Models\Beneficiary;
use App\Models\Company;
use App\Models\Province;
use App\Models\City;
use App\Models\Barangay;
use App\Models\CompanyClassification;
use App\Models\CompanyOfficerClassification;
use App\Models\CompanyQuestionnaire;
use App\Models\CompanyIdTemplate;
use App\Models\CompanyDocumentTemplate;
use App\Models\BeneficiaryRelative;
use App\Models\BeneficiaryIncentive;
use App\Models\BeneficiaryAssistance;
use App\Models\BeneficiaryPatient;
use App\Models\BeneficiaryMessage;
use App\Models\BeneficiaryCall;
use App\Models\BeneficiaryIdentification;
use App\Models\BeneficiaryDocument;
use App\Models\BeneficiaryFamily;
use App\Models\Activity;

use App\Http\Repositories\MyCompany\BeneficiaryRepository as Repository;

use App\Http\Requests\MyCompany\Beneficiary\CheckRequest;
use App\Http\Requests\MyCompany\Beneficiary\StoreRequest;
use App\Http\Requests\MyCompany\Beneficiary\UpdateRequest;
use App\Http\Requests\MyCompany\Beneficiary\UpdatePhotoRequest;
use App\Http\Requests\MyCompany\Beneficiary\ArrangeRelativesRequest;
use App\Http\Requests\MyCompany\Beneficiary\StoreRelativeRequest;
use App\Http\Requests\MyCompany\Beneficiary\StoreNetworkRequest;
use App\Http\Requests\MyCompany\Beneficiary\StoreIncentiveRequest;
use App\Http\Requests\MyCompany\Beneficiary\StoreAssistanceRequest;
use App\Http\Requests\MyCompany\Beneficiary\UpdateAssistanceRequest;
use App\Http\Requests\MyCompany\Beneficiary\StorePatientRequest;
use App\Http\Requests\MyCompany\Beneficiary\UpdatePatientRequest;
use App\Http\Requests\MyCompany\Beneficiary\StoreMessageRequest;
use App\Http\Requests\MyCompany\Beneficiary\StoreCallRequest;
use App\Http\Requests\MyCompany\Beneficiary\StoreIdentificationRequest;
use App\Http\Requests\MyCompany\Beneficiary\StoreDocumentRequest;
use App\Http\Requests\MyCompany\Beneficiary\ArrangeFamiliesRequest;
use App\Http\Requests\MyCompany\Beneficiary\StoreFamilyRequest;
use App\Http\Requests\MyCompany\Beneficiary\UpdateFamilyRequest;
use App\Http\Requests\MyCompany\Beneficiary\StoreBeneficiaryOptionRequest;
use Illuminate\Support\Facades\DB;

class BeneficiaryController extends Controller
{
    private $repository;
    public function __construct()
    {
        // $this->middleware('auth.permission');

        $this->repository = new Repository;
    }

    public function index(Request $request)
    {
        $company = Auth::user()->company();

        $request->merge([
            'beneficiaries-related' => true
        ]);

        $model = new Beneficiary;

        return $model->where('company_id', $company->id)
            ->where(function ($q) use ($request) {
                if ($request->has('firstName') && $request->get('firstName')):
                    $q->where('first_name', 'LIKE', '%' . $request->get('firstName') . '%');
                endif;

                if ($request->has('middleName') && $request->get('middleName')):
                    $q->where('middle_name', 'LIKE', '%' . $request->get('middleName') . '%');
                endif;

                if ($request->has('lastName') && $request->get('lastName')):
                    $q->where('last_name', 'LIKE', '%' . $request->get('lastName') . '%');
                endif;

                // RELATIVE SEARCH
    
                if ($request->has('relativeName') && $request->get('relativeName')):
                    $q->whereHas('families', function ($q) use ($request) {
                        $q->where('full_name', 'LIKE', '%' . $request->get('relativeName') . '%');
                    });
                endif;

                if ($request->has('filter')):

                    if (isset($request->get('filter')['isHousehold'])):
                        $q->where('is_household', $request->get('filter')['isHousehold']);
                    endif;

                    if (isset($request->get('filter')['isPriority'])):
                        $q->where('is_priority', $request->get('filter')['isPriority']);
                    endif;

                    if (isset($request->get('filter')['isOfficer'])):
                        $q->where('is_officer', $request->get('filter')['isOfficer']);
                    endif;

                    if (isset($request->get('filter')['voterType'])):
                        $q->where('voter_type', $request->get('filter')['voterType']);
                    endif;

                    if (isset($request->get('filter')['gender'])):
                        $q->where('gender', $request->get('filter')['gender']);
                    endif;

                    if (isset($request->get('filter')['provCode'])):
                        $q->where('province_id', $request->get('filter')['provCode']);
                    endif;

                    if (isset($request->get('filter')['cityCode'])):
                        $q->where('city_id', $request->get('filter')['cityCode']);
                    endif;

                    if (isset($request->get('filter')['barangay'])):
                        $q->where('barangay_id', $request->get('filter')['barangay']);
                    endif;

                    if (isset($request->get('filter')['isGreen']) && isset($request->get('filter')['isOrange'])) {
                        $q->where(function ($query) {
                            $query->where('verify_voter', 2)
                                ->orWhere('verify_voter', 1);
                        });
                    } elseif (isset($request->get('filter')['isGreen'])) {
                        $q->where('verify_voter', 2);
                    } elseif (isset($request->get('filter')['isOrange'])) {
                        $q->where('verify_voter', 1);
                    }

                    if (isset($request->get('filter')['age'])):
                        $arrAgeRange = explode(',', $request->get('filter')['age']);

                        $minDate = \Carbon\Carbon::today()->subYears($arrAgeRange[0])->format('Y');
                        $maxDate = \Carbon\Carbon::today()->subYears($arrAgeRange[1])->format('Y');

                        $q->whereBetween(DB::raw('YEAR(date_of_birth)'), [$maxDate, $minDate]);

                    endif;

                    if (isset($request->get('filter')['hasNetwork'])):

                        $q->has('parentingNetworks', '>=', 1);

                    endif;
                endif;
            })
            ->where(function ($q) use ($request) {
                if ($request->has('range') && $request->get('range')['dateRegistered']):
                    $dates = explode(',', $request->get('range')['dateRegistered']);

                    $q->whereDate('date_registered', $dates[0])
                        ->orWhereDate('date_registered', $dates[1])
                        ->orWhereBetween('date_registered', [$dates[0], $dates[1]]);
                endif;
            })
            // ->orderBy('is_priority', 'desc')
            // ->orderBy('last_name', 'asc')
            // ->orderBy('first_name', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function downloadReport(Request $request)
    {
        $request->merge([
            'beneficiaries-related' => true,
        ]);

        return $this->repository->downloadReport($request);
    }

    public function downloadPrintReport(Request $request)
    {
        $request->merge([
            'beneficiaries-related' => true,
        ]);

        return $this->repository->downloadPrintReport($request);
    }

    public function downloadByBarangayReport(Request $request)
    {
        $request->merge([
            'beneficiaries-related' => true,
        ]);

        return $this->repository->downloadByBarangayReport($request);
    }

    public function downloadByPurokReport(Request $request)
    {
        $request->merge([
            'beneficiaries-related' => true,
        ]);

        return $this->repository->downloadByPurokReport($request);
    }

    public function downloadHouseholdByBarangayReport(Request $request)
    {
        $request->merge([
            'beneficiaries-related' => true,
        ]);

        return $this->repository->downloadHouseholdByBarangayReport($request);
    }

    public function downloadHouseholdByPurokReport(Request $request)
    {
        $request->merge([
            'beneficiaries-related' => true,
        ]);

        return $this->repository->downloadHouseholdByPurokReport($request);
    }

    public function showBeneficiariesLocationsList(Request $request)
    {
        $request->merge([
            'beneficiaries-related' => true
        ]);

        return $this->repository->showBeneficiariesLocationsList($request);
    }

    public function showBeneficiariesSummaryByBarangay(Request $request)
    {
        $request->merge([
            'beneficiaries-related' => true
        ]);

        return $this->repository->showBeneficiariesSummaryByBarangay($request);
    }

    public function showBeneficiariesVoterTypeList(Request $request)
    {
        $request->merge([
            'beneficiaries-related' => true
        ]);

        return $this->repository->showBeneficiariesVoterTypeList($request);
    }

    public function check(Request $request, CheckRequest $formRequest)
    {
        $request->merge([
            'beneficiary-checking-related' => true
        ]);

        return $this->repository->check($formRequest);
    }

    public function store(Request $request, StoreRequest $formRequest)
    {
        $request->merge([
            'beneficiaries-related' => true
        ]);

        return $this->repository->store($formRequest);
    }

    public function import(Request $request)
    {
        $request->merge([
            'beneficiaries-related' => true
        ]);

        return $this->repository->import($request);
    }

    public function show(Request $request, $code)
    {
        $request->merge([
            'beneficiaries-related' => true
        ]);

        return $this->repository->show($request, $code);
    }

    public function edit(Request $request, $code)
    {
        $request->merge([
            'beneficiaries-related' => true
        ]);

        return $this->repository->edit($request, $code);
    }

    public function update(Request $request, UpdateRequest $formRequest, $code)
    {
        $request->merge([
            'beneficiaries-related' => true
        ]);

        return $this->repository->update($request, $code);
    }

    public function updatePhoto(Request $request, UpdatePhotoRequest $formRequest, $code)
    {
        $request->merge([
            'beneficiaries-related' => true
        ]);

        return $this->repository->updatePhoto($request, $code);
    }

    public function updateOfficer(Request $request, $code)
    {
        $request->merge([
            'beneficiaries-related' => true
        ]);

        return $this->repository->updateOfficer($request, $code);
    }

    public function updateIsVoter(Request $request, $code)
    {
        $request->merge([
            'beneficiaries-related' => true
        ]);

        return $this->repository->updateOfficer($request, $code);
    }

    public function checkVoters(Request $request, $code)
    {
        $request->merge([
            'beneficiary-checking-voters-related' => true
        ]);

        return $this->repository->checkVoters($request, $code);
    }

    public function updateVoter(Request $request, $code)
    {
        $request->merge([
            'beneficiaries-related' => true
        ]);

        return $this->repository->updateVoter($request, $code);
    }

    public function showProfile(Request $request, $code)
    {
        $request->merge([
            'beneficiary-profile-related' => true
        ]);

        return $this->repository->showProfile($request, $code);
    }

    public function showMobileNo(Request $request, $code)
    {
        $request->merge([
            'beneficiary-sms-related' => true
        ]);

        return $this->repository->showMobileNo($request, $code);
    }

    public function destroy(Request $request, $code)
    {
        $request->merge([
            'beneficiary-deletion' => true
        ]);

        return $this->repository->destroy($request, $code);
    }

    public function showActivities(Request $request, $code)
    {
        $module = Slug::findCodeOrDie($code);

        $filters = [
            'activityType' => 'App\\Models\\Beneficiary',
            'activityId' => $module->slug_id
        ];

        $sorts = [
            'created' => 'desc'
        ];

        $request->merge([
            'beneficiaries-related' => true,
            'filter' => $this->handleQueries('filter', $request, $filters),
            'sort' => $this->handleQueries('sort', $request, $sorts),
        ]);

        $model = new Activity;

        return $model->build();
    }

    public function showRelatives(Request $request, $code)
    {
        $filters = [
            'beneficiaryCode' => $code
        ];

        $sorts = [
            'orderNo' => 'asc'
        ];

        $request->merge([
            'beneficiary-relatives-related' => true,
            'filter' => $this->handleQueries('filter', $request, $filters),
            'sort' => $this->handleQueries('sort', $request, $sorts),
            'all' => true
        ]);

        $model = new BeneficiaryRelative;

        return $model->build();
    }

    public function arrangeRelatives(Request $request, ArrangeRelativesRequest $formRequest, $code)
    {
        $request->merge([
            'beneficiary-relatives-related' => true
        ]);

        return $this->repository->arrangeRelatives($formRequest, $code);
    }

    public function storeRelative(Request $request, StoreRelativeRequest $formRequest, $code)
    {
        $request->merge([
            'beneficiary-relatives-related' => true
        ]);

        return $this->repository->storeRelative($formRequest, $code);
    }

    public function destroyRelative(Request $request, $code, $id)
    {
        $request->merge([
            'beneficiary-relative-deletion' => true
        ]);

        return $this->repository->destroyRelative($request, $code, $id);
    }

    public function showFamilies(Request $request, $code)
    {
        $filters = [
            'beneficiaryCode' => $code
        ];

        $sorts = [
            'orderNo' => 'asc'
        ];

        $request->merge([
            'beneficiary-families-related' => true,
            'filter' => $this->handleQueries('filter', $request, $filters),
            'sort' => $this->handleQueries('sort', $request, $sorts),
            'all' => true
        ]);

        $model = new BeneficiaryFamily;

        return $model->build();
    }

    public function arrangeFamilies(Request $request, ArrangeFamiliesRequest $formRequest, $code)
    {
        $request->merge([
            'beneficiary-families-related' => true
        ]);

        return $this->repository->arrangeFamilies($formRequest, $code);
    }

    public function storeFamily(Request $request, StoreFamilyRequest $formRequest, $code)
    {
        $request->merge([
            'beneficiary-families-related' => true
        ]);

        return $this->repository->storeFamily($formRequest, $code);
    }

    public function updateFamily(Request $request, UpdateFamilyRequest $formRequest, $code, $id)
    {
        $request->merge([
            'beneficiary-families-related' => true
        ]);

        return $this->repository->updateFamily($formRequest, $code, $id);
    }

    public function destroyFamily(Request $request, $code, $id)
    {
        $request->merge([
            'beneficiary-family-deletion' => true
        ]);

        return $this->repository->destroyFamily($request, $code, $id);
    }

    public function showNetworkByList(Request $request, $code)
    {
        $request->merge([
            'beneficiary-networks-related' => true
        ]);

        return $this->repository->showNetworkByList($request, $code);
    }

    public function downloadNetworkByList(Request $request, $code)
    {
        $request->merge([
            'beneficiary-networks-related' => true
        ]);

        return $this->repository->downloadNetworkByList($request, $code);
    }

    public function showNetworkByChart(Request $request, $code)
    {
        $request->merge([
            'beneficiary-networks-related' => true
        ]);

        return $this->repository->showNetworkByChart($request, $code);
    }

    public function storeNetwork(Request $request, StoreNetworkRequest $formRequest, $code)
    {
        $request->merge([
            'beneficiary-networks-related' => true
        ]);

        return $this->repository->storeNetwork($formRequest, $code);
    }

    public function destroyNetwork(Request $request, $code, $networkId)
    {
        $request->merge([
            'beneficiary-network-deletion' => true
        ]);

        return $this->repository->destroyNetwork($request, $code, $networkId);
    }

    public function showIncentives(Request $request, $code)
    {
        $filters = [
            'beneficiaryCode' => $code
        ];

        $sorts = [
            'incentiveDate' => 'desc'
        ];

        $request->merge([
            'beneficiary-incentives-related' => true,
            'filter' => $this->handleQueries('filter', $request, $filters),
            'sort' => $this->handleQueries('sort', $request, $sorts),
        ]);

        $model = new BeneficiaryIncentive;

        return $model->build();
    }

    public function storeIncentive(Request $request, StoreIncentiveRequest $formRequest, $code)
    {
        $request->merge([
            'beneficiary-incentives-related' => true
        ]);

        return $this->repository->storeIncentive($formRequest, $code);
    }

    public function destroyIncentive(Request $request, $code, $id)
    {
        $request->merge([
            'beneficiary-incentive-deletion' => true
        ]);

        return $this->repository->destroyIncentive($request, $code, $id);
    }

    public function showAssistances(Request $request, $code)
    {
        $filters = [
            'beneficiaryCode' => $code
        ];

        $sorts = [
            'assistanceDate' => 'desc'
        ];

        $request->merge([
            'beneficiary-assistances-related' => true,
            'filter' => $this->handleQueries('filter', $request, $filters),
            'sort' => $this->handleQueries('sort', $request, $sorts),
        ]);

        $model = new BeneficiaryAssistance;

        return $model->build();
    }

    public function storeAssistance(Request $request, StoreAssistanceRequest $formRequest, $code)
    {
        $request->merge([
            'beneficiary-assistances-related' => true
        ]);

        return $this->repository->storeAssistance($formRequest, $code);
    }

    public function updateAssistance(Request $request, UpdateAssistanceRequest $formRequest, $code, $assistanceId)
    {
        $request->merge([
            'beneficiary-assistances-related' => true
        ]);

        return $this->repository->updateAssistance($formRequest, $code, $assistanceId);
    }

    public function destroyAssistance(Request $request, $code, $assistanceId)
    {
        $request->merge([
            'beneficiary-assistance-deletion' => true
        ]);

        return $this->repository->destroyAssistance($request, $code, $assistanceId);
    }

    public function showPatients(Request $request, $code)
    {
        $filters = [
            'beneficiaryCode' => $code
        ];

        $sorts = [
            'patientDate' => 'desc'
        ];

        $request->merge([
            'beneficiary-patients-related' => true,
            'filter' => $this->handleQueries('filter', $request, $filters),
            'sort' => $this->handleQueries('sort', $request, $sorts),
        ]);

        $model = new BeneficiaryPatient;

        return $model->build();
    }

    public function storePatient(Request $request, StorePatientRequest $formRequest, $code)
    {
        $request->merge([
            'beneficiary-patients-related' => true
        ]);

        return $this->repository->storePatient($formRequest, $code);
    }

    public function updatePatient(Request $request, UpdatePatientRequest $formRequest, $code, $patientId)
    {
        $request->merge([
            'beneficiary-patients-related' => true
        ]);

        return $this->repository->updatePatient($formRequest, $code, $patientId);
    }

    public function approvePatient(Request $request, $code, $patientId)
    {
        $request->merge([
            'beneficiary-patients-related' => true
        ]);

        return $this->repository->updatePatientStatus($request, $code, $patientId, 1);
    }

    public function inProgressPatient(Request $request, $code, $patientId)
    {
        $request->merge([
            'beneficiary-patients-related' => true
        ]);

        return $this->repository->updatePatientStatus($request, $code, $patientId, 2);
    }

    public function completePatient(Request $request, $code, $patientId)
    {
        $request->merge([
            'beneficiary-patients-related' => true
        ]);

        return $this->repository->updatePatientStatus($request, $code, $patientId, 3);
    }

    public function cancelPatient(Request $request, $code, $patientId)
    {
        $request->merge([
            'beneficiary-patients-related' => true
        ]);

        return $this->repository->updatePatientStatus($request, $code, $patientId, 4);
    }

    public function destroyPatient(Request $request, $code, $patientId)
    {
        $request->merge([
            'beneficiary-patient-deletion' => true
        ]);

        return $this->repository->destroyPatient($request, $code, $patientId);
    }

    public function showMessages(Request $request, $code)
    {
        $filters = [
            'beneficiaryCode' => $code
        ];

        $sorts = [
            'messageDate' => 'desc',
            'created' => 'desc',
        ];

        $request->merge([
            'beneficiary-messages-related' => true,
            'filter' => $this->handleQueries('filter', $request, $filters),
            'sort' => $this->handleQueries('sort', $request, $sorts),
        ]);

        $model = new BeneficiaryMessage;

        return $model->build();
    }

    public function storeMessage(Request $request, StoreMessageRequest $formRequest, $code)
    {
        $request->merge([
            'beneficiary-messages-related' => true
        ]);

        return $this->repository->storeMessage($formRequest, $code);
    }

    public function showCalls(Request $request, $code)
    {
        $filters = [
            'beneficiaryCode' => $code
        ];

        $sorts = [
            'callDate' => 'desc',
            'created' => 'desc',
        ];

        $request->merge([
            'beneficiary-calls-related' => true,
            'filter' => $this->handleQueries('filter', $request, $filters),
            'sort' => $this->handleQueries('sort', $request, $sorts),
        ]);

        $model = new BeneficiaryCall;

        return $model->build();
    }

    public function storeCall(Request $request, StoreCallRequest $formRequest, $code)
    {
        $request->merge([
            'beneficiary-calls-related' => true
        ]);

        return $this->repository->storeCall($formRequest, $code);
    }

    public function updateCall(Request $request, $code, $callId)
    {
        $request->merge([
            'beneficiary-calls-related' => true
        ]);

        return $this->repository->updateCall($request, $code, $callId);
    }

    public function showIdentifications(Request $request, $code)
    {
        $filters = [
            'beneficiaryCode' => $code
        ];

        $sorts = [
            'identificationDate' => 'desc'
        ];

        $request->merge([
            'beneficiary-identifications-related' => true,
            'filter' => $this->handleQueries('filter', $request, $filters),
            'sort' => $this->handleQueries('sort', $request, $sorts),
        ]);

        $model = new BeneficiaryIdentification;

        return $model->build();
    }

    public function storeIdentification(Request $request, StoreIdentificationRequest $formRequest, $code)
    {
        $request->merge([
            'beneficiary-identifications-related' => true,
        ]);

        return $this->repository->storeIdentification($formRequest, $code);
    }

    public function downloadIdentification(Request $request, $code, $identificationId)
    {
        $request->merge([
            'beneficiary-identifications-related' => true,
        ]);

        return $this->repository->downloadIdentification($request, $code, $identificationId);
    }

    public function destroyIdentification(Request $request, $code, $identificationId)
    {
        $request->merge([
            'beneficiary-identification-deletion' => true
        ]);

        return $this->repository->destroyIdentification($request, $code, $identificationId);
    }

    public function showDocuments(Request $request, $code)
    {
        $filters = [
            'beneficiaryCode' => $code
        ];

        $sorts = [
            'documentDate' => 'desc'
        ];

        $request->merge([
            'beneficiary-documents-related' => true,
            'filter' => $this->handleQueries('filter', $request, $filters),
            'sort' => $this->handleQueries('sort', $request, $sorts),
        ]);

        $model = new BeneficiaryDocument;

        return $model->build();
    }

    public function storeDocument(Request $request, StoreDocumentRequest $formRequest, $code)
    {
        $request->merge([
            'beneficiary-documents-related' => true,
        ]);

        return $this->repository->storeDocument($formRequest, $code);
    }

    public function downloadDocument(Request $request, $code, $documentId)
    {
        $request->merge([
            'beneficiary-documents-related' => true,
        ]);

        return $this->repository->downloadDocument($request, $code, $documentId);
    }

    public function destroyDocument(Request $request, $code, $documentId)
    {
        $request->merge([
            'beneficiary-document-deletion' => true
        ]);

        return $this->repository->destroyDocument($request, $code, $documentId);
    }

    public function provincesOptions(Request $request)
    {
        $sorts = [
            'name' => 'asc'
        ];

        $request->merge([
            'beneficiary-options' => true,
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
            'beneficiary-options' => true,
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
            'beneficiary-options' => true,
            'sort' => $this->handleQueries('sort', $request, $sorts),
            'all' => true
        ]);

        $model = new Barangay;

        return $model->build();
    }

    public function classificationOptions(Request $request)
    {
        $company = Auth::user()->company();

        $filters = [
            'companyCode' => $company->slug->code,
            'enabled' => 1
        ];

        $sorts = [
            'name' => 'asc'
        ];

        $request->merge([
            'beneficiary-options' => true,
            'filter' => $this->handleQueries('filter', $request, $filters),
            'sort' => $this->handleQueries('sort', $request, $sorts),
            'all' => true
        ]);

        $model = new CompanyClassification;

        return $model->build();
    }

    public function officerClassificationOptions(Request $request)
    {
        $company = Auth::user()->company();

        $filters = [
            'companyCode' => $company->slug->code,
            'enabled' => 1
        ];

        $sorts = [
            'name' => 'asc'
        ];

        $request->merge([
            'beneficiary-options' => true,
            'filter' => $this->handleQueries('filter', $request, $filters),
            'sort' => $this->handleQueries('sort', $request, $sorts),
            'all' => true
        ]);

        $model = new CompanyOfficerClassification;

        return $model->build();
    }

    public function questionnaireOptions(Request $request)
    {
        $company = Auth::user()->company();

        $filters = [
            'companyCode' => $company->slug->code,
            'enabled' => 1,
        ];

        $sorts = [
            'orderNo' => 'asc'
        ];

        $request->merge([
            'beneficiary-options' => true,
            'filter' => $this->handleQueries('filter', $request, $filters),
            'sort' => $this->handleQueries('sort', $request, $sorts),
            'all' => true
        ]);

        $model = new CompanyQuestionnaire;

        return $model->build();
    }

    public function reportFieldOptions(Request $request)
    {
        $model = new Beneficiary;

        return [
            'data' => $model->reportFieldOptions
        ];
    }

    public function relationshipOptions(Request $request)
    {
        $model = new BeneficiaryRelative;

        return $model->relationships;
    }

    public function beneficiaryOptions(Request $request)
    {
        $request->merge([
            'beneficiary-options' => true,
        ]);

        $company = Auth::user()->company();

        $model = new Beneficiary;

        return $model->where('company_id', $company->id)
            ->where(function ($q) use ($request) {
                if ($request->has('firstName') && $request->get('firstName')):
                    $q->where('first_name', 'LIKE', '%' . $request->get('firstName') . '%');
                endif;

                if ($request->has('middleName') && $request->get('middleName')):
                    $q->where('middle_name', 'LIKE', '%' . $request->get('middleName') . '%');
                endif;

                if ($request->has('lastName') && $request->get('lastName')):
                    $q->where('last_name', 'LIKE', '%' . $request->get('lastName') . '%');
                endif;
            })
            ->orderBy('last_name', 'asc')
            ->get();
    }

    public function beneficiaryNetworkOptions(Request $request)
    {
        $request->merge([
            'beneficiary-network-options' => true,
        ]);

        $company = Auth::user()->company();

        $model = new Beneficiary;

        return $model->where('company_id', $company->id)
            ->where(function ($q) use ($request) {
                if ($request->has('firstName') && $request->get('firstName')):
                    $q->where('first_name', 'LIKE', '%' . $request->get('firstName') . '%');
                endif;

                if ($request->has('middleName') && $request->get('middleName')):
                    $q->where('middle_name', 'LIKE', '%' . $request->get('middleName') . '%');
                endif;

                if ($request->has('lastName') && $request->get('lastName')):
                    $q->where('last_name', 'LIKE', '%' . $request->get('lastName') . '%');
                endif;
            })
            ->orderBy('last_name', 'asc')
            ->get();
    }

    public function idTemplateOptions(Request $request)
    {
        $company = Auth::user()->company();

        $filters = [
            'companyCode' => $company->slug->code,
            'enabled' => 1
        ];

        $sorts = [
            'name' => 'asc'
        ];

        $request->merge([
            'beneficiary-options' => true,
            'filter' => $this->handleQueries('filter', $request, $filters),
            'sort' => $this->handleQueries('sort', $request, $sorts),
            'all' => true
        ]);

        $model = new CompanyIdTemplate;

        return $model->build();
    }

    public function documentTemplateOptions(Request $request)
    {
        $company = Auth::user()->company();

        $filters = [
            'companyCode' => $company->slug->code,
            'enabled' => 1
        ];

        $sorts = [
            'name' => 'asc'
        ];

        $request->merge([
            'beneficiary-options' => true,
            'filter' => $this->handleQueries('filter', $request, $filters),
            'sort' => $this->handleQueries('sort', $request, $sorts),
            'all' => true
        ]);

        $model = new CompanyDocumentTemplate;

        return $model->build();
    }

    public function storeBeneficiaryOption(Request $request, StoreBeneficiaryOptionRequest $formRequest)
    {
        $request->merge([
            'beneficiary-options' => true,
        ]);

        return $this->repository->storeBeneficiaryOption($formRequest);
    }

    public function relationshipsOptions() 
    {
        $model = new BeneficiaryRelative;

        return $model->relationships;
    }
}
