<?php

namespace App\Models;

use App\Models\Model;

class CompanySmsSetting extends Model
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
            'max_char_per_sms' => $this->max_char_per_sms,
            'credit_per_branding_sms' => $this->credit_per_branding_sms,
            'credit_per_regular_sms' => $this->credit_per_regular_sms,
            'credit_threshold' => $this->credit_threshold,
            'header_name' => $this->header_name,
            'footer_name' => $this->footer_name,
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
            'sms_status' => $this->sms_status,
            'otp_status' => $this->otp_status,
            'diafaan_status' => $this->diafaan_status,
            'header_name' => $this->header_name,
            'footer_name' => $this->footer_name,
            'max_char_per_sms' => $this->max_char_per_sms,
            'credit_per_branding_sms' => $this->credit_per_branding_sms,
            'credit_per_regular_sms' => $this->credit_per_regular_sms,
            'credit_threshold' => $this->credit_threshold,
            'branding_sender_name' => $this->branding_sender_name,
            'branding_api_url' => $this->branding_api_url,
            'branding_api_code' => $this->branding_api_code,
            'birthday_status' => $this->birthday_status,
            'birthday_header' => $this->birthday_header,
            'birthday_message' => $this->birthday_message,
            'report_status' => $this->report_status,
            'report_template' => $this->report_template,
            'report_mobile_numbers' => $this->report_mobile_numbers
                ? explode(',', $this->report_mobile_numbers)
                : [],
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at
                ? $this->updated_at->toDateTimeString()
                : $this->created_at->toDateTimeString()
        ];
    }

    public function toArrayMyCompanySmsSettingRelated()
    {
        $setting = \App\Models\SystemSetting::where('is_default', 1)->first();

        return [
            'sms_status' => $this->sms_status,
            'diafaan_status' => $this->diafaan_status,
            'header_name' => $this->header_name,
            'footer_name' => $this->footer_name,
            'max_char_per_sms' => $this->max_char_per_sms,
            'credit_per_branding_sms' => $this->credit_per_branding_sms,
            'credit_per_regular_sms' => $this->credit_per_regular_sms,
            'credit_threshold' => $this->credit_threshold,
            'sms_service_status' => $setting->sms_service_status,
        ];
    }
}
