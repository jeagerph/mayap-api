<?php

namespace App\Http\Requests\MyCompany\Member;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class StoreAttachmentRequest extends Request
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
            'file' => 'required|max:10000',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Name is required.',
            'file.required' => 'File is required.',
            'file.max' => 'File accepts maximum of 10MB only.'
        ];
    }
}
