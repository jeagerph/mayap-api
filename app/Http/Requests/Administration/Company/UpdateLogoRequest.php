<?php

namespace App\Http\Requests\Administration\Company;

use App\Http\Requests\Request;

class UpdateLogoRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'logo' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'logo.required' => 'Logo is required.',
        ];
    }
}
