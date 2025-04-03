<?php

namespace App\Models;

use App\Models\Model;

class BeneficiaryPatient extends Model
{
    public $searchFields = [];

    public $filterFields = [
        'beneficiaryCode' => 'slug:beneficiary_id',
        'companyCode' => 'slug:company_id',
        'status' => ':status',
    ];

    public $sortFields = [
        'patientDate' => ':patient_date',
        'created' => ':created_at',
        'status' => ':status',
    ];

    public $statusOptions = [
        1 => 'FOR APPROVAL',
        2 => 'IN PROGRESS',
        3 => 'COMPLETED',
        4 => 'CANCELED'
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

    public function fullName($format = 'L, F M')
    {
        switch($format):

            case 'L, F M':
                return $this->last_name . ', ' . $this->first_name . ($this->middle_name ? ' ' . $this->middle_name : '');
                break;

            case 'F M L':
                return $this->first_name . ' ' . ($this->middle_name ? $this->middle_name .' ' : '') . $this->last_name;
                break;

            case 'L, F MI':
                return $this->last_name . ', ' . $this->first_name . ($this->middle_name ? (' ' . $this->middle_name[0] .'.') : '');
                break;

            default:
                return $this->last_name . ', ' . $this->first_name . ($this->middle_name ? ' ' . $this->middle_name : '');
                break;
        endswitch;
    }

    public function toArray()
    {
        $arr = [
            'patient_date' => $this->patient_date,
            'full_name' => $this->fullName(),
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'relation_to_patient' => $this->relation_to_patient,

            'problem_presented' => $this->problem_presented,
            'findings' => $this->findings,
            'assessment_recommendation' => $this->assessment_recommendation,
            'needs' => $this->needs,

            'status' => [
                'id' => $this->status,
                'name' => $this->statusOptions[$this->status]
            ],
            
            'remarks' => $this->remarks,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at
                ? $this->updated_at->toDateTimeString()
                : $this->created_at->toDateTimeString()
        ];

        if (request()->has('beneficiary-patients-related')):

            $arr['id'] = $this->id;

        endif;

        if (request()->has('dashboard-patients-related')):

            $arr['id'] = $this->id;
            $arr['beneficiary'] = $this->beneficiary->toArrayDashboardPatientsRelated();

        endif;

        if (request()->has('patients-related')):

            $arr['id'] = $this->id;
            $arr['beneficiary'] = $this->beneficiary->toArrayPatientsRelated();
            $arr['created_by'] = $this->creator();
        endif;

        return $arr;
    }

    public function toArrayBeneficiaryPatientsRelated()
    {
        return [
            'id' => $this->id,
            'patient_date' => $this->patient_date,
            'full_name' => $this->fullName(),
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'relation_to_patient' => $this->relation_to_patient,

            'problem_presented' => $this->problem_presented,
            'findings' => $this->findings,
            'assessment_recommendation' => $this->assessment_recommendation,
            'needs' => $this->needs,

            'status' => [
                'id' => $this->status,
                'name' => $this->statusOptions[$this->status]
            ],
            
            'remarks' => $this->remarks,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at
                ? $this->updated_at->toDateTimeString()
                : $this->created_at->toDateTimeString()
        ];
    }

    public function toArrayDashboardPatientsRelated()
    {
        return [
            'id' => $this->id,
            'patient_date' => $this->patient_date,
            'full_name' => $this->fullName(),
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'relation_to_patient' => $this->relation_to_patient,

            'problem_presented' => $this->problem_presented,
            'findings' => $this->findings,
            'assessment_recommendation' => $this->assessment_recommendation,
            'needs' => $this->needs,

            'status' => [
                'id' => $this->status,
                'name' => $this->statusOptions[$this->status]
            ],
            
            'remarks' => $this->remarks,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at
                ? $this->updated_at->toDateTimeString()
                : $this->created_at->toDateTimeString()
        ];
    }

    public function toArrayPatientsRelated()
    {
        return [
            'id' => $this->id,
            'patient_date' => $this->patient_date,
            'full_name' => $this->fullName(),
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'relation_to_patient' => $this->relation_to_patient,

            'problem_presented' => $this->problem_presented,
            'findings' => $this->findings,
            'assessment_recommendation' => $this->assessment_recommendation,
            'needs' => $this->needs,
            'status' => [
                'id' => $this->status,
                'name' => $this->statusOptions[$this->status]
            ],
            'remarks' => $this->remarks,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at
                ? $this->updated_at->toDateTimeString()
                : $this->created_at->toDateTimeString(),
            'beneficiary' => $this->beneficiary->toArrayPatientsRelated(),
        ]; 
    }
}
