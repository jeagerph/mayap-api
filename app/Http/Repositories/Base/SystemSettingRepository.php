<?php

namespace App\Http\Repositories\Base;

use Illuminate\Support\Facades\Auth;

use App\Models\Slug;
use App\Models\SystemSetting;

use App\Traits\MeSender;

class SystemSettingRepository
{
    use MeSender;

    public function sender($senderId, $mobileNumber, $message)
    {
        if (!mobileNumberValidator($mobileNumber))
            return abort(403, 'Invalid contact number.');

        $setting = SystemSetting::where('is_default', 1)->first();

        if($senderId == 'company'):

            $response = $this->fireTxtbox(
                $setting->branding_api_url,
                $setting->branding_api_code,
                $mobileNumber,
                $message
            );

        elseif($senderId == 'velcro'):

            $response = $this->fireTxtbox(
                env('VELCRO_TXTBOX_URL'),
                env('VELCRO_TXTBOX_API_KEY'),
                $mobileNumber,
                $message
            );

        endif;

        return $response;
    }

    public function caller($senderId, $mobileNumber)
    {
        // if (!phoneNumberValidator($mobileNumber))
        //     return abort(403, 'Invalid contact number.');

        $setting = SystemSetting::where('is_default', 1)->first();

        if($senderId == 'company'):

            $response = $this->fireCall(
                $setting->call_account_sid,
                $setting->call_auth_token,
                $setting->call_auth_url,
                $setting->call_phone_no,
                $mobileNumber,
            );

        elseif($senderId == 'velcro'):

            $response = $this->fireCall(
                $setting->call_account_sid,
                $setting->call_auth_token,
                $setting->call_auth_url,
                $setting->call_phone_no,
                $mobileNumber,
            );

        endif;

        return $response;
    }

    public function diafaan($senderId, $mobileNumber, $message)
    {
        if (!mobileNumberValidator($mobileNumber))
            return abort(403, 'Invalid contact number.');

        $setting = SystemSetting::where('is_default', 1)->first();

        if($senderId == 'otp'):

            $response = $this->fireDiafaanOtp(
                $mobileNumber,
                $message
            );

        elseif($senderId == 'message'):

            $response = $this->fireDiafaanDefault(
                diafaanMobileNumber($mobileNumber),
                $message
            );

        else:
            $response = [
                'response' => 'Invalid senderId.',
                'statusCode' => 404
            ];

        endif;

        return $response;
    }

    public function checkSmsServiceStatus()
    {
        $setting = SystemSetting::where('is_default', 1)->first();

        if (!$setting->sms_service_status)
            return abort(403, 'SMS service is not available at the moment. Please try again later.');
    }

    public function isSmsServiceActive()
    {
        $setting = SystemSetting::where('is_default', 1)->first();

        return $setting->sms_service_status;
    }

    public function checkCallServiceStatus()
    {
        $setting = SystemSetting::where('is_default', 1)->first();

        if (!$setting->call_service_status)
            return abort(403, 'Call service is not available at the moment. Please try again later.');
    }

    public function isCallServiceActive()
    {
        $setting = SystemSetting::where('is_default', 1)->first();

        return $setting->call_service_status;
    }
}
?>