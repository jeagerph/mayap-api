<?php

namespace App\Models;

use App\Models\Model;

class BeneficiaryMessage extends Model
{
    public $searchFields = [];

    public $filterFields = [
        'beneficiaryCode' => 'slug:beneficiary_id',
    ];

    public $sortFields = [
        'messageDate' => ':message_date',
        'created' => ':created_at'
    ];

    public $messageTypeOptions = [
        1 => 'REGULAR',
        2 => 'BRANDING'
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company');
    }

    public function beneficiary()
    {
        return $this->belongsTo('App\Models\Beneficiary');
    }

    public function createdBy()
    {
        return $this->belongsTo('App\Models\User', 'created_by');
    }

    public function toArray()
    {
        $arr = [
            'message_date' => $this->message_date,
            'message_type' => [
                'id' => $this->message_type,
                'name' => $this->messageTypeOptions[$this->message_type]
            ],
            'message_sender_name' => $this->message_sender_name,
            'mobile_number' => $this->mobile_number,
            'message' => $this->message,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at
                ? $this->updated_at->toDateTimeString()
                : $this->created_at->toDateTimeString()
        ];

        if (request()->has('beneficiary-messages-related')):

            $arr['id'] = $this->id;
            $arr['created_by'] = $this->createdBy->account;

        endif;

        return $arr;
    }
}
