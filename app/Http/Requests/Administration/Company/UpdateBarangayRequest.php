<?php

namespace App\Http\Requests\Administration\Company;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class UpdateBarangayRequest extends Request
{
    public function authorize()
    {
        return Auth::user()->account->account_type == 1;
    }

    public function rules()
    {
        return [
            'province_name' => 'required',
            'city_name' => 'required',
            'barangay_name' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'province_name.required' => 'Province name is required.',
            'city_name.required' => 'City name is required.',
            'barangay_name.required' => 'Barangay name is required.',
        ];
    }
}
