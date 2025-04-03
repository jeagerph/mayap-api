<?php

namespace App\Http\Requests\MyCompany\Beneficiary;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Factory as ValidationFactory;

class ArrangeRelativesRequest extends Request
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

            foreach($data['relatives'] as $row):

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
            'relatives' => 'required|array|duplicate_order_no',
            'relatives.*.id' => 'required_if:relatives,true|exists:beneficiary_relatives,id',
            'relatives.*.order_no' => 'required_if:relatives,true'
        ];
    }

    public function messages()
    {
        return [
            'relatives.required' => 'Relatives is required.',
            'relatives.array' => 'Relatives accepts array value only.',
            'relatives.duplicate_order_no' => 'Order number has duplicate.',
            'relatives.*.id.required_if' => 'Relative is required.',
            'relatives.*.id.exists' => 'Relative does not exists.',
            'relatives.*.order_no.required_if' => 'Order no is required.'


        ];
    }
}
