<?php

namespace App\Http\Requests\MyCompany\DocumentTemplate;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class UpdateRequest extends Request
{
    public function authorize()
    {
        return Auth::user()->account->account_type == 2;
    }

    public function rules()
    {
        return [
            'name' => 'required',
            'description' => 'nullable',
            'view.index' => 'required',
            'view.header' => 'required',
            'view.sidebar' => 'required',
            'view.content' => 'required',
            'view.footer' => 'required',
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
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Name of document is required.',
            'name.unique_name' => 'Name of document already exists.',
            'view.index.required' => 'View of document is required.',
            'view.header.required' => 'View of document is required.',
            'view.sidebar.required' => 'View of document is required.',
            'view.content.required' => 'View of document is required.',
            'view.footer.required' => 'View of document is required.',
            'content.title.required' => 'Title is required.',
            'content.salutation.required' => 'Salutation is required.',
            'content.body.required' => 'Body is required.',
            'options.with_issuance_date.required' => 'Document setting is required.',
            'options.with_issuance_date.in' => 'Document setting accepts valid value only.',
            'options.with_expiration_date.required' => 'Document setting is required.',
            'options.with_expiration_date.in' => 'Document setting accepts valid value only.',
            'options.with_applicant_photo.required' => 'Applicant setting is required.',
            'options.with_applicant_photo.in' => 'Applicant setting accepts valid value only.',
            'options.with_applicant_signature.required' => 'Applicant signature is required.',
            'options.with_applicant_signature.in' => 'Applicant signature accepts valid value only.',
            'options.with_left_thumbmark.required' => 'Applicant setting is required.',
            'options.with_left_thumbmark.in' => 'Applicant setting accepts valid value only.',
            'options.with_right_thumbmark.required' => 'Applicant setting is required.',
            'options.with_right_thumbmark.in' => 'Applicant setting accepts valid value only.',
            'options.with_left_approval.required' => 'Attested by approval is required.',
            'options.with_left_approval.in' => 'Attested by approval accepts valid value only.',
            'options.with_right_approval.required' => 'Approved by approval is required.',
            'options.with_right_approval.in' => 'Approved by approval accepts valid value only.',
        ];
    }
}
