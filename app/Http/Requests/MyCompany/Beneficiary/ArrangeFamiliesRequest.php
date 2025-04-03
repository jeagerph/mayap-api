<?php

namespace App\Http\Requests\MyCompany\Beneficiary;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Factory as ValidationFactory;

class ArrangeFamiliesRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function __construct(ValidationFactory $validationFactory)
    {
        $validationFactory->extend('duplicate_order_no', function($attribute, $value, $parameters, $validator)
        {
            $data = $validator->getData();
            $orderNos = [];
            $hasDuplicate = false;

            foreach($data['families'] as $row):

                if( !in_array($row['order_no'], $orderNos) ):

                    $orderNos[] = $row['order_no'];
                
                else:
                    $hasDuplicate = true;
                    break;
                endif;

            endforeach;

            return !$hasDuplicate;
        });
    }

    public function rules()
    {
        return [
            'families' => 'required|array|duplicate_order_no',
            'families.*.id' => 'required_if:families,true|exists:beneficiary_families,id',
            'families.*.order_no' => 'required_if:families,true'
        ];
    }

    public function messages()
    {
        return [
            'families.required' => 'Families is required.',
            'families.array' => 'Families accepts array value only.',
            'families.duplicate_order_no' => 'Order number has duplicate.',
            'families.*.id.required_if' => 'Family is required.',
            'families.*.id.exists' => 'Family does not exists.',
            'families.*.order_no.required_if' => 'Order no is required.'


        ];
    }
}
