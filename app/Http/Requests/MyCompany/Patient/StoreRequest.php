<?php

namespace App\Http\Requests\MyCompany\Patient;

use App\Http\Requests\Request;

class StoreRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'beneficiaryCode' => 'required|exists:slugs,code',
            'patient_date' => 'required|date|date_format:Y-m-d',
            
            'first_name' => 'required',
            'middle_name' => 'nullable',
            'last_name' => 'required',
            'relation_to_patient' => 'required',
            
            'remarks' => 'nullable',
        ];
    }

    public function messages()
    {
        return [
            'beneficiaryCode.required' => 'Beneficiary is required.',
            'beneficiaryCode.exists' => 'Selected beneficiary does not exists.',
            
            'patient_date.required' => 'Date is required.',
            'patient_date.date' => 'Date accepts valid date format only (2020-01-01).',
            'patient_date.date_format' => 'Date accepts valid date format only (2020-01-01).',

            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',


            'remarks.required' => 'Remarks is required.',
        ];
    }
}
