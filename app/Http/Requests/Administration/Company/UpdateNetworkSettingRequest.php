<?php

namespace App\Http\Requests\Administration\Company;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class UpdateNetworkSettingRequest extends Request
{
    public function authorize()
    {
        return Auth::user()->account->account_type == 1;
    }

    public function rules()
    {
        return [
            'master_degree_enabled' => 'required|in:0,1',
            'master_degree_points' => 'required|numeric|min:0',
            'first_degree_enabled' => 'required|in:0,1',
            'first_degree_points' => 'required|numeric|min:0',
            'second_degree_enabled' => 'required|in:0,1',
            'second_degree_points' => 'required|numeric|min:0',
            'third_degree_enabled' => 'required|in:0,1',
            'third_degree_points' => 'required|numeric|min:0',
            'fourth_degree_enabled' => 'required|in:0,1',
            'fourth_degree_points' => 'required|numeric|min:0',
            'fifth_degree_enabled' => 'required|in:0,1',
            'fifth_degree_points' => 'required|numeric|min:0',
        ];
    }

    public function messages()
    {
        return [
            'master_degree_enabled.required' => 'Master degree activation is required.',
            'master_degree_enabled.in' => 'Master degree activation accepts valid value only.',
            'first_degree_enabled.required' => 'First degree activation is required.',
            'first_degree_enabled.in' => 'First degree activation accepts valid value only.',
            'second_degree_enabled.required' => 'Second degree activation is required.',
            'second_degree_enabled.in' => 'Second degree activation accepts valid value only.',
            'third_degree_enabled.required' => 'Third degree activation is required.',
            'third_degree_enabled.in' => 'Third degree activation accepts valid value only.',
            'fourth_degree_enabled.required' => 'Fourth degree activation is required.',
            'fourth_degree_enabled.in' => 'Fourth degree activation accepts valid value only.',
            'fifth_degree_enabled.required' => 'Fifth degree activation is required.',
            'fifth_degree_enabled.in' => 'Fifth degree activation accepts valid value only.',
        ];
    }
}
