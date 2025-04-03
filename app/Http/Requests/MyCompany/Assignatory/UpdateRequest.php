<?php

namespace App\Http\Requests\MyCompany\Assignatory;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class UpdateRequest extends Request
{
    public function authorize()
    {
        return Auth::user()->account->account_type == 2;
    }

    public function rules()
    {
        return [
            'name' => 'required',
            'position' => 'required',
            'photo' => 'nullable',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Name is required.',
            'position.required' => 'Position is required.',

        ];
    }
}
