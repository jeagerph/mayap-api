<?php

namespace App\Models;

use App\Models\Model;

use App\Observers\CompanySmsRecipientObserver as Observer;

class CompanySmsRecipient extends Model
{
    use Observer;
    
    public $searchFields = [];

    public $filterFields = [
        'smsTransactionCode' => 'slug:company_sms_transaction_id',
        'status' => ':status',
    ];
    
	public $sortFields = [
        'status' => ':status',
        'created' => ':created_at',
    ];

    public $statuses = [
        1 => 'PENDING',
        2 => 'SENT',
        3 => 'FAILED'
    ];

    public function smsTransaction()
    {
        return $this->belongsTo('App\Models\CompanySmsTransaction', 'company_sms_transaction_id');
    }

    public function toArray()
    {
        $arr = [
            'mobile_number' => $this->mobile_number,
            'message' => $this->message,
            'status' => [
                'id' => $this->status,
                'name' => $this->statuses[$this->status]
            ],
            'sent_at' => $this->sent_at,
            'failure_message' => $this->failure_message,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at
                ? $this->updated_at->toDateTimeString()
                : $this->created_at->toDateTimeString()
        ];

        if (request()->has('admin-sms-transaction-recipients-related')):
            $arr['id'] = $this->id;
        endif;

        if (request()->has('my-company-sms-transaction-recipients-related')):
            $arr['id'] = $this->id;
        endif;

        return $arr;
    }

    public function toArrayAdminSmsTransactionRecipientsRelated()
    {
        return [
            'id' => $this->id,
            'mobile_number' => $this->mobile_number,
            'message' => $this->message,
            'status' => [
                'id' => $this->status,
                'name' => $this->statuses[$this->status]
            ],
            'sent_at' => $this->sent_at,
            'failure_message' => $this->failure_message,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at
                ? $this->updated_at->toDateTimeString()
                : $this->created_at->toDateTimeString()
        ];
    }

    public function toArrayMyCompanySmsTransactionRecipientsRelated()
    {
        return [
            'id' => $this->id,
            'mobile_number' => $this->mobile_number,
            'message' => $this->message,
            'status' => [
                'id' => $this->status,
                'name' => $this->statuses[$this->status]
            ],
            'sent_at' => $this->sent_at,
            'failure_message' => $this->failure_message,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at
                ? $this->updated_at->toDateTimeString()
                : $this->created_at->toDateTimeString()
        ];
    }
}
