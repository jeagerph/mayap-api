<?php

namespace App\Models;

use App\Models\Model;

use App\Observers\CompanySmsTransactionObserver as Observer;

class CompanySmsTransaction extends Model
{
    use Observer;
    
    public $searchFields = [
        'code' => ':code',
    ];

    public $filterFields = [
        'companyCode' => 'slug:company_id',
        'status' => ':status',
        'transactionType' => ':transaction_type',
    ];
    
	public $sortFields = [
        'created' => ':created_at',
        'transactionDate' => ':transaction_date',
        'status' => ':status',
    ];

    public $rangeFields = [
        'created' => ':created_at',
        'transactionDate' => ':transaction_date'
    ];

    public $smsTypes = [
        1 => 'REGULAR SMS',
        2 => 'BRANDING SMS'
    ];

    public $transactionTypes = [
        1 => 'INDIVIDUAL',
        2 => 'GROUP'
    ];

    public $statuses = [
        1 => 'PENDING',
        2 => 'APPROVED',
        3 => 'CANCELED'
    ];

    public function slug()
    {
        return $this->morphOne('App\Models\Slug', 'slug');
    }

    public function company()
    {
        return $this->belongsTo('App\Models\Company');
    }

    public function smsRecipients()
    {
        return $this->hasMany('App\Models\CompanySmsRecipient');
    }

    public function activities()
    {
        return $this->morphMany('App\Models\Activity', 'module');
    }

    public function createdBy()
    {
        return $this->belongsTo('App\Models\User', 'created_by');
    }

    public function pendingRecipientsCount()
    {
        return $this->smsRecipients()->where('status', 1)->count();
    }

    public function successRecipientsCount()
    {
        return $this->smsRecipients()->where('status', 2)->count();
    }

    public function failedRecipientsCount()
    {
        return $this->smsRecipients()->where('status', 3)->count();
    }

    public function possibleAmount()
    {
        $noOfRecipients = $this->smsRecipients->count();

        return $this->credit_per_sent * $noOfRecipients;
    }

    public function toArray()
    {
        $arr = [
            'slug' => $this->slug,
            'code' => $this->code,
            'sms_type' => [
                'id' => $this->sms_type,
                'name' => $this->smsTypes[$this->sms_type]
            ],
            'amount' => $this->amount,
            'transaction_date' => $this->transaction_date,
            'transaction_type' => [
                'id' => $this->transaction_type,
                'name' => $this->transactionTypes[$this->transaction_type]
            ],
            'status' => [
                'id' => $this->status,
                'name' => $this->statuses[$this->status]
            ],
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at
                ? $this->updated_at->toDateTimeString()
                : $this->created_at->toDateTimeString()
        ];

        if(request()->has('my-company-sms-transactions-related')):
            $arr['message'] = $this->message;
            $arr['credit_per_sent'] = $this->credit_per_sent;
            $arr['recipients_count'] = $this->smsRecipients->count();
            $arr['success_recipients_count'] = $this->successRecipientsCount();
            $arr['possible_amount'] = $this->possibleAmount();
            $arr['created_by'] = $this->createdBy->account;
            $arr['company'] = $this->company->toArrayMyCompanySmsTransactionsRelated();
        endif;

        return $arr;
    }

    public function toArrayAdminSmsTransactionsRelated()
    {
        return [
            'slug' => $this->slug,
            'code' => $this->code,
            'sms_type' => [
                'id' => $this->sms_type,
                'name' => $this->smsTypes[$this->sms_type]
            ],
            'amount' => $this->amount,
            'transaction_date' => $this->transaction_date,
            'transaction_type' => [
                'id' => $this->transaction_type,
                'name' => $this->transactionTypes[$this->transaction_type]
            ],
            'status' => [
                'id' => $this->status,
                'name' => $this->statuses[$this->status]
            ],
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at
                ? $this->updated_at->toDateTimeString()
                : $this->created_at->toDateTimeString(),
            'message' => $this->message,
            'credit_per_sent' => $this->credit_per_sent,
            'success_recipients_count' => $this->successRecipientsCount(),
            'recipients_count' => $this->smsRecipients->count(),
            'possible_amount' => $this->possibleAmount(),
            'created_by' => $this->createdBy->account,
            'company' => $this->company->toArrayAdminSmsTransactionsRelated(),
        ];
    }

    
    public function toArrayMyCompanySmsTransactionsRelated()
    {
        return [
            'slug' => $this->slug,
            'code' => $this->code,
            'sms_type' => [
                'id' => $this->sms_type,
                'name' => $this->smsTypes[$this->sms_type]
            ],
            'amount' => $this->amount,
            'transaction_date' => $this->transaction_date,
            'transaction_type' => [
                'id' => $this->transaction_type,
                'name' => $this->transactionTypes[$this->transaction_type]
            ],
            'status' => [
                'id' => $this->status,
                'name' => $this->statuses[$this->status]
            ],
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at
                ? $this->updated_at->toDateTimeString()
                : $this->created_at->toDateTimeString(),
            'message' => $this->message,
            'credit_per_sent' => $this->credit_per_sent,
            'success_recipients_count' => $this->successRecipientsCount(),
            'recipients_count' => $this->smsRecipients->count(),
            'possible_amount' => $this->possibleAmount(),
            'created_by' => $this->createdBy->account,
            'company' => $this->company->toArrayMyCompanySmsTransactionsRelated(),
        ];
    }
}
