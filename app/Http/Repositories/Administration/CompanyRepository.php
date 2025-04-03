<?php

namespace App\Http\Repositories\Administration;

use Illuminate\Support\Facades\Auth;

use App\Models\Slug;
use App\Models\Company;
use App\Models\CompanyAccount;
use App\Models\CompanySmsSetting;
use App\Models\CompanyCallSetting;
use App\Models\CompanyNetworkSetting;
use App\Models\CompanyIdSetting;
use App\Models\CompanyMapSetting;
use App\Models\CompanyBarangay;
use App\Models\CompanySmsCredit;
use App\Models\CompanyCallCredit;

use App\Http\Repositories\Base\SlugRepository;
use App\Http\Repositories\Base\UserRepository;
use App\Http\Repositories\Base\AccountRepository;
use App\Http\Repositories\Base\AccountPermissionRepository;
use App\Http\Repositories\Base\CompanyAccountRepository;
use App\Http\Repositories\Base\CompanyBarangayRepository;
use App\Http\Repositories\Base\CompanySmsSettingRepository;
use App\Http\Repositories\Base\CompanyCallSettingRepository;
use App\Http\Repositories\Base\CompanyInvoiceSettingRepository;
use App\Http\Repositories\Base\CompanyNetworkSettingRepository;
use App\Http\Repositories\Base\CompanyIdSettingRepository;
use App\Http\Repositories\Base\CompanyMapSettingRepository;
use App\Http\Repositories\Base\CompanySmsCreditRepository;
use App\Http\Repositories\Base\CompanyCallCreditRepository;
use App\Http\Repositories\Base\CompanyRepository as BaseRepository;

class CompanyRepository
{
    public function __construct()
    {
        $this->baseRepository = new BaseRepository;
        $this->slugRepository = new SlugRepository;
        $this->userRepository = new UserRepository;
        $this->accountRepository = new AccountRepository;
        $this->permissionRepository = new AccountPermissionRepository;
        $this->companyAccountRepository = new CompanyAccountRepository;
        $this->companyBarangayRepository = new CompanyBarangayRepository;
        $this->companySmsSettingRepository = new CompanySmsSettingRepository;
        $this->companyCallSettingRepository = new CompanyCallSettingRepository;
        $this->companyInvoiceSettingRepository = new CompanyInvoiceSettingRepository;
        $this->companyNetworkSettingRepository = new CompanyNetworkSettingRepository;
        $this->companyIdSettingRepository = new CompanyIdSettingRepository;
        $this->companyMapSettingRepository = new CompanyMapSettingRepository;
        $this->companySmsCreditRepository = new CompanySmsCreditRepository;
        $this->companyCallCreditRepository = new CompanyCallCreditRepository;
    }

    public function store($request)
	{
        $newCompany = $this->baseRepository->store($request);

        $newUser = $this->userRepository->store($request);

        $newAccount = $newUser->account()->save(
            $this->accountRepository->new($request)
        );

        $newAccount->slug()->save(
            $this->slugRepository->new(
                $request->input('full_name') . ' Account'
            )
        );

        $newCompanyAccount = $newCompany->companyAccounts()->save(
            $this->companyAccountRepository->new([
                'account_id' => $newAccount->id,
                'company_position_id' => $request->input('company_position_id'),
            ])
        );

        $newCompanyAccount->slug()->save(
            $this->slugRepository->new(
                $request->input('full_name') . ' Company Account'
            )
        );

        $this->accountRepository->setCompanyPermissions($newAccount);

        $newCompany->invoiceSetting()->save(
            $this->companyInvoiceSettingRepository->new(
                $this->companyInvoiceSettingRepository->defaultData
            )
        );

        $newCompany->smsSetting()->save(
            $this->companySmsSettingRepository->new(
                $this->companySmsSettingRepository->defaultData
            )
        );

        $newCompany->callSetting()->save(
            $this->companyCallSettingRepository->new(
                $this->companyCallSettingRepository->defaultData
            )
        );

        $newCompany->networkSetting()->save(
            $this->companyNetworkSettingRepository->new(
                $this->companyNetworkSettingRepository->defaultData
            )
        );

        $newCompany->idSetting()->save(
            $this->companyIdSettingRepository->new([
                'name' => $newCompany->name,
                'address' => $newCompany->address,
                'contact_no' => $newCompany->contact_no,
            ])
        );

        $newCompany->mapSetting()->save(
            $this->companyMapSettingRepository->new(
                $this->companyMapSettingRepository->defaultData
            )
        );

        return $newCompany;
    }

    public function update($request, $code)
    {
        $model = Slug::findCodeOrDie($code);

        $company = $model->slug;

        $company->update([
            'name' => strtoupper($request->input('name')),
            'address' => $request->input('address'),
            'contact_no' => $request->input('contact_no'),
            'updated_by' => Auth::id()
        ]);

        return (Company::find($company->id))->toArrayAdminCompaniesRelated();
    }

    public function updateLogo($request, $code)
    {
        $model = Slug::findCodeOrDie($code);

        $company = $model->slug;

        $filePath = $this->baseRepository->uploadLogo(
            $company->logo,
            $request,
            'logo/company'
        );

        $company->update([
            'logo' => $filePath,
            'updated_by' => Auth::id()
        ]);

        return (Company::find($company->id))->toArrayAdminCompaniesRelated();
    }

    public function updateSubLogo($request, $code)
    {
        $model = Slug::findCodeOrDie($code);

        $company = $model->slug;

        $filePath = $this->baseRepository->uploadLogo(
            $company->sub_logo,
            $request,
            'logo/company'
        );

        $company->update([
            'sub_logo' => $filePath,
            'updated_by' => Auth::id()
        ]);

        return (Company::find($company->id))->toArrayAdminCompaniesRelated();
    }

    public function updateStatus($request, $code)
    {
        $model = Slug::findCodeOrDie($code);

        $company = $model->slug;

        $company->update([
            'status' => !$company->status,
            'updated_by' => Auth::id()
        ]);

        return (Company::find($company->id))->toArrayAdminCompaniesRelated();
    }

    public function destroy($request, $code)
    {
        $model = Slug::findCodeOrDie($code);

        $company = $model->slug;

        $this->baseRepository->isAllowedToDelete($company);

        $company->delete();

        return response([
            'message' => 'Data deleted successfully.'
        ], 200);
    }

    
    public function storeAccount($request, $code)
    {
        $model = Slug::findCodeOrDie($code);

        $company = $model->slug;

        $newUser = $this->userRepository->store($request);

        $newAccount = $newUser->account()->save(
            $this->accountRepository->new($request)
        );

        $newAccount->slug()->save(
            $this->slugRepository->new(
                $request->input('full_name') . ' Account'
            )
        );

        $newCompanyAccount = $company->companyAccounts()->save(
            $this->companyAccountRepository->new([
                'account_id' => $newAccount->id,
                'company_position_id' => $request->input('company_position_id'),
            ])
        );

        $newCompanyAccount->slug()->save(
            $this->slugRepository->new(
                $request->input('full_name') . ' Company Account'
            )
        );

        $this->accountRepository->setCompanyPermissions($newAccount);

        return $newCompanyAccount;
    }

    public function updateAccount($request, $code, $accountCode)
    {
        $model = Slug::findCodeOrDie($code);

        $company = $model->slug;

        $companyAccountSlug = Slug::findCodeOrDie($accountCode);

        $companyAccount = $companyAccountSlug->slug;

        $this->baseRepository->isAccountRelated($company, $companyAccount->id);

        $account = $companyAccount->account;
        $user = $account->user;

        $companyAccount->update([
            'company_position_id' => $request->input('company_position_id'),
            'updated_by' => Auth::id()
        ]);

        $account->update([
            'full_name' => $request->input('full_name'),
            'email' => $request->input('email'),
            'mobile_number' => $request->input('mobile_number'),
            'updated_by' => Auth::id()
        ]);

        $user->update([
            'username' => $request->input('username'),
            'updated_by' => Auth::id()
        ]);

        return (CompanyAccount::find($companyAccount->id))->toArrayAdminCompanyAccountsRelated();
    }

    public function updateAccountPermission($request, $code, $accountCode)
    {
        $model = Slug::findCodeOrDie($code);

        $company = $model->slug;

        $companyAccountSlug = Slug::findCodeOrDie($accountCode);

        $companyAccount = $companyAccountSlug->slug;

        $this->baseRepository->isAccountRelated($company, $companyAccount->id);

        $account = $companyAccount->account;
        $user = $account->user;

        $permission = $account->permissions()->where('module_id', $request->input('module_id'))->first();

        if($permission):

            $permission->update([
                'access' => $request->input('access'),
                'index' => $request->input('index'),
                'store' => $request->input('store'),
                'update' => $request->input('update'),
                'destroy' => $request->input('destroy'),
                'updated_by' => Auth::id()
            ]);

            return $permission;
        else:
            $newPermission = $account->permissions()->save(
                $this->permissionRepository->new([
                    'module_id' => $request->input('module_id'),
                    'access' => $request->input('access'),
                    'index' => $request->input('index'),
                    'store' => $request->input('store'),
                    'update' => $request->input('update'),
                    'destroy' => $request->input('destroy'),
                ])
            );
            
            return $newPermission;
        endif;
    }

    public function destroyAccount($request, $code, $accountCode)
    {
        $model = Slug::findCodeOrDie($code);

        $company = $model->slug;

        $companyAccountSlug = Slug::findCodeOrDie($accountCode);

        $companyAccount = $companyAccountSlug->slug;

        $this->baseRepository->isAccountRelated($company, $companyAccount->id);

        $account = $companyAccount->account;

        if ($account):
            $account->update([
                'full_name' => 'DELETED',
                'updated_by' => Auth::id(),
            ]);

            $user = $account->user;

            if ($user):
                $user->update([
                    'username' => 'DELETED',
                    'locked' => 1,
                    'updated_by' => Auth::id()
                ]);
            endif;
        endif;

        $companyAccount->delete();

        return response([
            'message' => 'Data deleted successfully.'
        ], 200);
    }

    public function storeBarangay($request, $code)
    {
        $model = Slug::findCodeOrDie($code);

        $company = $model->slug;

        $this->baseRepository->isBarangayAlreadyExist($company, $request->input('barangay_id'));

        $newBarangay = $company->barangays()->save(
            $this->companyBarangayRepository->new($request, $company)
        );

        return $newBarangay;
    }

    public function updateBarangay($request, $code, $id)
    {
        $model = Slug::findCodeOrDie($code);

        $company = $model->slug;

        $barangay = $this->baseRepository->isBarangayRelated($company, $id);

        $barangay->update(
            $this->companyBarangayRepository->update($request)
        );

        return (CompanyBarangay::find($barangay->id))->toArrayAdminCompanyBarangaysRelated();
    }

    public function updateCityLogo($request, $code, $id)
    {
        $model = Slug::findCodeOrDie($code);

        $company = $model->slug;

        $barangay = $this->baseRepository->isBarangayRelated($company, $id);

        $filePath = $this->companyBarangayRepository->uploadLogo(
            $barangay->city_logo,
            $request,
            'city/logo'
        );

        $barangay->update([
            'city_logo' => $filePath,
            'updated_by' => Auth::id()
        ]);

        return (CompanyBarangay::find($barangay->id))->toArrayAdminCompanyBarangaysRelated();
    }

    public function updateBarangayLogo($request, $code, $id)
    {
        $model = Slug::findCodeOrDie($code);

        $company = $model->slug;

        $barangay = $this->baseRepository->isBarangayRelated($company, $id);

        $filePath = $this->companyBarangayRepository->uploadLogo(
            $barangay->barangay_logo,
            $request,
            'barangay/logo'
        );

        $barangay->update([
            'barangay_logo' => $filePath,
            'updated_by' => Auth::id()
        ]);

        return (CompanyBarangay::find($barangay->id))->toArrayAdminCompanyBarangaysRelated();
    }

    public function updateBarangayStatus($request, $code, $id)
    {
        $model = Slug::findCodeOrDie($code);

        $company = $model->slug;

        $barangay = $this->baseRepository->isBarangayRelated($company, $id);

        $barangay->update([
            'status' => !$barangay->status,
            'updated_by' => Auth::id()
        ]);

        return (CompanyBarangay::find($barangay->id))->toArrayAdminCompanyBarangaysRelated();
    }

    public function destroyBarangay($request, $code, $id)
    {
        $model = Slug::findCodeOrDie($code);

        $company = $model->slug;

        $barangay = $this->baseRepository->isBarangayRelated($company, $id);

        $barangay->delete();

        return response([
            'message' => 'Data deleted successfully.'
        ], 200);
    }

    public function showSmsSetting($request, $code)
    {
        $companySlug = Slug::findCodeOrDie($code);
        $company = $companySlug->slug;

        $smsSetting = $company->smsSetting;

        if ($smsSetting) return $smsSetting->toArrayAdminCompaniesRelated();

        $newSmsSetting = $company->smsSetting()->save(
            $this->companySmsSettingRepository->new(
                $this->companySmsSettingRepository->defaultData
            )
        );

        return $newSmsSetting->toArrayAdminCompaniesRelated();
    }

    public function updateSmsSetting($request, $code)
    {
        $companySlug = Slug::findCodeOrDie($code);
        $company = $companySlug->slug;

        $smsSetting = $company->smsSetting;

        $smsSetting->update([
            'sms_status' => $request->input('sms_status'),
            'otp_status' => $request->input('otp_status'),
            'diafaan_status' => $request->input('diafaan_status'),
            'header_name' => strtoupper($request->input('header_name')),
            'footer_name' => $request->input('footer_name'),
            'branding_sender_name' => $request->input('branding_sender_name'),
            'branding_api_url' => $request->input('branding_api_url'),
            'branding_api_code' => $request->input('branding_api_code'),
            'max_char_per_sms' => $request->input('max_char_per_sms'),
            'credit_per_branding_sms' => $request->input('credit_per_branding_sms'),
            'credit_per_regular_sms' => $request->input('credit_per_regular_sms'),
            'credit_threshold' => $request->input('credit_threshold'),
            'birthday_status' => $request->input('birthday_status'),
            'birthday_header' => $request->input('birthday_header'),
            'birthday_message' => $request->input('birthday_message'),
            'report_status' => $request->input('report_status'),
            'report_template' => $request->input('report_template'),
            'report_mobile_numbers' => count($request->input('report_mobile_numbers'))
                ? implode(',', $request->input('report_mobile_numbers'))
                : null,
            'updated_by' => Auth::id()
        ]);

        return (CompanySmsSetting::find($smsSetting->id))->toArrayAdminCompaniesRelated();
    }

    public function showCallSetting($request, $code)
    {
        $companySlug = Slug::findCodeOrDie($code);
        $company = $companySlug->slug;

        $setting = $company->callSetting;

        if ($setting) return $setting->toArrayAdminCompaniesRelated();

        $newSetting = $company->callSetting()->save(
            $this->companyCallSettingRepository->new(
                $this->companyCallSettingRepository->defaultData
            )
        );

        return $newSetting->toArrayAdminCompaniesRelated();
    }

    public function updateCallSetting($request, $code)
    {
        $companySlug = Slug::findCodeOrDie($code);
        $company = $companySlug->slug;

        $setting = $company->callSetting;

        $setting->update([
            'call_status' => $request->input('call_status'),
            'account_sid' => $request->input('account_sid'),
            'auth_token' => $request->input('auth_token'),
            // 'auth_url' => $request->input('auth_url'),
            'phone_no' => $request->input('phone_no'),
            'api_key' => $request->input('api_key'),
            'api_secret' => $request->input('api_secret'),
            'app_sid' => $request->input('app_sid'),
            'updated_by' => Auth::id()
        ]);

        return (CompanyCallSetting::find($setting->id))->toArrayAdminCompaniesRelated();
    }

    public function showInvoiceSetting($request, $code)
    {
        $companySlug = Slug::findCodeOrDie($code);
        $company = $companySlug->slug;

        $invoiceSetting = $company->invoiceSetting;

        if ($invoiceSetting) return $invoiceSetting->toArrayAdminCompaniesRelated();

        $newInvoiceSetting = $company->invoiceSetting()->save(
            $this->companyInvoiceSettingRepository->new(
                $this->companyInvoiceSettingRepository->defaultData
            )
        );

        return $newInvoiceSetting->toArrayAdminCompaniesRelated();
    }

    public function updateInvoiceSetting($request, $code)
    {
        $companySlug = Slug::findCodeOrDie($code);
        $company = $companySlug->slug;

        $invoiceSetting = $company->invoiceSetting;

        $invoiceSetting->update([
            'domain_hosting' => $request->input('domain_hosting'),
            'branding_sms' => $request->input('branding_sms'),
            'regular_sms' => $request->input('regular_sms'),
            'virtual_storage' => $request->input('virtual_storage'),
            'show_left_representative' => $request->input('show_left_representative'),
            'left_representative_name' => $request->input('left_representative_name'),
            'left_representative_position' => $request->input('left_representative_position'),
            'show_right_representative' => $request->input('show_right_representative'),
            'right_representative_name' => $request->input('right_representative_name'),
            'right_representative_position' => $request->input('right_representative_position'),
            'updated_by' => Auth::id()
        ]);

        return (CompanyInvoiceSetting::find($invoiceSetting->id))->toArrayAdminCompaniesRelated();
    }

    public function showNetworkSetting($request, $code)
    {
        $companySlug = Slug::findCodeOrDie($code);
        $company = $companySlug->slug;

        $networkSetting = $company->networkSetting;

        if ($networkSetting) return $networkSetting->toArrayAdminCompaniesRelated();

        $newNetworkSetting = $company->networkSetting()->save(
            $this->companyNetworkSettingRepository->new(
                $this->companyNetworkSettingRepository->defaultData
            )
        );

        return $newNetworkSetting->toArrayAdminCompaniesRelated();
    }

    public function updateNetworkSetting($request, $code)
    {
        $companySlug = Slug::findCodeOrDie($code);
        $company = $companySlug->slug;

        $networkSetting = $company->networkSetting;

        $networkSetting->update([
            'master_degree_enabled' => $request->input('master_degree_enabled'),
            'master_degree_points' => $request->input('master_degree_points'),
            'first_degree_enabled' => $request->input('first_degree_enabled'),
            'first_degree_points' => $request->input('first_degree_points'),
            'second_degree_enabled' => $request->input('second_degree_enabled'),
            'second_degree_points' => $request->input('second_degree_points'),
            'third_degree_enabled' => $request->input('third_degree_enabled'),
            'third_degree_points' => $request->input('third_degree_points'),
            'fourth_degree_enabled' => $request->input('fourth_degree_enabled'),
            'fourth_degree_points' => $request->input('fourth_degree_points'),
            'fifth_degree_enabled' => $request->input('fifth_degree_enabled'),
            'fifth_degree_points' => $request->input('fifth_degree_points'),
            'updated_by' => Auth::id()
        ]);

        return (CompanyNetworkSetting::find($networkSetting->id))->toArrayAdminCompaniesRelated();
    }

    public function showIdSetting($request, $code)
    {
        $companySlug = Slug::findCodeOrDie($code);
        $company = $companySlug->slug;

        $idSetting = $company->idSetting;

        if ($idSetting) return $idSetting->toArrayAdminCompaniesRelated();

        $newIdSetting = $company->idSetting()->save(
            $this->companyIdSettingRepository->new([
                'name' => $company->name,
                'address' => $company->address,
                'contact_no' => $company->contact_no
            ])
        );

        return $newIdSetting->toArrayAdminCompaniesRelated();
    }

    public function updateIdSetting($request, $code)
    {
        $companySlug = Slug::findCodeOrDie($code);
        $company = $companySlug->slug;

        $idSetting = $company->idSetting;

        $idSetting->update([
            'name' => $request->input('name'),
            'address' => $request->input('address'),
            'contact_no' => $request->input('contact_no'),
            'updated_by' => Auth::id()
        ]);

        return (CompanyIdSetting::find($idSetting->id))->toArrayAdminCompaniesRelated();
    }

    public function showMapSetting($request, $code)
    {
        $companySlug = Slug::findCodeOrDie($code);
        $company = $companySlug->slug;

        $mapSetting = $company->mapSetting;

        if ($mapSetting) return $mapSetting->toArrayAdminCompaniesRelated();

        $newMapSetting = $company->mapSetting()->save(
            $this->companyMapSettingRepository->new(
                $this->companyMapSettingRepository->defaultData
            )
        );

        return $newMapSetting->toArrayAdminCompaniesRelated();
    }

    public function updateMapSetting($request, $code)
    {
        $companySlug = Slug::findCodeOrDie($code);
        $company = $companySlug->slug;

        $mapSetting = $company->mapSetting;

        $mapSetting->update([
            'api_key' => $request->input('api_key'),
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
            'updated_by' => Auth::id()
        ]);

        return (CompanyMapSetting::find($mapSetting->id))->toArrayAdminCompaniesRelated();
    }

    public function storeSmsCredit($request, $code)
    {
        $companySlug = Slug::findCodeOrDie($code);
        $company = $companySlug->slug;

        $newCredit = $company->smsCredits()->save(
            $this->companySmsCreditRepository->new($request, $company)
        );

        $this->baseRepository->refreshCreditAmount($company);

        return $newCredit;
    }

    public function updateSmsCredit($request, $code, $id)
    {
        $companySlug = Slug::findCodeOrDie($code);
        $company = $companySlug->slug;

        $smsCredit = $this->baseRepository->isSmsCreditRelated($company, $id);

        $smsCredit->update([
            'credit_date' => $request->input('credit_date'),
            'credit_mode' => $request->input('credit_mode'),
            'amount' => $request->input('amount'),
            'remarks' => $request->input('remarks'),
        ]);

        $this->baseRepository->refreshCreditAmount($company);

        return (CompanySmsCredit::find($smsCredit->id))->toArrayAdminCompanySmsCreditsRelated();
    }

    public function destroySmsCredit($request, $code, $id)
    {
        $companySlug = Slug::findCodeOrDie($code);
        $company = $companySlug->slug;

        $smsCredit = $this->baseRepository->isSmsCreditRelated($company, $id);

        $smsCredit->delete();

        $this->baseRepository->refreshCreditAmount($company);

        return response([
            'message' => 'Data deleted successfully.'
        ], 200);
    }

    public function storeCallCredit($request, $code)
    {
        $companySlug = Slug::findCodeOrDie($code);
        $company = $companySlug->slug;

        $newCredit = $company->callCredits()->save(
            $this->companyCallCreditRepository->new($request, $company)
        );

        $this->baseRepository->refreshCallCreditAmount($company);

        return $newCredit;
    }

    public function updateCallCredit($request, $code, $id)
    {
        $companySlug = Slug::findCodeOrDie($code);
        $company = $companySlug->slug;

        $credit = $this->baseRepository->isCallCreditRelated($company, $id);

        $credit->update([
            'credit_date' => $request->input('credit_date'),
            'credit_mode' => $request->input('credit_mode'),
            'amount' => $request->input('amount'),
            'remarks' => $request->input('remarks'),
        ]);

        $this->baseRepository->refreshCallCreditAmount($company);

        return (CompanyCallCredit::find($smsCredit->id))->toArrayAdminCompanyCallCreditsRelated();
    }

    public function destroyCallCredit($request, $code, $id)
    {
        $companySlug = Slug::findCodeOrDie($code);
        $company = $companySlug->slug;

        $callCredit = $this->baseRepository->isCallCreditRelated($company, $id);

        $callCredit->delete();

        $this->baseRepository->refreshCallCreditAmount($company);

        return response([
            'message' => 'Data deleted successfully.'
        ], 200);
    }

    public function showSummaryOfSmsCredits($request, $dates, $code)
    {
        $companySlug = Slug::findCodeOrDie($code);
        $company = $companySlug->slug;

        $setting = $company->smsSetting;

        return [
            'sms_credit' => $setting->sms_credit,
            'last_replenish_sms_credit_record' => $company->lastReplenishSmsCreditRecord(),
            'last_sms_transaction_record' => $company->lastSmsTransactionRecord()
        ];
    }

    public function showSummaryOfCallCredits($request, $dates, $code)
    {
        $companySlug = Slug::findCodeOrDie($code);
        $company = $companySlug->slug;

        $setting = $company->callSetting;

        return [
            'call_credit' => $setting->call_credit,
            'last_replenish_call_credit_record' => $company->lastReplenishCallCreditRecord(),
            'last_call_transaction_record' => null,
            // 'last_call_transaction_record' => $company->lastCallTransactionRecord()
        ];
    }
}
?>