<?php

namespace App\Http\Requests\MyCompany\Beneficiary;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class UpdateAssistanceRequest extends Request
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
            'assistance_date' => 'required|date|date_format:Y-m-d',
            'assistance_type' => 'required',
            'is_assisted' => 'required|in:1,0',
            'assisted_date' => 'required_if:is_assisted,1|nullable|date|date_format:Y-m-d',
            'assisted_by' => 'required_if:is_assisted,1',
            'remarks' => 'nullable',
        ];
    }

    public function messages()
    {
        return [
            'assistance_date.required' => 'Assistance date is required.',
            'assistance_date.date' => 'Assistance date accepts valid date format only (2020-01-01).',
            'assistance_date.date_format' => 'Assistance date accepts valid date format only (2020-01-01).',
            'assistance_type.required' => 'Type of assistance is required.',
            'remarks.required' => 'Remarks is required.',

            'is_assisted.required' => 'Checkbox is required.',
            'is_assisted.in' => 'Checkbox accepts valid value only.',
            'assisted_date.required_if' => 'Assisted date is required.',
            'assisted_date.date' => 'Assisted date accepts valid format (2023-01-01).',
            'assisted_date.date_format' => 'Assisted date accepts valid format (2023-01-01).',
            'assisted_by.required_if' => 'Assisted by is required.'
        ];
    }
}
