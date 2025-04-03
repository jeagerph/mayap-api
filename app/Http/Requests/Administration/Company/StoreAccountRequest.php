<?php

namespace App\Http\Requests\Administration\Company;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class StoreAccountRequest extends Request
{
    public function authorize()
    {
        return Auth::user()->account->account_type == 1;
    }

    public function rules()
    {
        return [
            'company_position_id' => 'required|exists:company_positions,id',
            // 'account_type' => 'required|in:1.2',
            'username' => 'required|unique:users,username|min:6',
            'full_name' => 'required',
            'mobile_number' => 'nullable|min:11|max:11',
            'email' => 'nullable|email',
        ];
    }

    public function messages()
    {
        return [
            'company_position_id.required' => 'Position is required.',
            'company_position_id.exists' => 'Selected position does not exists.',
            // 'account_type_id.required' => 'Account type is required.',
            // 'account_type_id.exists' => 'Selected account type does not exists.',
            'username.required' => 'Username is required',
            'username.unique' => 'Username is already taken.',
            'username.min' => 'Username accepts atleast 6 characters.',
            'full_name.required' => 'First name is required.',
            'mobile_number.min' => 'Mobile number accepts valid value only (09123456789).',
            'mobile_number.max' => 'Mobile number accepts valid value only (09123456789).',
        ];
    }
}
