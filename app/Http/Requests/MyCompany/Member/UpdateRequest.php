<?php

namespace App\Http\Requests\MyCompany\Member;

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

			return ($data['contact_no'][0] == '0' && $data['contact_no'][1] == '9' && strlen($data['contact_no']) == 11);
		});
    }

    public function rules()
    {
        return [
            'province_id' => 'required|exists:provinces,prov_code',
            'city_id' => 'required|exists:cities,city_code',
            'barangay_id' => 'required|exists:barangays,id',
            'house_no' => 'nullable',

            'company_classification_id' => 'required|exists:company_classifications,id',

            'first_name' => 'required',
            'middle_name' => 'nullable',
            'last_name' => 'nullable',
            'gender' => 'required|in:1,2',
            'date_of_birth' => 'required|date|date_format:Y-m-d|before:today',
            'address' => 'nullable',
            'contact_no' => 'required|valid_number',
            'email' => 'nullable|email',
            'place_of_birth' => 'nullable',

            'civil_status' => 'nullable|in:1,2,3,4,5,6',
            'citizenship' => 'nullable',
            'religion' => 'nullable',
            'eligibility' => 'nullable',
            'blood_type' => 'nullable',
            'health_history' => 'nullable',
            'skills' => 'nullable',
            'pending' => 'nullable',

            'emergency_contact_name' => 'nullable',
            'emergency_contact_no' => 'nullable',
            'emergency_contact_address' => 'nullable',

            'precinct_no' => 'nullable',
            'is_household' => 'required|in:0,1',
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
            'barangay_id.unique_barangay' => 'Selected barangay is already registered.',

            'company_classification_id.required' => 'Classification is required.',
            'company_classification_id.exists' => 'Selected classification does not exists.',

            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'gender.required' => 'Gender is required.',
            'gender.in' => 'Gender accepts valid value only.',
            'address.required' => 'Address is required.',
            'contact_no.required' => 'Contact number is required.',
            'contact_no.valid_number' => 'Contact number must be a valid number (09227503074).',
            'email.email' => 'E-mail accepts valid format only (username@domain.com).',
            'place_of_birth.required' => 'Place of birth is required.',
            'date_of_birth.required' => 'Date of birth is required.',
            'date_of_birth.date' => 'Date of birth must be a valid date (2020-01-01)',
            'date_of_birth.date_format' => 'Date of birth must ba valid date (2020-01-01)',

            'civil_status.in' => 'Civil status accepts valid value only.',
            
            'emergency_contact_name.required' => 'Full name is required.',
            'emergency_contact_no.required' => 'Contact number is required.', 

            'is_household.required' => 'Household is required.',
            'is_household.in' => 'Household accepts valid value only.'
        ];
    }
}
