<?php

namespace App\Http\Requests\MyCompany\Beneficiary;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Factory as ValidationFactory;

class StoreCallRequest extends Request
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

			return ($data['mobile_number'][0] == '0' && $data['mobile_number'][1] == '9' && strlen($data['mobile_number']) == 11);
		});
    }

    public function rules()
    {
        return [
            'transaction_date' => 'required|date|date_format:Y-m-d',
            'mobile_number' => 'required|numeric|valid_number',

            'call_date' => 'required|date|date_format:Y-m-d',
        ];
    }

    public function messages()
    {
        return [
            'transaction_date.required' => 'Date of call is required.',
            'transaction_date.date' => 'Date of call must be a valid date (2020-01-01)',
            'transaction_date.date_format' => 'Date of call must be a valid date (2020-01-01)',
            'mobile_number.required' => 'Mobile number is required.',
            'mobile_number.numeric' => 'Mobile number accepts numeric value only (09227503074).',
            'mobile_number.valid_number' => 'Mobile number accepts valid number only  (09227503074).',

            'call_date.required' => 'Date of call is required.',
            'call_date.date' => 'Date of call must be a valid date (2020-01-01)',
            'call_date.date_format' => 'Date of call must be a valid date (2020-01-01)',
        ];
    }
}
