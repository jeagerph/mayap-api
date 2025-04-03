<?php

namespace App\Models;

use App\Models\Model;

class CompanyIdSetting extends Model
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
            'name' => $this->name,
            'address' => $this->address,
            'contact' => $this->contact,
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
            'name' => $this->name,
            'address' => $this->address,
            'contact' => $this->contact,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at
                ? $this->updated_at->toDateTimeString()
                : $this->created_at->toDateTimeString()
        ];
    }

    public function toArrayMyCompanyCallSettingRelated()
    {
        return [
            'name' => $this->name,
            'address' => $this->address,
            'contact' => $this->contact,
        ];
    }
}
