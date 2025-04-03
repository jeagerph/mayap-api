<?php

namespace App\Models;

use App\Observers\CompanyCallTransactionObserver as Observer;

class CompanyCallTransaction extends Model
{
    use Observer;
    
    public $searchFields = [
        'code' => ':code',
    ];

    public $filterFields = [
        'companyCode' => 'slug:company_id',
        'status' => ':status',
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

    public $statusOptions = [
        1 => 'PENDING',
        2 => 'COMPLETED',
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

    public function activities()
    {
        return $this->morphMany('App\Models\Activity', 'module');
    }

    public function beneficiaryCall()
    {
        return $this->hasOne('App\Models\BeneficiaryCall', 'company_call_transaction_id');
    }

    public function createdBy()
    {
        return $this->belongsTo('App\Models\User', 'created_by');
    }

    public function toArray()
    {
        $arr = [
            'slug' => $this->slug,
            'code' => $this->code,
            'transaction_date' => $this->transaction_date,
            'amount' => $this->amount,
            'recording_url' => $this->recording_url,
            'mobile_number' => $this->mobile_number,
            'call_sid' => $this->call_sid,
            'call_duration' => $this->call_duration,
            'status' => [
                'id' => $this->status,
                'name' => $this->statusOptions[$this->status]
            ],
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at
                ? $this->updated_at->toDateTimeString()
                : $this->created_at->toDateTimeString()
        ];

        if(request()->has('my-company-call-transactions-related')):
            $arr['created_by'] = $this->createdBy->account;
        endif;

        return $arr;
    }

    
    public function toArrayMyCompanyCallTransactionsRelated()
    {
        return [
            'slug' => $this->slug,
            'code' => $this->code,
            'transaction_date' => $this->transaction_date,
            'amount' => $this->amount,
            'recording_url' => $this->recording_url,
            'mobile_number' => $this->mobile_number,
            'call_sid' => $this->call_sid,
            'call_duration' => $this->call_duration,
            'status' => [
                'id' => $this->status,
                'name' => $this->statusOptions[$this->status]
            ],
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at
                ? $this->updated_at->toDateTimeString()
                : $this->created_at->toDateTimeString(),
            'created_by' => $this->createdBy->account,
        ];
    }

    public function toArrayBeneficiaryCallsRelated()
    {
        return [
            'slug' => $this->slug,
            'code' => $this->code,
            'transaction_date' => $this->transaction_date,
            'recording_url' => $this->recording_url,
            'recording_duration' => $this->recording_duration,
            'mobile_number' => $this->mobile_number,
            'status' => [
                'id' => $this->status,
                'name' => $this->statusOptions[$this->status]
            ],
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at
                ? $this->updated_at->toDateTimeString()
                : $this->created_at->toDateTimeString(),
            'created_by' => $this->createdBy->account,
        ];
    }
}
