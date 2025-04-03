<?php

namespace App\Http\Requests\Administration\Company;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class UpdateSmsCreditRequest extends Request
{
    public function authorize()
    {
        return Auth::user()->account->account_type == 1;
    }

    public function rules()
    {
        return [
            'credit_date' => 'required|date|date_format:Y-m-d',
            'credit_mode' => 'required|in:1,2',
            'amount' => 'required|numeric|min:1',
            'remarks' => 'nullable'
        ];
    }

    public function messages()
    {
        return [
            'credit_date.required' => 'Date of credit is required.',
            'credit_date.date' => 'Date of credit accepts valid format only (2022-01-01).',
            'credit_date.date_format' => 'Date of credit accepts valid format only (2022-01-01).',
            'amount.required' => 'Amount is required.',
            'amount.numeric' => 'Amount accepts numeric value only.',
            'amount.min' => 'Amount accepts minimum of 1 value only.',
            'credit_mode.required' => 'Mode is required.',
            'credit_mode.in' => 'Mode accepts valid value only.',
        ];
    }
}
