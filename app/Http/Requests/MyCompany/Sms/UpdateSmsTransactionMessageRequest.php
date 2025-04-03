<?php

namespace App\Http\Requests\MyCompany;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class UpdateSmsTransactionMessageRequest extends Request
{
    public function authorize()
    {
        return Auth::user()->account->account_type_id == 2;
    }

    public function rules()
    {
        return [
            'message' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'message.required' => 'Message is required.',
        ];
    }
}
