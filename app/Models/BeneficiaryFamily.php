<?php

namespace App\Models;

use App\Models\Model;

class BeneficiaryFamily extends Model
{
    public $searchFields = [];

    public $filterFields = [
        'beneficiaryCode' => 'slug:beneficiary_id'
    ];

    public $sortFields = [
        'orderNo' => ':order_no'
    ];

    public $genderOptions = [
        1 => 'MALE',
        2 => 'FEMALE',
    ];

    public function beneficiary()
    {
        return $this->belongsTo('App\Models\Beneficiary');
    }

    public function toArray()
    {
        $arr = [
            'order_no' => $this->order_no,
            'full_name' => $this->full_name,
            'gender' => [
                'id' => $this->gender,
                'name' => $this->genderOptions[$this->gender],
            ],
            'date_of_birth' => $this->date_of_birth,
            'education' => $this->education,
            'occupation' => $this->occupation,
            'address' => $this->address,
            'relationship' => $this->relationship,
        ];

        if(request()->has('beneficiary-families-related')):
            $arr['id'] = $this->id;
        endif;

        return $arr;
    }

    public function toArrayBeneficiaryFamiliesRelated()
    {
        return [
            'id' => $this->id,
            'order_no' => $this->order_no,
            'full_name' => $this->full_name,
            'gender' => [
                'id' => $this->gender,
                'name' => $this->genderOptions[$this->gender],
            ],
            'date_of_birth' => $this->date_of_birth,
            'education' => $this->education,
            'occupation' => $this->occupation,
            'address' => $this->address,
            'relationship' => $this->relationship,
        ];
    }
}
