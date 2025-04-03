<?php

namespace App\Models;

use App\Models\Model;

class Province extends Model
{
    public $searchFields = [
        'name' => ':name',
    ];

    public $filterFields = [];
    
	public $sortFields = [
        'name' => ':name',
    ];

	public function cities()
	{
		return $this->hasMany('App\Models\City', 'prov_code', 'prov_code');
    }
    
    public function toArray()
    {
        $arr = [
            'name' => strtoupper($this->name),
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
