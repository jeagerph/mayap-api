<?php

namespace App\Models;

use App\Models\Model;

class CompanyClassification extends Model
{
    public $searchFields = [
        'name' => ':name',
        'description' => ':description',
    ];

    public $filterFields = [
        'companyCode' => 'slug:company_id',
        'enabled' => ':enabled',
    ];

    public $sortFields = [
        'name' => ':name',
        'enabled' => ':enabled',
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company');
    }

    public function beneficiaries()
    {
        return $this->hasMany('App\Models\Beneficiary');
    }

    public function toArray()
    {
        $arr = [
            'name' => $this->name,
            'description' => $this->description,
            'enabled' => $this->enabled,
        ];

        if(
            request()->has('my-company-classification-options') ||
            request()->has('my-company-classifications-related') ||
            request()->has('member-options')
        ):
            $arr['id'] = $this->id;
        endif;

        return $arr;
    }

    public function toArrayMyCompanyClassificationsRelated()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'enabled' => $this->enabled,
        ];
    }
}
