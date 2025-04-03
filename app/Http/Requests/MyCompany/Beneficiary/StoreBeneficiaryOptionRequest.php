<?php

namespace App\Http\Requests\MyCompany\Beneficiary;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Factory as ValidationFactory;

class StoreBeneficiaryOptionRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function __construct(ValidationFactory $validationFactory)
    {
        $validationFactory->extend('unique_beneficiary', function($attribute, $value, $parameters, $validator)
        {
            $company = Auth::user()->company();

            $data = $validator->getData();

            $model = new \App\Models\Beneficiary;

            $results = $model->where('first_name', $data['first_name'])
                            ->where('middle_name', $data['middle_name'])
                            ->where('last_name', $data['last_name'])
                            ->where('date_of_birth', $data['date_of_birth'])
                            ->where('company_id', $company->id)
                            ->get();

            return !($results->count());
        });

        $validationFactory->extend('valid_number', function($attribute, $value, $parameters, $validator)
		{
			$data = $validator->getData();

			return ($data['mobile_no'][0] == '0' && $data['mobile_no'][1] == '9' && strlen($data['mobile_no']) == 11);
		});
    }

    public function rules()
    {
        return [
            'beneficiary_validation' => 'unique_beneficiary',

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
        ];
    }

    public function messages()
    {
        return [
            'beneficiary_validation.unique_beneficiary' => 'Beneficiary is already registered.',
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

            'date_of_birth.required' => 'Date of birth is required.',
            'date_of_birth.date' => 'Date of birth must be a valid date (2020-01-01)',
            'date_of_birth.date_format' => 'Date of birth must ba valid date (2020-01-01)',
        
        ];
    }
}
