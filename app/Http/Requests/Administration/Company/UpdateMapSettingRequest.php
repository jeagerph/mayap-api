<?php

namespace App\Http\Requests\Administration\Company;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class UpdateMapSettingRequest extends Request
{
    public function authorize()
    {
        return Auth::user()->account->account_type == 1;
    }

    public function rules()
    {
        return [
            'api_key' => 'nullable',
            'latitude' => 'required',
            'longitude' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'latitude.required' => 'Latitude is required.',
            'longitude.required' => 'Longitude is required.',
        ];
    }
}
