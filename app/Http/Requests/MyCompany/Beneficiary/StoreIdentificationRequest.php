<?php

namespace App\Http\Requests\MyCompany\Beneficiary;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class StoreIdentificationRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'identification_date' => 'required|date|date_format:Y-m-d',
            'name' => 'required',
            'description' => 'nullable',
            'view.index' => 'required',
            'view.header' => 'required',
            'view.front' => 'required',
            'view.back' => 'required',
            'content.title' => 'required',
            'content.salutation' => 'nullable',
            'content.body' => 'nullable',
            'options.with_issuance_date' => 'required|in:0,1',
            'options.with_expiration_date' => 'required|in:0,1',
            'options.with_applicant_photo' => 'required|in:0,1',
            'options.with_applicant_signature' => 'required|in:0,1',
            'options.with_left_approval' => 'required|in:0,1',
            'options.with_right_approval' => 'required|in:0,1',
            'approvals.left_approval.label' => 'nullable',
            'approvals.left_approval.name' => 'nullable',
            'approvals.left_approval.position' => 'nullable',
            'approvals.right_approval.label' => 'nullable',
            'approvals.right_approval.name' => 'nullable',
            'approvals.right_approval.position' => 'nullable',
            'left_signature' => 'nullable',
            'right_signature' => 'nullable',
        ];
    }

    public function messages()
    {
        return [
            'identification_date.required' => 'Date is required.',
            'identification_date.date' => 'Date accepts valid date format only (2020-01-01).',
            'identification_date.date_format' => 'Date accepts valid date format only (2020-01-01).',
        ];
    }
}
