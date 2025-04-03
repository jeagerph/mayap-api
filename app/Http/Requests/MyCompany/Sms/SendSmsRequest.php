<?php

namespace App\Http\Requests\MyCompany\Sms;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Factory as ValidationFactory;

class SendSmsRequest extends Request
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
            'sms_type' => 'required|in:1,2',
            'transaction_date' => 'required|date|date_format:Y-m-d',
            'transaction_type' => 'required|in:1,2',
            'scheduled_date' => 'nullable|date|date_format:Y-m-d',
            'scheduled_time' => 'nullable|date_format:H:i',
            'message' => 'required',
            'mobile_number' => 'required|numeric|valid_number',
        ];
    }

    public function messages()
    {
        return [
            'sms_type.required' => 'Type of SMS is required.',
            'sms_type.in' => 'Selected type accepts valid value only.',
            'transaction_date.required' => 'Date of SMS is required.',
            'transaction_date.date' => 'Date of SMS must be a valid date (2020-01-01)',
            'transaction_date.date_format' => 'Date of SMS must be a valid date (2020-01-01)',
            'scheduled_date.date' => 'Date schedule must be a valid date (2020-01-01)',
            'scheduled_date.date_format' => 'Date schedule must be a valid date (2020-01-01)',
            'scheduled_time.date' => 'Date schedule must be a valid time (08:00:00)',
            'scheduled_time.date_format' => 'Date schedule must be a valid time (08:00:00)',
            'message.required' => 'Message is required.',
            'mobile_number.required' => 'Mobile number is required.',
            'mobile_number.numeric' => 'Mobile number accepts numeric value only (09227503074).',
            'mobile_number.valid_number' => 'Mobile number accepts valid number only  (09227503074).',
        ];
    }
}
