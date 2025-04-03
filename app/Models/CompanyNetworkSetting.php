<?php

namespace App\Models;

use App\Models\Model;

class CompanyNetworkSetting extends Model
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
            'master_degree_enabled' => $this->master_degree_enabled,
            'master_degree_points' => $this->master_degree_points,
            'first_degree_enabled' => $this->first_degree_enabled,
            'first_degree_points' => $this->first_degree_points,
            'second_degree_enabled' => $this->second_degree_enabled,
            'second_degree_points' => $this->second_degree_points,
            'third_degree_enabled' => $this->third_degree_enabled,
            'third_degree_points' => $this->third_degree_points,
            'fourth_degree_enabled' => $this->fourth_degree_enabled,
            'fourth_degree_points' => $this->fourth_degree_points,
            'fifth_degree_enabled' => $this->fifth_degree_enabled,
            'fifth_degree_points' => $this->fifth_degree_points,
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
            'master_degree_enabled' => $this->master_degree_enabled,
            'master_degree_points' => $this->master_degree_points,
            'first_degree_enabled' => $this->first_degree_enabled,
            'first_degree_points' => $this->first_degree_points,
            'second_degree_enabled' => $this->second_degree_enabled,
            'second_degree_points' => $this->second_degree_points,
            'third_degree_enabled' => $this->third_degree_enabled,
            'third_degree_points' => $this->third_degree_points,
            'fourth_degree_enabled' => $this->fourth_degree_enabled,
            'fourth_degree_points' => $this->fourth_degree_points,
            'fifth_degree_enabled' => $this->fifth_degree_enabled,
            'fifth_degree_points' => $this->fifth_degree_points,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at
                ? $this->updated_at->toDateTimeString()
                : $this->created_at->toDateTimeString()
        ];
    }

    public function toArrayMyCompanyRelated()
    {
        return [
            'master_degree_enabled' => $this->master_degree_enabled,
            'master_degree_points' => $this->master_degree_points,
            'first_degree_enabled' => $this->first_degree_enabled,
            'first_degree_points' => $this->first_degree_points,
            'second_degree_enabled' => $this->second_degree_enabled,
            'second_degree_points' => $this->second_degree_points,
            'third_degree_enabled' => $this->third_degree_enabled,
            'third_degree_points' => $this->third_degree_points,
            'fourth_degree_enabled' => $this->fourth_degree_enabled,
            'fourth_degree_points' => $this->fourth_degree_points,
            'fifth_degree_enabled' => $this->fifth_degree_enabled,
            'fifth_degree_points' => $this->fifth_degree_points,
        ];
    }
}
