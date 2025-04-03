<?php

namespace App\Models;

use App\Models\Model;

class CompanyBarangay extends Model
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

    public function toArray()
    {
        $arr = [
            'province_name' => $this->province_name,
            'city_name' => $this->city_name,
            'barangay_name' => $this->barangay_name,
            'city_logo' => $this->city_logo
                ? env('CDN_URL', '') . '/storage/' . $this->city_logo
                : '',
            'barangay_logo' => $this->barangay_logo
                ? env('CDN_URL', '') . '/storage/' . $this->barangay_logo
                : '',
            'status' => $this->status,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at
                ? $this->updated_at->toDateTimeString()
                : $this->created_at->toDateTimeString()
        ];

        if (request()->has('admin-company-barangays-related')):
            $arr['id'] = $this->id;
            $arr['province_id'] = $this->province_id;
            $arr['city_id'] = $this->city_id;
            $arr['barangay_id'] = $this->barangay_id;
        endif;

        if (request()->has('monitoring-barangays-related')):
            $arr['id'] = $this->id;
            $arr['province_id'] = $this->province_id;
            $arr['city_id'] = $this->city_id;
            $arr['barangay_id'] = $this->barangay_id;
        endif;

        return $arr;
    }

    public function toArrayAdminCompanyBarangaysRelated()
    {
        return [
            'id' => $this->id,
            'province_id' => $this->province_id,
            'city_id' => $this->city_id,
            'barangay_id' => $this->barangay_id,
            'province_name' => $this->province_name,
            'city_name' => $this->city_name,
            'barangay_name' => $this->barangay_name,
            'city_logo' => $this->city_logo
                ? env('CDN_URL', '') . '/storage/' . $this->city_logo
                : '',
            'barangay_logo' => $this->barangay_logo
                ? env('CDN_URL', '') . '/storage/' . $this->barangay_logo
                : '',
            'status' => $this->status,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at
                ? $this->updated_at->toDateTimeString()
                : $this->created_at->toDateTimeString()
        ];
    }
}
