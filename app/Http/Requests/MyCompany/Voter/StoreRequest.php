<?php

namespace App\Http\Requests\MyCompany\Voter;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Factory as ValidationFactory;

class StoreRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function __construct(ValidationFactory $validationFactory)
    {
        $validationFactory->extend('unique_voter', function($attribute, $value, $parameters, $validator)
        {
            $company = Auth::user()->company();

            $data = $validator->getData();

            $model = new \App\Models\Voter;

            $results = $model->where('first_name', $data['first_name'])
                            ->where('middle_name', $data['middle_name'])
                            ->where('last_name', $data['last_name'])
                            ->where('date_of_birth', $data['date_of_birth'])
                            ->where('company_id', $company->id)
                            ->get();

            return !($results->count());
        });
    }

    public function rules()
    {
        return [
            'voter_validation' => 'unique_voter',

            'province_id' => 'required|exists:provinces,prov_code',
            'city_id' => 'required|exists:cities,city_code',
            'barangay_id' => 'required|exists:barangays,id',
            'house_no' => 'nullable',
            
            'first_name' => 'required',
            'middle_name' => 'nullable',
            'last_name' => 'required',
            'gender' => 'required|in:1,2',
            'date_of_birth' => 'required|date|date_format:Y-m-d|before:today',
            
            'precinct_no' => 'nullable',
            'application_no' => 'nullable',
            'application_date' => 'nullable|date|date_format:Y-m-d',
            'application_type' => 'nullable'
        ];
    }

    public function messages()
    {
        return [
            'voter_validation.unique_voter' => 'Voter is already registered.',
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
            'date_of_birth.required' => 'Date of birth is required.',
            'date_of_birth.date' => 'Date of birth must be a valid date (2020-01-01)',
            'date_of_birth.date_format' => 'Date of birth must ba valid date (2020-01-01)',
            
            'precinct_no.required' => 'Precinct no. is required.',
            'application_no.required' => 'Application no. is required.',
            'application_date.required' => 'Application date is required.',
            'application_date.date' => 'Application date accepts date value only.',
            'application_date.date_format' => 'Application date accepts date format only.',
        ];
    }
}
