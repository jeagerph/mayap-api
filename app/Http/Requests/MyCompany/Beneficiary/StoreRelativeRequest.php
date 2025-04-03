<?php

namespace App\Http\Requests\MyCompany\Beneficiary;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class StoreRelativeRequest extends Request
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
            'relativeCode' => 'required|exists:slugs,code',
            'relationship' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'relativeCode.required' => 'Relative is required.',
            'relativeCode.exists' => 'Selected relative does not exists.',
            'relationship.required' => 'Relationship is required.'
        ];
    }
}
