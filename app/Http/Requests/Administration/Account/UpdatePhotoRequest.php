<?php

namespace App\Http\Requests\Administration\Account;

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
