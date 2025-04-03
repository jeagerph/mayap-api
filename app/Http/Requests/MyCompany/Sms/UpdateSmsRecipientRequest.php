<?php

namespace App\Http\Requests\MyCompany;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Factory as ValidationFactory;

class UpdateSmsRecipientRequest extends Request
{
    public function authorize()
    {
        return Auth::user()->account->account_type_id == 2;
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
            'mobile_number' => 'required|numeric|valid_number',
        ];
    }

    public function messages()
    {
        return [
            'mobile_number.required' => 'Mobile number is required.',
            'mobile_number.numeric' => 'Mobile number accepts numeric value only (09227503074).',
            'mobile_number.valid_number' => 'Mobile number accepts valid number only  (09227503074).',
        ];
    }
}
