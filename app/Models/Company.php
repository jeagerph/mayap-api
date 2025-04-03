<?php

namespace App\Models;

use App\Models\Model;

use App\Observers\CompanyObserver as Observer;

class Company extends Model
{
    use Observer;
    
    public $searchFields = [
        'name' => ':name',
    ];

    public $sortFields = [
        'name' => ':name',
        'status' => ':status',
        'created' => ':created_at'
    ];

    public $filterFields = [
        'status' => ':status',
    ];

    public function slug()
    {
        return $this->morphOne('App\Models\Slug', 'slug');
    }

    public function companyAccounts()
    {
        return $this->hasMany('App\Models\CompanyAccount');
    }

    public function smsSetting()
    {
        return $this->hasOne('App\Models\CompanySmsSetting');
    }

    public function callSetting()
    {
        return $this->hasOne('App\Models\CompanyCallSetting');
    }

    public function invoiceSetting()
    {
        return $this->hasOne('App\Models\CompanyInvoiceSetting');
    }

    public function networkSetting()
    {
        return $this->hasOne('App\Models\CompanyNetworkSetting');
    }

    public function idSetting()
    {
        return $this->hasOne('App\Models\CompanyIdSetting');
    }

    public function mapSetting()
    {
        return $this->hasOne('App\Models\CompanyMapSetting');
    }

    public function smsCredits()
    {
        return $this->hasMany('App\Models\CompanySmsCredit');
    }

    public function smsTransactions()
    {
        return $this->hasMany('App\Models\CompanySmsTransaction');
    }

    public function callCredits()
    {
        return $this->hasMany('App\Models\CompanyCallCredit');
    }

    public function callTransactions()
    {
        return $this->hasMany('App\Models\CompanyCallTransaction');
    }

    public function classifications()
    {
        return $this->hasMany('App\Models\CompanyClassification');
    }

    public function officerClassifications()
    {
        return $this->hasMany('App\Models\CompanyOfficerClassification');
    }

    public function questionnaires()
    {
        return $this->hasMany('App\Models\CompanyQuestionnaire');
    }

    public function assignatories()
    {
        return $this->hasMany('App\Models\CompanyAssignatory');
    }

    public function idTemplates()
    {
        return $this->hasMany('App\Models\CompanyIdTemplate');
    }

    public function documentTemplates()
    {
        return $this->hasMany('App\Models\CompanyDocumentTemplate');
    }

    public function members()
    {
        return $this->hasMany('App\Models\Member');
    }

    public function beneficiaries()
    {
        return $this->hasMany('App\Models\Beneficiary');
    }

    public function beneficiaryNetworks()
    {
        return $this->hasMany('App\Models\BeneficiaryNetwork');
    }

    public function beneficiaryIncentives()
    {
        return $this->hasMany('App\Models\BeneficiaryIncentive');
    }

    public function beneficiaryAssistances()
    {
        return $this->hasMany('App\Models\BeneficiaryAssistance');
    }

    public function beneficiaryPatients()
    {
        return $this->hasMany('App\Models\BeneficiaryPatient');
    }

    public function beneficiaryMessages()
    {
        return $this->hasMany('App\Models\BeneficiaryMessage');
    }

    public function beneficiaryCalls()
    {
        return $this->hasMany('App\Models\BeneficiaryCall');
    }

    public function beneficiaryIdentifications()
    {
        return $this->hasMany('App\Models\BeneficiaryIdentification');
    }

    public function beneficiaryDocuments()
    {
        return $this->hasMany('App\Models\BeneficiaryDocument');
    }

    public function barangays()
    {
        return $this->hasMany('App\Models\CompanyBarangay');
    }

    public function voters()
    {
        return $this->hasMany('App\Models\Voter');
    }

    public function activities()
    {
        return $this->morphMany('App\Models\Activity', 'module');
    }

    public function lastReplenishSmsCreditRecord()
    {
        return $this->smsCredits()->where('credit_mode', 1)->latest()->first();
    }

    public function lastSmsTransactionRecord()
    {
        return $this->smsTransactions()->latest()->first();
    }

    public function lastReplenishCallCreditRecord()
    {
        return $this->callCredits()->where('credit_mode', 1)->latest()->first();
    }

    // public function lastCallTransactionRecord()
    // {
    //     return $this->callTransactions()->latest()->first();
    // }

    public function toArray()
    {
        $arr = [
            'slug' => $this->slug,
            'name' => $this->name,
            'address' => $this->address,
            'contact_no' => $this->contact_no,
            'logo' => $this->logo
                ? env('CDN_URL', '') . '/storage/' . $this->logo
                : null,
            'sub_logo' => $this->sub_logo
                ? env('CDN_URL', '') . '/storage/' . $this->sub_logo
                : null,
            'status' => $this->status,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at
                ? $this->updated_at->toDateTimeString()
                : $this->created_at->toDateTimeString()
        ];

        if (request()->has('admin-companies-related')):

            $arr['sms_credit'] = $this->smsSetting->sms_credit;
            $arr['running_balance'] = 0;

        endif;

        return $arr;
    }

    public function toArrayEdit()
    {
        return [
            'name' => $this->name,
            'address' => $this->address,
            'contact_no' => $this->contact_no,
        ];
    }

    public function toArrayMyAccountRelated()
    {
        $setting = \App\Models\SystemSetting::where('is_default', 1)->first();

        $provinces = $this->barangay_report_provinces
            ? explode(',', $this->barangay_report_provinces)
            : [];

        return [
            'slug' => $this->slug,
            'name' => $this->name,
            'logo' => $this->logo
                ? env('CDN_URL', '') . '/storage/' . $this->logo
                : '',
            'sub_logo' => $this->sub_logo
                ? env('CDN_URL', '') . '/storage/' . $this->sub_logo
                : '',
            'status' => $this->status,

            'sms_credit' => $this->smsSetting->sms_credit,
            'sms_setting' => [
                'sender_name' => $this->smsSetting->branding_sender_name,
                'max_char' => $this->smsSetting->max_char_per_sms,
                'credit_per_sent' => $this->smsSetting->credit_per_branding_sms,
                'header_name' => $this->smsSetting->header_name,
                'footer_name' => $this->smsSetting->footer_name,
                'sms_status' => $this->smsSetting->sms_status,
                'sms_service_status' => $setting->sms_service_status,
            ],
            'call_credit' => $this->callSetting->call_credit,
            'call_setting' => [
                'call_status' => $this->callSetting->call_status,
                'call_service_status' => $setting->call_service_status,
            ],
            'map_setting' => [
                'api_key' => $this->mapSetting->api_key,
                'latitude' => $this->mapSetting->latitude,
                'longitude' => $this->mapSetting->longitude,
            ],
            'province_id' => $provinces[0],
        ];
    }

    public function toArrayAdminCompaniesRelated()
    {
        return [
            'slug' => $this->slug,
            'name' => $this->name,
            'address' => $this->address,
            'contact_no' => $this->contact_no,
            'logo' => $this->logo
                ? env('CDN_URL', '') . '/storage/' . $this->logo
                : '',
            'sub_logo' => $this->sub_logo
                ? env('CDN_URL', '') . '/storage/' . $this->sub_logo
                : '',
            'status' => $this->status,

            'sms_credit' => $this->smsSetting->sms_credit,
        ];
    }
    
    public function toArrayMyCompanyRelated()
    {
        return [
            'slug' => $this->slug,
            'name' => $this->name,
            'address' => $this->address,
            'contact_no' => $this->contact_no,
            'logo' => $this->logo
                ? env('CDN_URL', '') . '/storage/' . $this->logo
                : '',
            'sub_logo' => $this->sub_logo
                ? env('CDN_URL', '') . '/storage/' . $this->sub_logo
                : '',
            'status' => $this->status,

            'sms_credit' => $this->smsSetting->sms_credit
        ];
    }
    
    public function toArrayMyCompanySmsTransactionsRelated()
    {
        return [
            'slug' => $this->slug,
            'name' => $this->name,
        ];
    }

    public function toArrayMembersRelated()
    {
        return [
            'slug' => $this->slug,
            'name' => $this->name,
        ];
    }

    public function toArrayMemberCheckingRelated()
    {
        return [
            'slug' => $this->slug,
            'name' => $this->name,
        ];
    }
}
