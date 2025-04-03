<?php

namespace App\Http\Requests\MyCompany\Classification;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class UpdateClassificationRequest extends Request
{
    public function rules()
    {
        return [
            'name' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Name of classification is required.',
            'name.unique_name' => 'Name of classification already exists.',

        ];
    }
}
