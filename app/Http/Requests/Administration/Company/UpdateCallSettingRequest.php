<?php

namespace App\Http\Requests\Administration\Company;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class UpdateCallSettingRequest extends Request
{
    public function authorize()
    {
        return Auth::user()->account->account_type == 1;
    }

    public function rules()
    {
        return [
            'call_status' => 'required|in:0,1',
            'account_sid' => 'required',
            'auth_token' => 'required',
            // 'auth_url' => 'required',
            'phone_no' => 'required',
            'api_key' => 'required',
            'api_secret' => 'required',
            'app_sid' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'call_status.required' => 'Status is required.',
            'call_status.in' => 'Status accepts valid value only.',
            'account_sid.required' => 'Account SID is required.',
            'auth_token.required' => 'Auth token is required.',
            // 'auth_url.required' => 'Auth URL is required.',
            'phone_no.required' => 'Phone no. is required.',
            'api_key.required' => 'API Key is required.',
            'api_secret.required' => 'API Secret is required.',
            'app_sid.required' => 'App SID is required.',
        ];
    }
}
