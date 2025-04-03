<?php

namespace App\Http\Repositories\Base;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

use App\Models\CompanySmsSetting;

class CompanySmsSettingRepository
{
    public $defaultData = [
        'sms_credit' => 0.00,
        'sms_status' => 0,
        'otp_status' => 0,
        'diafaan_status' => 0,
        'header_name' => 'COMPANY :',
        'footer_name' => '',
        'branding_sender_name' => 'COMPANY',
        'branding_api_url' => 'https://ws-v2.txtbox.com/messaging/v1/sms/push',
        'branding_api_code' => '1a5fef9024bab8040ef8f22b98e7b2ea',
        'max_char_per_sms' => 159,
        'credit_per_branding_sms' => 0.50,
        'credit_per_regular_sms' => 0.30,
        'credit_threshold' => 0.00,
        'birthday_status' => 0,
        'birthday_header' => 'COMPANY:',
        'birthday_message' => 'Hi {{first_name}}, Happy birthday to you!',
        'report_status' => 0,
        'report_template' => 'default',
        'report_mobile_numbers' => null,
    ];

    public function new($data)
    {
        return new CompanySmsSetting([
            'sms_credit' => 0,
            'sms_status' => 0,
            'otp_status' => 0,
            'diafaan_status' => 0,
            'report_status' => 0,
            'header_name' => $data['header_name']
                ? strtoupper($data['header_name'])
                : null,
            'footer_name' => $data['footer_name'] ?: null,
            'branding_sender_name' => $data['branding_sender_name'] ?: null,
            'branding_api_url' => $data['branding_api_url'] ?: null,
            'branding_api_code' => $data['branding_api_code'] ?: null,
            'credit_threshold' => 0.00,
            'birthday_status' => 0,
            'birthday_header' => $data['birthday_header'],
            'birthday_message' => $data['birthday_message'],
            'report_status' => 0,
            'report_template' => $data['report_template'],
            'report_mobile_numbers' => null,
            'created_by' => Auth::id() ?: 1
        ]);
    }

    public function checkCreditBalance($company, $noOfRecipient, $rawMessage, $insertFooter = false)
    {
        $smsSetting = $company->smsSetting;

        $loadAmount = $smsSetting->sms_credit + $smsSetting->credit_threshold;

        $message = formSmsMessage(
            $rawMessage,
            $smsSetting ? $smsSetting->header_name : '',
            $smsSetting ? $smsSetting->footer_name : '',
            $insertFooter
        );

        $creditPerMessage = computeMessageCreditCharge(
            $message,
            $smsSetting->credit_per_branding_sms,
            $smsSetting->max_char_per_sms 
        );

        $forSendingAmount = $creditPerMessage * $noOfRecipient;

        if ($loadAmount <= $forSendingAmount):
            return abort(403, 'SMS credit balance is insufficient.');
        endif;
    }

    public function isCreditBalanceEnough($company, $noOfRecipient, $rawMessage, $insertFooter = false)
    {
        $smsSetting = $company->smsSetting;

        $loadAmount = $smsSetting->sms_credit + $smsSetting->credit_threshold;

        $message = formSmsMessage(
            $rawMessage,
            $smsSetting ? $smsSetting->header_name : '',
            $smsSetting ? $smsSetting->footer_name : '',
            $insertFooter
        );

        $creditPerMessage = computeMessageCreditCharge(
            $message,
            $smsSetting->credit_per_branding_sms,
            $smsSetting->max_char_per_sms 
        );

        $forSendingAmount = $creditPerMessage * $noOfRecipient;

        return $loadAmount <= $forSendingAmount;
    }
}
?>