<?php

namespace App\Models;

use App\Models\Model;

class BeneficiaryAssistance extends Model
{
    protected $guarded = [];
    public $searchFields = [
        'assistanceType' => ':assistance_type',
        'beneficiaryFirstName' => 'beneficiary:first_name',
        'beneficiaryLastName' => 'beneficiary:last_name',
        'beneficiaryMiddleName'=>'beneficiary:middle_name',
        'beneficiaryAddress'=> 'beneficiary:address'
    ];

    public $filterFields = [
        'companyCode' => 'slug:company_id',
        'beneficiaryCode' => 'slug:beneficiary_id',
        'assistanceId' => ':id',
        'assisted' => ':is_assisted',
        'assistanceFrom' => ':assistance_from',
        'gender'=>'beneficiary:gender',
        'dateOfBirth'=>'beneficiary:date_of_birth',
    ];

    public $sortFields = [
        'assistanceDate' => ':assistance_date',
        'assistedDate' => ':assisted_date',
        'isAssisted' => ':is_assisted',
        'created' => ':created_at'
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
            'full_name' =>  $this->createdBy && $this->createdBy->account
                ? $this->createdBy->account->full_name
                : 'DELETED USER'
        ];
    }

    public function toArray()
    {
        $arr = [
            'assistance_date' => $this->assistance_date,
            'assistance_type' => $this->assistance_type,
            'assistance_amount' => $this->assistance_amount,
            'is_assisted' => $this->is_assisted,
            'assisted_date' => $this->assisted_date,
            'assisted_by' => $this->assisted_by,
            'assistance_from' => $this->assistance_from,
            'remarks' => $this->remarks,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at
                ? $this->updated_at->toDateTimeString()
                : $this->created_at->toDateTimeString()
        ];

        if (request()->has('beneficiary-assistances-related')):

            $arr['id'] = $this->id;

        endif;

        if (request()->has('dashboard-assistances-related')):

            $arr['id'] = $this->id;
            $arr['beneficiary'] = $this->beneficiary->toArrayDashboardAssistancesRelated();

        endif;

        if (request()->has('assistances-related')):

            $arr['id'] = $this->id;
            $arr['beneficiary'] = $this->beneficiary->toArrayAssistancesRelated();
            $arr['created_by'] = $this->creator();

        endif;

        return $arr;
    }

    public function toArrayBeneficiaryAssistancesRelated()
    {
        return [
            'id' => $this->id,
            'assistance_date' => $this->assistance_date,
            'assistance_type' => $this->assistance_type,
            'assistance_amount' => $this->assistance_amount,
            'is_assisted' => $this->is_assisted,
            'assisted_date' => $this->assisted_date,
            'assisted_by' => $this->assisted_by,
            'assistance_from' => $this->assistance_from,
            'remarks' => $this->remarks,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at
                ? $this->updated_at->toDateTimeString()
                : $this->created_at->toDateTimeString()
        ];
    }

    public function toArrayDashboardAssistancesRelated()
    {
        return [
            'id' => $this->id,
            'assistance_date' => $this->assistance_date,
            'assistance_type' => $this->assistance_type,
            'assistance_amount' => $this->assistance_amount,
            'is_assisted' => $this->is_assisted,
            'assisted_date' => $this->assisted_date,
            'assisted_by' => $this->assisted_by,
            'assistance_from' => $this->assistance_from,
            'remarks' => $this->remarks,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at
                ? $this->updated_at->toDateTimeString()
                : $this->created_at->toDateTimeString()
        ];
    }

    public function toArrayAssistancesRelated()
    {
        return [
            'id' => $this->id,
            'assistance_date' => $this->assistance_date,
            'assistance_type' => $this->assistance_type,
            'assistance_amount' => $this->assistance_amount,
            'is_assisted' => $this->is_assisted,
            'assisted_date' => $this->assisted_date,
            'assisted_by' => $this->assisted_by,
            'assistance_from' => $this->assistance_from,
            'remarks' => $this->remarks,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at
                ? $this->updated_at->toDateTimeString()
                : $this->created_at->toDateTimeString(),
            'created_by' => $this->creator(),

            'beneficiary' => $this->beneficiary->toArrayAssistancesRelated(),
        ];
    }
}
