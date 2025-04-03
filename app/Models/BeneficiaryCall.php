<?php

namespace App\Models;

use App\Models\Model;

class BeneficiaryCall extends Model
{
    public $searchFields = [];

    public $filterFields = [
        'beneficiaryCode' => 'slug:beneficiary_id',
    ];

    public $sortFields = [
        'callDate' => ':call_date',
        'created' => ':created_at'
    ];

    public $statusOptions = [
        1 => 'INITIATED',
        2 => 'COMPLETED',
        3 => 'DISCONNECTED'
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company');
    }

    public function beneficiary()
    {
        return $this->belongsTo('App\Models\Beneficiary');
    }

    public function callTransaction()
    {
        return $this->belongsTo('App\Models\CompanyCallTransaction', 'company_call_transaction_id');
    }

    public function createdBy()
    {
        return $this->belongsTo('App\Models\User', 'created_by');
    }

    public function toArray()
    {
        $arr = [
            'call_date' => $this->call_date,
            'call_minutes' => $this->call_minutes,
            'mobile_number' => $this->mobile_number,
            'call_url' => $this->call_url,
            'mobile_number' => $this->mobile_number,
            'status' => [
                'id' => $this->status,
                'name' => $this->statusOptions[$this->status]
            ],
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at
                ? $this->updated_at->toDateTimeString()
                : $this->created_at->toDateTimeString()
        ];

        if (request()->has('beneficiary-calls-related')):

            $arr['id'] = $this->id;
            $arr['created_by'] = $this->createdBy->account;
            $arr['call_transaction'] = $this->callTransaction->toArrayBeneficiaryCallsRelated();

        endif;

        return $arr;
    }

    public function toArrayBeneficiaryCallsRelated()
    {
        return [
            'id' => $this->id,
            'call_date' => $this->call_date,
            'call_minutes' => $this->call_minutes,
            'mobile_number' => $this->mobile_number,
            'call_url' => $this->call_url,
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
