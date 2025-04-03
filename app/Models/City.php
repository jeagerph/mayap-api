<?php

namespace App\Models;

use App\Models\Model;

class City extends Model
{
    public $searchFields = [
        'name' => ':name',
    ];

    public $filterFields = [
        'provCode' => ':prov_code',
    ];

    public $sortFields = [
        'name' => ':name',
        'created' => ':created_at',
    ];

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

		return $arr;
    }

    public function toArrayMyCompanyMonitoringRelated()
    {
        return [
            'name' => $this->name
        ];
    }
}
