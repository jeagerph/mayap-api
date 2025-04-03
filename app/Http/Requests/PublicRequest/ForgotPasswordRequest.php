<?php

namespace App\Http\Requests\PublicRequest;

use App\Http\Requests\Request;

class ForgotPasswordRequest extends Request
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
            'mobile_number' => 'required|exists:accounts,mobile_number',
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
            'mobile_number.required' => 'Mobile number is required.',
            'mobile_number.exists' => 'Mobile number does not exists in our database.'
        ];
	}
}
