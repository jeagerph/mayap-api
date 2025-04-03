<?php

namespace App\Models;

use App\Models\Model;

use App\Observers\CompanySmsCreditObserver as Observer;

class CompanySmsCredit extends Model
{
    use Observer;

    public $searchFields = [];

    public $filterFields = [
        'companyCode' => 'slug:company_id',
    ];

    public $sortFields = [
        'creditDate' => ':credit_date',
        'created' => ':created_at'
    ];

    public $creditModeOptions = [
        1 => 'REPLENISH',
        2 => 'WITDRAWAL'
    ];
    
    public function company()
    {
        return $this->belongsTo('App\Models\Company');
    }

    public function createdBy()
    {
        return $this->belongsTo('App\Models\User', 'created_by');
    }

    public function toArray()
    {
        $arr = [
            'code' => $this->code,
            'amount' => $this->amount,
            'credit_date' => $this->credit_date,
            'credit_mode' => [
                'id' => $this->credit_mode,
                'name' => $this->creditModeOptions[$this->credit_mode]
            ],
            'remarks' => $this->remarks,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at
                ? $this->updated_at->toDateTimeString()
                : $this->created_at->toDateTimeString(),
        ];

        if(request()->has('admin-company-sms-credits-related')):
            $arr['id'] = $this->id;
            $arr['created_by'] = $this->createdBy->account;
        endif;
        
        return $arr;
    }

    public function toArrayAdminCompanySmsCreditsRelated()
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'amount' => $this->amount,
            'credit_date' => $this->credit_date,
            'credit_mode' => [
                'id' => $this->credit_mode,
                'name' => $this->creditModeOptions[$this->credit_mode]
            ],
            'remarks' => $this->remarks,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at
                ? $this->updated_at->toDateTimeString()
                : $this->created_at->toDateTimeString(),
            'created_by' => $this->createdBy->account,
        ];
    }
}
