<?php

namespace App\Http\Requests\MyCompany\Classification;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Factory as ValidationFactory;

class StoreClassificationRequest extends Request
{
    public function authorize()
    {
        return Auth::user()->account->account_type == 2;
    }

    public function __construct(ValidationFactory $validationFactory)
    {
        $validationFactory->extend('unique_name', function($attribute, $value, $parameters, $validator)
        {
            $data = $validator->getData();

            $company = Auth::user()->company();

            $checking = $company->classifications()->where('name', $data['name'])->first();

            if($checking) return false;

            return true;
        });
    }

    public function rules()
    {
        return [
            'name' => 'required|unique_name',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Name of classification is required.',
            'name.unique_name' => 'Name of classification already exists.',

        ];
    }
}
