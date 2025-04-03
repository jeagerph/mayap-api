<?php

namespace App\Http\Repositories\Base;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

use App\Models\CompanyCallSetting;

class CompanyCallSettingRepository
{
    public $defaultData = [
        'call_credit' => 0.00,
        'call_status' => 0,
        'account_sid' => 'ACd1d19a5cb3dcfd8878652af22844e780',
        'auth_token' => '8166a756ccd7856df01ce91940e2bbc5',
        'auth_url' => 'http://demo.twilio.com/docs/voice.xml',
        'phone_no' => '+13253133642',
        'api_key' => null,
        'api_secret' => null,
        'app_sid' => null,
        'is_recording' => 0,
        'recording_status_url' => null,
    ];

    public function new($data)
    {
        return new CompanyCallSetting([
            'call_credit' => 0,
            'call_status' => 0,
            'account_sid' => $data['account_sid'],
            'auth_token' => $data['auth_token'],
            'auth_url' => null,
            'phone_no' => $data['phone_no'],
            'api_key' => $data['api_key'],
            'api_secret' => $data['api_secret'],
            'app_sid' => $data['app_sid'],
            'is_recording' => $data['is_recording'],
            'recording_status_url' => $data['recording_status_url'],
            'created_by' => Auth::id() ?: 1
        ]);
    }
}
?>