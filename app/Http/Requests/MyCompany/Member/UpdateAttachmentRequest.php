<?php

namespace App\Http\Requests\MyCompany\Member;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class UpdateAttachmentRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required',
            'description' => 'nullable',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Name is required.',
        ];
    }
}
