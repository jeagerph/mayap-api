<?php

namespace App\Http\Requests\MyCompany\DocumentTemplate;

use App\Http\Requests\Request;

class UpdatePhotoRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'photo' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'photo.required' => 'Photo is required.',
        ];
    }
}
