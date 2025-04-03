<?php

namespace App\Models;

use App\Models\Model;

class BeneficiaryIncentive extends Model
{
    public $searchFields = [];

    public $filterFields = [
        'beneficiaryCode' => 'slug:beneficiary_id',
        'mode' => ':mode',
    ];

    public $sortFields = [
        'incentiveDate' => ':incentive_date',
        'created' => ':created_at'
    ];

    public $modeOptions = [
        1 => 'ADD',
        2 => 'DEDUCT'
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

    public function creator()
    {
        return [
            'full_name' => $this->createdBy->account
                ? $this->createdBy->account->full_name
                : 'DELETED USER'
        ];
    }

    public function toArray()
    {
        $arr = [
            'points' => $this->points,
            'mode' => [
                'id' => $this->mode,
                'name' => $this->modeOptions[$this->mode],
            ],
            'incentive_date' => $this->incentive_date,
            'remarks' => $this->remarks,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at
                ? $this->updated_at->toDateTimeString()
                : $this->created_at->toDateTimeString()
        ];

        if (
            request()->has('beneficiary-incentives-related')
        ):

            $arr['id'] = $this->id;

        endif;

        if (request()->has('my-company-incentives-related')):
            $arr['id'] = $this->id;
            $arr['beneficiary'] = $this->beneficiary->toArrayMyCompanyIncentivesRelated();
            $arr['created_by'] = $this->creator();
        endif;

        return $arr;
    }
}
