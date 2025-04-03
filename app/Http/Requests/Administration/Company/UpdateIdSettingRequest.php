<?php

namespace App\Http\Requests\Administration\Company;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class UpdateIdSettingRequest extends Request
{
    public function authorize()
    {
        return Auth::user()->account->account_type == 1;
    }

    public function rules()
    {
        return [
            'name' => 'required',
            'address' => 'nullable',
            'contact_no' => 'nullable',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Name is required.',
            'name.unique' => 'Name already exists.',
        ];
    }
}
