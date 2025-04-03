<?php

namespace App\Http\Requests\PublicRequest;

use App\Http\Requests\Request;

class ContactRequest extends Request
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
            'email' => 'required|string',
            'fullname' => 'required|string',
            'contact' => 'required|string',
            'message' => 'required|string'
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
            'required' => 'The :attribute is required',
            'after' => 'The :attribute is invalid. Must be latest',
            'max' => 'The :attribute accepts 11-digit number',
            'min' => 'The :attribute accepts 11-digit number',
            'unique' => 'The :attribute is already taken',
            'numeric' => 'The :attribute accepts only numeric values',
            'in' => 'The :attribute accepts only 1 or 0 values',
            'exists' => 'The :attribute accepts existing values',
            'mimes' => 'The :attribute accepts jpeg and png format only'
        ];
	}
}
