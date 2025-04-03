<?php

namespace App\Http\Requests\MyCompany\Assignatory;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Factory as ValidationFactory;

class ArrangeRequest extends Request
{
    public function authorize()
    {
        return Auth::user()->account->account_type == 2;
    }

    public function __construct(ValidationFactory $validationFactory)
    {
        $validationFactory->extend('duplicate_order_no', function($attribute, $value, $parameters, $validator)
        {
            $data = $validator->getData();
            $orderNos = [];
            $hasDuplicate = false;

            foreach($data['assignatories'] as $row):

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
            'assignatories' => 'required|array|duplicate_order_no',
            'assignatories.*.id' => 'required_if:assignatories,true|exists:company_assignatories,id',
            'assignatories.*.order_no' => 'required_if:assignatories,true'
        ];
    }

    public function messages()
    {
        return [
            'assignatories.required' => 'Assignatories is required.',
            'assignatories.array' => 'Assignatories accepts array value only.',
            'assignatories.duplicate_order_no' => 'Order number has duplicate.',
            'assignatories.*.id.required_if' => 'Assignatory is required.',
            'assignatories.*.id.exists' => 'Assignatory does not exists.',
            'assignatories.*.order_no.required_if' => 'Order no is required.'


        ];
    }
}
