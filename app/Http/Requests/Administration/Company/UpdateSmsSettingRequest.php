<?php

namespace App\Http\Requests\Administration\Company;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class UpdateSmsSettingRequest extends Request
{
    public function authorize()
    {
        return Auth::user()->account->account_type == 1;
    }

    public function rules()
    {
        return [
            'sms_status' => 'required|in:0,1',
            'otp_status' => 'required|in:0,1',
            'header_name' => 'required',
            'footer_name' => 'nullable',
            'branding_sender_name' => 'required',
            'branding_api_url' => 'required',
            'branding_api_code' => 'required',
            'max_char_per_sms' => 'required|numeric',
            'credit_per_branding_sms' => 'required|numeric',
            'credit_per_regular_sms' => 'required|numeric',
            'credit_threshold' => 'required|numeric',
        ];
    }

    public function messages()
    {
        return [
            'header_name.required' => 'Header name is required.',
            'footer_name.required' => 'Header name is required.',
            'sms_status.required' => 'Status is required.',
            'sms_status.in' => 'Status accepts valid value only.',
            'otp_status.required' => 'Status is required.',
            'otp_status.in' => 'Status accepts valid value only.',
            'branding_sender_name.required' => 'Sender Name is required.',
            'branding_api_url.required' => 'API URL is required.',
            'branding_api_code.required' => 'API Code is required.',
            'max_char_per_sms.required' => 'Max char / SMS is required.',
            'max_char_per_sms.numeric' => 'Max char / SMS accepts numeric value only.',
            'credit_per_branding_sms.required' => 'Credit / Branding SMS is required',
            'credit_per_branding_sms.numeric' => 'Credit / Branding SMS accepts numeric value only.',
            'credit_per_regular_sms.required' => 'Credit / Regular SMS is required',
            'credit_per_regular_sms.numeric' => 'Credit / Regular SMS accepts numeric value only.',
            'credit_threshold.required' => 'Max allowable credit is required',
            'credit_threshold.numeric' => 'Max allowable credit accepts numeric value only.',
        ];
    }
}
