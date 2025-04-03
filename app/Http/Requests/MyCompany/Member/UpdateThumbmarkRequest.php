<?php

namespace App\Http\Requests\MyCompany\Member;

use App\Http\Requests\Request;

class UpdateThumbmarkRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'thumbmark' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'thumbmark.required' => 'Thumbmark is required.',
        ];
    }
}
