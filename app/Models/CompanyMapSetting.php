<?php

namespace App\Models;

use App\Models\Model;

class CompanyMapSetting extends Model
{
    public $searchFields = [];

    public $filterFields = [];

    public $sortFields = [
        'created' => ':created_at'
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }

    public function toArray()
    {
        $arr = [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at
                ? $this->updated_at->toDateTimeString()
                : $this->created_at->toDateTimeString()
        ];

        return $arr;
    }

    public function toArrayAdminCompaniesRelated()
    {
        return [
            'api_key' => $this->api_key,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at
                ? $this->updated_at->toDateTimeString()
                : $this->created_at->toDateTimeString()
        ];
    }
}
