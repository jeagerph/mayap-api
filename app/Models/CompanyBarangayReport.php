<?php

namespace App\Models;

use App\Models\Model;

class CompanyBarangayReport extends Model
{
    public $searchFields = [];

    public $filterFields = [
        'companyCode' => 'slug:company_id',
    ];

    public $sortFields = [
        'barangayName' => ':barangay_name',
        'created' => ':created_at'
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company');
    }

    public function province()
    {
        return $this->belongsTo('App\Models\Province', 'province_id', 'prov_code');
    }

    public function city()
    {
        return $this->belongsTo('App\Models\City', 'city_id', 'city_code');
    }

    public function barangay()
    {
        return $this->belongsTo('App\Models\Barangay', 'barangay_id');
    }
}
