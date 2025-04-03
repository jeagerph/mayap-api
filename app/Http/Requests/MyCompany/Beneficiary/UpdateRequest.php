<?php

namespace App\Http\Requests\MyCompany\Beneficiary;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Factory as ValidationFactory;

class UpdateRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function __construct(ValidationFactory $validationFactory)
    {
        $validationFactory->extend('valid_number', function($attribute, $value, $parameters, $validator)
		{
			$data = $validator->getData();

			return ($data['mobile_no'][0] == '0' && $data['mobile_no'][1] == '9' && strlen($data['mobile_no']) == 11);
		});
    }

    public function rules()
    {
        return [
            'province_id' => 'required|exists:provinces,prov_code',
            'city_id' => 'required|exists:cities,city_code',
            'barangay_id' => 'required|exists:barangays,id',
            'house_no' => 'nullable',
            
            'first_name' => 'required',
            'middle_name' => 'nullable',
            'last_name' => 'required',
            'gender' => 'required|in:1,2',
            'mobile_no' => 'required|valid_number',
            'email' => 'nullable|email',

            'date_of_birth' => 'required|date|date_format:Y-m-d|before:today',
            'place_of_birth' => 'nullable',
            'civil_status' => 'nullable',
            'citizenship' => 'nullable',
            'religion' => 'nullable',

            'educational_attainment' => 'nullable',
            'occupation' => 'nullable',
            'monthly_income' => 'nullable',
            'classification' => 'nullable',

            'is_household' => 'required|in:0,1',
            'is_priority' => 'required|in:0,1',

            'emergency_contact_name' => 'nullable',
            'emergency_contact_no' => 'nullable',
            'emergency_contact_address' => 'nullable',

            'remarks' => 'nullable',
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
            
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'gender.required' => 'Gender is required.',
            'gender.in' => 'Gender accepts valid value only.',

            'mobile_no.required' => 'Mobie no. is required.',
            'mobile_no.valid_number' => 'Mobie no. must be a valid number (09XXXXXXXXX).',
            'email.email' => 'E-mail accepts valid format only (username@domain.com).',
            
            'place_of_birth.required' => 'Place of birth is required.',
            'date_of_birth.required' => 'Date of birth is required.',
            'date_of_birth.date' => 'Date of birth must be a valid date (2020-01-01)',
            'date_of_birth.date_format' => 'Date of birth must ba valid date (2020-01-01)',
            'civil_status.required' => 'Civil status is required.',
            'civil_status.in' => 'Civil status accepts valid value only.',
            
            'emergency_contact_name.required' => 'Full name is required.',
            'emergency_contact_no.required' => 'Contact number is required.', 

            'photo.required' => 'Photo is required.',

            'is_household.required' => 'Household is required.',
            'is_household.in' => 'Household accepts valid value only.',

            'is_priority.required' => 'Priority is required.',
            'is_priority.in' => 'Priority accepts valid value only.'
        ];
    }
}
