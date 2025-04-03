<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Request;

class ValidateOtpRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'otp' => 'required|numeric',
            'token' => 'required|exists:user_pins,token',
        ];
    }

    public function messages()
    {
        return [
            'otp.required' => 'OTP is required.',
            'otp.numeric' => 'OTP accepts numeric value only.',
            'token.required' => 'Token is required.',
            'token.exists' => 'Invalid validation token',
        ];
    }
}
