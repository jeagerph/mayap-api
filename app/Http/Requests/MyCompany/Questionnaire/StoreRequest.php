<?php

namespace App\Http\Requests\MyCompany\Questionnaire;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class StoreRequest extends Request
{
    public function authorize()
    {
        return Auth::user()->account->account_type == 2;
    }

    public function rules()
    {
        return [
            'question' => 'required',
            'description' => 'nullable',
        ];
    }

    public function messages()
    {
        return [
            'question.required' => 'Question is required.',

        ];
    }
}
