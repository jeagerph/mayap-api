<?php

namespace App\Http\Requests\MyCompany\Beneficiary;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class StoreDocumentRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'document_date' => 'required|date|date_format:Y-m-d',
            'name' => 'required',
            'description' => 'nullable',
            
            'view.header' => 'required',
            'view.sidebar' => 'required',
            'view.content' => 'required',
            'view.footer' => 'required',

            'content.title' => 'required',
            'content.salutation' => 'nullable',
            'content.body' => 'required',

            'content.issuance_date' => 'required_if:options.with_issuance_date,1',
            'content.expiration_date' => 'required_if:options.with_expiration_date,1',

            'options.with_qr_code' => 'required|in:0,1',
            'options.with_document_no' => 'required|in:0,1',
            'options.with_issuance_date' => 'required|in:0,1',
            'options.with_expiration_date' => 'required|in:0,1',

            'options.with_applicant_photo' => 'required|in:0,1',
            'options.with_applicant_signature' => 'required|in:0,1',

            'options.with_left_approval' => 'required|in:0,1',
            'options.with_right_approval' => 'required|in:0,1',

            'inputs' => 'nullable|array',
            'tables' => 'nullable|array',

            'approvals.left_approval.label' => 'required_if:options.with_left_approval,1',
            'approvals.left_approval.name' => 'required_if:options.with_left_approval,1',
            'approvals.left_approval.position' => 'required_if:options.with_left_approval,1',
            'approvals.right_approval.label' => 'required_if:options.with_right_approval,1',
            'approvals.right_approval.name' => 'required_if:options.with_right_approval,1',
            'approvals.right_approval.position' => 'required_if:options.with_right_approval,1',
        ];
    }

    public function messages()
    {
        return [
            'document_date.required' => 'Date is required.',
            'document_date.date' => 'Date accepts valid date format only (2020-01-01).',
            'document_date.date_format' => 'Date accepts valid date format only (2020-01-01).',
        ];
    }
}
