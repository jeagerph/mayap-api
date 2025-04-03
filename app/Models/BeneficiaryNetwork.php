<?php

namespace App\Models;

use App\Models\Model;

class BeneficiaryNetwork extends Model
{
    public $searchFields = [];

    public $filterFields = [
        'beneficiaryCode' => 'slug:beneficiary_id'
    ];

    public $sortFields = [
        'orderNo' => ':order_no'
    ];

    public $networkLevelOptions = [
        1 => 'MASTER LEVEL',
        2 => '1ST DEGREE',
        3 => '2ND DEGREE',
        4 => '3RD DEGREE',
        5 => '4TH DEGREE',
        6 => '5TH DEGREE',
    ];

    public function beneficiary()
    {
        return $this->belongsTo('App\Models\Beneficiary');
    }

    public function parentBeneficiary()
    {
        return $this->belongsTo('App\Models\Beneficiary', 'parent_beneficiary_id');
    }

    public function toArray()
    {
        $arr = [
            'order_no' => $this->order_no,
        ];

        if (request()->has('beneficiary-networks-related')):
            $arr['beneficiary'] = $this->beneficiary->toArrayBeneficiaryNetworksRelated();
        endif;
        
        return $arr;
    }

    public function toArrayBeneficiaryNetworkOptions()
    {
        return [
            'parent_beneficiary' => $this->parentBeneficiary->toArrayBeneficiaryNetworkOptions()
        ];
    }
}
