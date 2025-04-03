<?php

namespace App\Http\Requests\Administration\Company;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class StoreBarangayRequest extends Request
{
    public function authorize()
    {
        return Auth::user()->account->account_type == 1;
    }

    public function rules()
    {
        return [
            'province_id' => 'required|exists:provinces,prov_code',
            'city_id' => 'required|exists:cities,city_code',
            'barangay_id' => 'required|exists:barangays,id',
        ];
    }

    public function messages()
    {
        return [
            'province_id.required' => 'Province is required.',
            'province_id.exists' => 'Selected province does not exists.',
            'city_id.required' => 'City is required.',
            'city_id.exists' => 'Selected city does not exists.',
            'barangay_id.required' => 'Barangay is required.',
            'barangay_id.exists' => 'Selected barangay does not exists.',
        ];
    }
}
