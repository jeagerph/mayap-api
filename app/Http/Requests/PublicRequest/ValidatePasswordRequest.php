<?php

namespace App\Http\Requests\PublicRequest;

use App\Http\Requests\Request;

class ValidatePasswordRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
            'token' => 'required|exists:user_verifications,token',
            'password' => 'required|confirmed|min:6',
        ];
	}
	
	/**
	 * Custom message for validation
	 *
	 * @return array
	 */
	public function messages()
	{
		return [
            'token.required' => 'Token is required.',
            'token.exists' => 'Token is not recognized.',
            'password.required' => 'Please fill out a secure password.',
            'password.confirmed' => 'Password does not match.',
            'password.min' => 'Password accepts at least 6 characters.',
        ];
	}
}
