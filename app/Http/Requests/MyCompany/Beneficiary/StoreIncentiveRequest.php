<?php

namespace App\Http\Requests\MyCompany\Beneficiary;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class StoreIncentiveRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'points' => 'required|numeric|min:1',
            'mode' => 'required|in:1,2',
            'remarks' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'points.required' => 'Points is required.',
            'points.numeric' => 'Points accepts numeric value only.',
            'points.min' => 'Points accepts minimum of 1 value only.',
            'mode.required' => 'Mode is required.',
            'model.in' => 'Mode accepts valid value only.',
            'remarks.required' => 'Remarks is required.',
        ];
    }
}
