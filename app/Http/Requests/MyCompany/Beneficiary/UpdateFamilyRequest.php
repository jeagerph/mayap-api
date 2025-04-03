<?php

namespace App\Http\Requests\MyCompany\Beneficiary;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class UpdateFamilyRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'full_name' => 'required',
            'gender' => 'required|in:1,2',
            'date_of_birth' => 'required|date|date_format:Y-m-d|before:today',
            'address' => 'required',
            'education' => 'nullable',
            'occupation' => 'nullable',
            'relationship' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'full_name.required' => 'Full name is required.',
            'gender.required' => 'Gender is required.',
            'date_of_birth.required' => 'Date of birth is required.',
            'date_of_birth.date' => 'Date of birth must be a valid date (2020-01-01)',
            'date_of_birth.date_format' => 'Date of birth must ba valid date (2020-01-01)',
            'address.required' => 'Address is required.',
            
            'relationship.required' => 'Relationship is required.'
        ];
    }
}
