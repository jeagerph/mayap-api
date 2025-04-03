<?php

namespace App\Models;

use App\Models\Model;

class CompanyCallSetting extends Model
{
    public $searchFields = [];

    public $filterFields = [];

    public $sortFields = [
        'created' => ':created_at'
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }

    public function toArray()
    {
        $arr = [
            'call_status' => $this->call_status,
            'call_credit' => $this->call_credit,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at
                ? $this->updated_at->toDateTimeString()
                : $this->created_at->toDateTimeString()
        ];

        return $arr;
    }

    public function toArrayAdminCompaniesRelated()
    {
        return [
            'call_status' => $this->call_status,
            'account_sid' => $this->account_sid,
            'auth_token' => $this->auth_token,
            'auth_url' => $this->auth_url,
            'phone_no' => $this->phone_no,
            'api_key' => $this->api_key,
            'api_secret' => $this->api_secret,
            'app_sid' => $this->app_sid,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at
                ? $this->updated_at->toDateTimeString()
                : $this->created_at->toDateTimeString()
        ];
    }

    public function toArrayMyCompanyCallSettingRelated()
    {
        $setting = \App\Models\SystemSetting::where('is_default', 1)->first();

        return [
            'account_sid' => $this->account_sid,
            'auth_token' => $this->auth_token,
            'phone_no' => $this->phone_no,
            'api_key' => $this->api_key,
            'api_secret' => $this->api_secret,
            'app_sid' => $this->app_sid,
            'call_status' => $this->call_status,
            'call_service_status' => $setting->call_service_status,
        ];
    }
}
