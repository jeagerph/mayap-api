<?php

namespace App\Http\Controllers\API\Administration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Slug;
use App\Models\Module;
use App\Models\Activity;
use App\Models\Province;
use App\Models\City;
use App\Models\Barangay;
use App\Models\Company;
use App\Models\CompanyAccount;
use App\Models\CompanyPosition;
use App\Models\CompanyBarangay;
use App\Models\CompanySmsCredit;
use App\Models\CompanyCallCredit;

use App\Http\Requests\Administration\Company\StoreRequest;
use App\Http\Requests\Administration\Company\UpdateRequest;
use App\Http\Requests\Administration\Company\UpdatePhotoRequest;
use App\Http\Requests\Administration\Company\StoreAccountRequest;
use App\Http\Requests\Administration\Company\UpdateAccountRequest;
use App\Http\Requests\Administration\Company\UpdateSmsSettingRequest;
use App\Http\Requests\Administration\Company\UpdateCallSettingRequest;
use App\Http\Requests\Administration\Company\UpdateNetworkSettingRequest;
use App\Http\Requests\Administration\Company\UpdateIdSettingRequest;
use App\Http\Requests\Administration\Company\UpdateMapSettingRequest;
use App\Http\Requests\Administration\Company\StoreBarangayRequest;
use App\Http\Requests\Administration\Company\UpdateBarangayRequest;
use App\Http\Requests\Administration\Company\UpdateLogoRequest;
use App\Http\Requests\Administration\Company\StoreSmsCreditRequest;
use App\Http\Requests\Administration\Company\UpdateSmsCreditRequest;
use App\Http\Requests\Administration\Company\StoreCallCreditRequest;
use App\Http\Requests\Administration\Company\UpdateCallCreditRequest;

use App\Http\Repositories\Administration\CompanyRepository as Repository;

class CompanyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.permission');

        $this->repository = new Repository;
    }

    public function index(Request $request)
    {
        $sorts = [
            'status' => 'desc',
            'created' => 'desc',
        ];

        $request->merge([
            'admin-companies-related' => true,
            'sort' => $this->handleQueries('sort', $request, $sorts)
        ]);

        $model = new Company;

        return $model->build();
    }

    public function show(Request $request, $code)
    {
        $request->merge([
            'admin-companies-related' => true
        ]);

        $model = Slug::findCodeOrDie($code);

        return $model->slug->toArrayAdminCompaniesRelated();
    }

    public function edit(Request $request, $code)
    {
        $request->merge([
            'admin-companies-related' => true
        ]);

        $model = Slug::findCodeOrDie($code);

        return $model->slug->toArrayEdit();
    }

    public function store(Request $request, StoreRequest $formRequest)
    {
        $request->merge([
            'admin-companies-related' => true
        ]);

        return $this->repository->store($formRequest);
    }

    public function update(Request $request, UpdateRequest $formRequest, $code)
    {
        $request->merge([
            'admin-companies-related' => true
        ]);

        return $this->repository->update($formRequest, $code);
    }

    public function updateLogo(Request $request, UpdatePhotoRequest $formRequest, $code)
    {
        $request->merge([
            'admin-companies-related' => true
        ]);

        return $this->repository->updateLogo($formRequest, $code);
    }

    public function updateSubLogo(Request $request, UpdatePhotoRequest $formRequest, $code)
    {
        $request->merge([
            'admin-companies-related' => true
        ]);

        return $this->repository->updateSubLogo($formRequest, $code);
    }

    public function updateStatus(Request $request, $code)
    {
        $request->merge([
            'admin-companies-related' => true
        ]);

        return $this->repository->updateStatus($request, $code);
    }

    public function destroy(Request $request, $code)
    {
        $request->merge([
            'admin-company-deletion' => true
        ]);

        return $this->repository->destroy($request, $code);
    }

    public function showAccounts(Request $request, $code)
    {
        $module = Slug::findCodeOrDie($code);

        $filters = [
            'companyCode' => $code
        ];

        $sorts = [
            'created' => 'desc'
        ];

        $request->merge([
            'admin-company-accounts-related' => true,
            'filter' => $this->handleQueries('filter', $request, $filters),
            'sort' => $this->handleQueries('sort', $request, $sorts),
            'all' => true
        ]);

        $model = new CompanyAccount;

        return $model->build();
    }

    public function storeAccount(Request $request, StoreAccountRequest $formRequest, $code)
    {
        $request->merge([
            'admin-company-accounts-related' => true
        ]);

        return $this->repository->storeAccount($formRequest, $code);
    }

    public function updateAccount(Request $request, UpdateAccountRequest $formRequest, $code, $accountCode)
    {
        $request->merge([
            'admin-company-accounts-related' => true
        ]);

        return $this->repository->updateAccount($formRequest, $code, $accountCode);
    }

    public function updateAccountPermission(Request $request, $code, $accountCode)
    {
        $request->merge([
            'admin-company-accounts-related' => true
        ]);

        return $this->repository->updateAccountPermission($request, $code, $accountCode);
    }

    public function destroyAccount(Request $request, $code, $accountCode)
    {
        $request->merge([
            'admin-company-account-deletion' => true
        ]);

        return $this->repository->destroyAccount($request, $code, $accountCode);
    }

    public function showBarangays(Request $request, $code)
    {
        $module = Slug::findCodeOrDie($code);

        $filters = [
            'companyCode' => $code
        ];

        $sorts = [
            'barangayName' => 'asc',
            'created' => 'desc'
        ];

        $request->merge([
            'admin-company-barangays-related' => true,
            'filter' => $this->handleQueries('filter', $request, $filters),
            'sort' => $this->handleQueries('sort', $request, $sorts),
            'all' => true
        ]);

        $model = new CompanyBarangay;

        return $model->build();
    }

    public function storeBarangay(Request $request, StoreBarangayRequest $formRequest, $code)
    {
        $request->merge([
            'admin-company-barangays-related' => true
        ]);

        return $this->repository->storeBarangay($formRequest, $code);
    }

    public function updateBarangay(Request $request, UpdateBarangayRequest $formRequest, $code, $barangayId)
    {
        $request->merge([
            'admin-company-barangays-related' => true
        ]);

        return $this->repository->updateBarangay($formRequest, $code, $barangayId);
    }

    public function updateBarangayStatus(Request $request, $code, $barangayId)
    {
        $request->merge([
            'admin-company-barangays-related' => true
        ]);

        return $this->repository->updateBarangayStatus($request, $code, $barangayId);
    }

    public function updateCityLogo(Request $request, UpdateLogoRequest $formRequest, $code, $barangayId)
    {
        $request->merge([
            'admin-company-barangays-related' => true
        ]);

        return $this->repository->updateCityLogo($formRequest, $code, $barangayId);
    }

    public function updateBarangayLogo(Request $request, UpdateLogoRequest $formRequest, $code, $barangayId)
    {
        $request->merge([
            'admin-company-barangays-related' => true
        ]);

        return $this->repository->updateBarangayLogo($formRequest, $code, $barangayId);
    }

    public function destroyBarangay(Request $request, $code, $barangayId)
    {
        $request->merge([
            'admin-company-barangay-deletion' => true
        ]);

        return $this->repository->destroyBarangay($request, $code, $barangayId);
    }

    public function showActivities(Request $request, $code)
    {
        $module = Slug::findCodeOrDie($code);

        $filters = [
            'activityType' => 'App\\Models\\CompanyAccount',
            'activityId' => $module->slug_id
        ];

        $sorts = [
            'created' => 'desc'
        ];

        $request->merge([
            'admin-company-activities-related' => true,
            'filter' => $this->handleQueries('filter', $request, $filters),
            'sort' => $this->handleQueries('sort', $request, $sorts),
        ]);

        $model = new Activity;

        return $model->build();
    }

    public function showSmsSetting(Request $request, $code)
    {
        $request->merge([
            'admin-companies-related' => true
        ]);

        return $this->repository->showSmsSetting($request, $code);
    }

    public function updateSmsSetting(Request $request, UpdateSmsSettingRequest $formRequest, $code)
    {
        $request->merge([
            'admin-companies-related' => true
        ]);

        return $this->repository->updateSmsSetting($formRequest, $code);
    }

    public function showCallSetting(Request $request, $code)
    {
        $request->merge([
            'admin-companies-related' => true
        ]);

        return $this->repository->showCallSetting($request, $code);
    }

    public function updateCallSetting(Request $request, UpdateCallSettingRequest $formRequest, $code)
    {
        $request->merge([
            'admin-companies-related' => true
        ]);

        return $this->repository->updateCallSetting($formRequest, $code);
    }

    public function showNetworkSetting(Request $request, $code)
    {
        $request->merge([
            'admin-companies-related' => true
        ]);

        return $this->repository->showNetworkSetting($request, $code);
    }

    public function updateNetworkSetting(Request $request, UpdateNetworkSettingRequest $formRequest, $code)
    {
        $request->merge([
            'admin-companies-related' => true
        ]);

        return $this->repository->updateNetworkSetting($formRequest, $code);
    }

    public function showIdSetting(Request $request, $code)
    {
        $request->merge([
            'admin-companies-related' => true
        ]);

        return $this->repository->showIdSetting($request, $code);
    }

    public function updateIdSetting(Request $request, UpdateIdSettingRequest $formRequest, $code)
    {
        $request->merge([
            'admin-companies-related' => true
        ]);

        return $this->repository->updateIdSetting($formRequest, $code);
    }

    public function showMapSetting(Request $request, $code)
    {
        $request->merge([
            'admin-companies-related' => true
        ]);

        return $this->repository->showMapSetting($request, $code);
    }

    public function updateMapSetting(Request $request, UpdateMapSettingRequest $formRequest, $code)
    {
        $request->merge([
            'admin-companies-related' => true
        ]);

        return $this->repository->updateMapSetting($formRequest, $code);
    }

    public function showSmsCredits(Request $request, $code)
    {
        $module = Slug::findCodeOrDie($code);

        $filters = [
            'companyCode' => $code
        ];

        $sorts = [
            'creditDate' => 'desc',
            'created' => 'desc'
        ];

        $request->merge([
            'admin-company-sms-credits-related' => true,
            'filter' => $this->handleQueries('filter', $request, $filters),
            'sort' => $this->handleQueries('sort', $request, $sorts),
        ]);

        $model = new CompanySmsCredit;

        return $model->build();
    }

    public function storeSmsCredit(Request $request, StoreSmsCreditRequest $formRequest, $code)
    {
        $request->merge([
            'admin-company-sms-credits-related' => true
        ]);

        return $this->repository->storeSmsCredit($formRequest, $code);
    }

    public function updateSmsCredit(Request $request, UpdateSmsCreditRequest $formRequest, $code, $id)
    {
        $request->merge([
            'admin-company-sms-credits-related' => true
        ]);

        return $this->repository->updateSmsCredit($formRequest, $code, $id);
    }

    public function destroySmsCredit(Request $request, $code, $id)
    {
        $request->merge([
            'admin-company-sms-credit-deletion' => true
        ]);

        return $this->repository->destroySmsCredit($formRequest, $code, $id);
    }

    public function showCallCredits(Request $request, $code)
    {
        $module = Slug::findCodeOrDie($code);

        $filters = [
            'companyCode' => $code
        ];

        $sorts = [
            'creditDate' => 'desc',
            'created' => 'desc'
        ];

        $request->merge([
            'admin-company-call-credits-related' => true,
            'filter' => $this->handleQueries('filter', $request, $filters),
            'sort' => $this->handleQueries('sort', $request, $sorts),
        ]);

        $model = new CompanyCallCredit;

        return $model->build();
    }

    public function storeCallCredit(Request $request, StoreCallCreditRequest $formRequest, $code)
    {
        $request->merge([
            'admin-company-call-credits-related' => true
        ]);

        return $this->repository->storeCallCredit($formRequest, $code);
    }

    public function updateCallCredit(Request $request, UpdateCallCreditRequest $formRequest, $code, $id)
    {
        $request->merge([
            'admin-company-call-credits-related' => true
        ]);

        return $this->repository->updateCallCredit($formRequest, $code, $id);
    }

    public function destroyCallCredit(Request $request, $code, $id)
    {
        $request->merge([
            'admin-company-call-credit-deletion' => true
        ]);

        return $this->repository->destroyCallCredit($formRequest, $code, $id);
    }

    public function showSummaryOfSmsCredits(Request $request, $code)
    {
        $dates = $this->queryDates($request);
        
        return $this->repository->showSummaryOfSmsCredits($request, $dates, $code);
    }

    public function showSummaryOfCallCredits(Request $request, $code)
    {
        $dates = $this->queryDates($request);
        
        return $this->repository->showSummaryOfCallCredits($request, $dates, $code);
    }

    public function positionOptions(Request $request)
    {
        $filters = [
            'enabled' => 1
        ];

        $request->merge([
            'admin-company-options' => true,
            'filter' => $this->handleQueries('filter', $request, $filters),
            'all' => true
        ]);

        $model = new CompanyPosition;

        return $model->build();
    }

    public function moduleOptions(Request $request)
    {
        $filters = [
            'isAdmin' => 0
        ];

        $sorts = [
            'name' => 'asc'
        ];

        $request->merge([
            'admin-company-options' => true,
            'filter' => $this->handleQueries('filter', $request, $filters),
            'sort' => $this->handleQueries('sort', $request, $sorts),
            'all' => true
        ]);

        $model = new Module;

        return $model->build();
    }

    public function provincesOptions(Request $request)
    {
        $sorts = [
            'name' => 'asc'
        ];

        $request->merge([
            'company-options' => true,
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
            'company-options' => true,
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
            'company-options' => true,
            'sort' => $this->handleQueries('sort', $request, $sorts),
            'all' => true
        ]);

        $model = new Barangay;

        return $model->build();
    }
}
