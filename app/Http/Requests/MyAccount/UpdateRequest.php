<?php

namespace App\Http\Requests\MyAccount;

use App\Http\Requests\Request;

class UpdateRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'username' => 'required',
            'full_name' => 'required',
            'email' => 'nullable|email',
            'mobile_number' => 'nullable|min:11',
        ];
    }

    public function messages()
    {
        return [
            'username.required' => 'Username is required.',
            'full_name.required' => 'Full name is required.',
            'email.email' => 'E-mail accepts valid value (username@domain.com).',
            'mobile_number.min' => 'Mobile number accepts valid values only.'
        ];
    }
}
