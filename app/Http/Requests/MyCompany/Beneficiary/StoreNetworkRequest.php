<?php

namespace App\Http\Requests\MyCompany\Beneficiary;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class StoreNetworkRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'beneficiaryCode' => 'required|exists:slugs,code',
        ];
    }

    public function messages()
    {
        return [
            'beneficiaryCode.required' => 'Beneficiary is required.',
            'relativeCode.exists' => 'Selected beneficiary does not exists.',
        ];
    }
}
