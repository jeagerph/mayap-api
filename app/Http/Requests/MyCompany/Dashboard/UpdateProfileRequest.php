<?php

namespace App\Http\Requests\MyCompany\Dashboard;

use App\Http\Requests\Request;

class UpdateProfileRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required',
            'address' => 'required',
            'contact_no' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Name is required.',
            'address.required' => 'Address is required.',
            'contact_no.required' => 'Contact no. is required.',
        ];
    }
}
