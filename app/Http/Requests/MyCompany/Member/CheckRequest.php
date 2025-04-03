<?php

namespace App\Http\Requests\MyCompany\Member;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class CheckRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'first_name' => 'required',
            'middle_name' => 'nullable',
            'last_name' => 'required',
            'gender' => 'required|in:1,2',
            'date_of_birth' => 'required|date|date_format:Y-m-d|before:today'
        ];
    }

    public function messages()
    {
        return [
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'gender.required' => 'Gender is required.',
            'gender.in' => 'Gender accepts valid value only.',
        ];
    }
}
