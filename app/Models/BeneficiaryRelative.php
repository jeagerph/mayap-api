<?php

namespace App\Models;

use App\Models\Model;

class BeneficiaryRelative extends Model
{
    public $searchFields = [];

    public $filterFields = [
        'beneficiaryCode' => 'slug:beneficiary_id'
    ];

    public $sortFields = [
        'orderNo' => ':order_no'
    ];

    public $relationships = [
        'GRANDFATHER' => [
            1 => 'GRANDSON',
            2 => 'GRANDDAUGHTER'
        ],
        'GRANDMOTHER' => [
            1 => 'GRANDSON',
            2 => 'GRANDDAUGHTER'
        ],
        'FATHER' => [
            1 => 'SON',
            2 => 'DAUGHTER'
        ],
        'MOTHER' => [
            1 => 'SON',
            2 => 'DAUGHTER'
        ],
        'UNCLE' => [
            1 => 'NEPHEW',
            2 => 'NIECE'
        ],
        'AUNT' => [
            1 => 'NEPHEW',
            2 => 'NIECE'
        ],
        'HUSBAND' => [
            1 => 'WIFE',
            2 => 'WIFE'
        ],
        'WIFE' => [
            1 => 'HUSBAND',
            2 => 'HUSBAND'
        ],
        'SON' => [
            1 => 'FATHER',
            2 => 'MOTHER'
        ],
        'DAUGHTER' => [
            1 => 'FATHER',
            2 => 'MOTHER'
        ],
        'SIBLING' => [
            1 => 'SIBLING',
            2 => 'SIBLING'
        ],
        'GRANDSON' => [
            1 => 'GRANDFATHER',
            2 => 'GRANDMOTHER'
        ],
        'GRANDDAUGHTER' => [
            1 => 'GRANDFATHER',
            2 => 'GRANDMOTHER'
        ],
        'NEPHEW' => [
            1 => 'UNCLE',
            2 => 'AUNT'
        ],
        'NIECE' => [
            1 => 'UNCLE',
            2 => 'AUNT'
        ],
        'COUSIN' => [
            1 => 'COUSIN',
            2 => 'COUSIN'
        ],
        'LIVE-IN PARTNER' => [
            1 => 'LIVE-IN PARTNER',
            2 => 'LIVE-IN PARTNER'
        ],
        'STEPFATHER' => [
            1 => 'STEPSON',
            2 => 'STEPDAUGHTER'
        ],
        'STEPMOTHER' => [
            1 => 'STEPSON',
            2 => 'STEPDAUGHTER'
        ],
        'STEPSON' => [
            1 => 'STEPFATHER',
            2 => 'STEPMOTHER'
        ],
        'STEPDAUGHTER' => [
            1 => 'STEPFATHER',
            2 => 'STEPMOTHER'
        ],
    ];

    public function beneficiary()
    {
        return $this->belongsTo('App\Models\Beneficiary');
    }

    public function relative()
    {
        return $this->belongsTo('App\Models\Beneficiary', 'related_beneficiary_id');
    }

    public function toArray()
    {
        $arr = [
            'order_no' => $this->order_no,
            'relationship' => $this->relationship,
        ];

        if(request()->has('beneficiary-relatives-related')):
            $arr['id'] = $this->id;
            $arr['relative'] = $this->relative->toArrayBeneficiaryRelativesRelated();
        endif;

        return $arr;
    }
}
