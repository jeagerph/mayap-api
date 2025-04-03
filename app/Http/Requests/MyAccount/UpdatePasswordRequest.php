<?php

namespace App\Http\Requests\MyAccount;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Factory as ValidationFactory;

class UpdatePasswordRequest extends Request
{
    public function __construct(ValidationFactory $validationFactory)
	{
		$validationFactory->extend('same_password', function($attribute, $value, $parameters, $validator)
		{
			$data = $validator->getData();

			return Hash::check($data['old_password'], Auth::user()->password);
		});
    }
    
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'old_password' => 'required|same_password',
            'password' => 'required|confirmed|min:6',
            'password_confirmation' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'old_password.same_password' => 'Old password does not match.',
            'password.confirmed' => 'Passwords does not match.',
            'password.min' => 'Password accepts minimum of 6 characters.'
        ];
    }
}
