<?php

namespace App\Models;

use App\Models\Model;

class Barangay extends Model
{
    public $searchFields = [
        'name' => ':name',
    ];
    
    public $filterFields = [
        'cityCode' => ':city_code',
    ];

    public $sortFields = [
        'name' => ':name',
        'created' => ':created_at',
    ];

    public function city()
    {
        return $this->belongsTo('App\Models\City', 'city_code', 'city_code');
    }

    public function province()
    {
        return $this->belongsTo('App\Models\Province', 'prov_code', 'prov_code');
    }

    public function toArray()
    {
        $arr = [
            'name' => strtoupper($this->name),
            'city_code' => $this->city_code,
            'prov_code' => $this->prov_code,
            'reg_code' => $this->reg_code,
            'psgc_code' => $this->psgc_code,
        ];

        if(
            request()->has('beneficiary-options') ||
            request()->has('company-options') ||
            request()->has('patient-options') ||
            request()->has('assistance-options') ||
            request()->has('voter-options') ||
            request()->has('dashboard-options')
        ):
            $arr['id'] = $this->id;
        endif;

        if (request()->has('my-company-monitoring-related')):
            $arr['city'] = $this->city->toArrayMyCompanyMonitoringRelated();
            $arr['province'] = $this->province->toArrayMyCompanyMonitoringRelated();
        endif;

        return $arr;
    }

    public function toArrayBeneficiariesRelated()
    {
        return [
            'name' => strtoupper($this->name),
            'city_code' => $this->city_code,
            'prov_code' => $this->prov_code,
            'reg_code' => $this->reg_code,
            'psgc_code' => $this->psgc_code,
            'city' => $this->city,
            'province' => $this->province,
        ];
    }
}
