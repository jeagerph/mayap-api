<?php

namespace App\Models;

use App\Models\Model;

class CompanyOfficerClassification extends Model
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
            request()->has('my-company-officer-classification-options') ||
            request()->has('my-company-officer-classifications-related') ||
            request()->has('beneficiary-options')
        ):
            $arr['id'] = $this->id;
        endif;

        return $arr;
    }

    public function toArrayMyCompanyOfficerClassificationsRelated()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'enabled' => $this->enabled,
        ];
    }
}
