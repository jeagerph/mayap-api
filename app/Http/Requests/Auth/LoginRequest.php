<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Request;

class LoginRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'username' => 'required|exists:users,username',
            'password' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'username.required' => 'Username is required.',
            'username.exists' => 'Username does not exists.',
            'password.required' => 'Password is required.'
        ];
    }
}
