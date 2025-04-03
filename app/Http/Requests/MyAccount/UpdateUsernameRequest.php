<?php

namespace App\Http\Requests\MyAccount;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class UpdateUsernameRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'username' => 'required|unique:users,username,' . Auth::id()
        ];
    }

    public function messages()
    {
        return [
            'username.required' => 'Username is required.',
            'username.unique' => 'Username already exists.',
        ];
    }
}
