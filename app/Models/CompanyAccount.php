<?php

namespace App\Models;

use App\Models\Model;

use App\Observers\CompanyAccountObserver as Observer;

class CompanyAccount extends Model
{
    use Observer;

    public $searchFields = [];

    public $filterFields = [
        'companyCode' => 'slug:company_id',
    ];

    public $sortFields = [
        'created' => ':created_at',
    ];

    public $rangeFields = [];

    public function slug()
    {
        return $this->morphOne('App\Models\Slug', 'slug');
    }

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }

    public function account()
    {
        return $this->belongsTo('App\Models\Account');
    }

    public function companyPosition()
    {
        return $this->belongsTo('App\Models\CompanyPosition');
    }

    public function toArray()
    {
        $arr = [
            'slug' => $this->slug,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at
                ? $this->updated_at->toDateTimeString()
                : $this->created_at->toDateTimeString()
        ];

        if(request()->has('admin-company-accounts-related')):
            $arr['company_position'] = $this->companyPosition->toArrayAdminCompanyAccountsRelated();
            $arr['account'] = $this->account->toArrayAdminCompanyAccountsRelated();
        endif;

        if(request()->has('my-company-accounts-related')):
            $arr['company_position'] = $this->companyPosition->toArrayMyCompanyAccountsRelated();
            $arr['account'] = $this->account->toArrayMyCompanyAccountsRelated();
        endif;
        
        return $arr;
    }

    public function toArrayAdminCompanyAccountsRelated()
    {
        return [
            'id' => $this->id,
            'company_position' => $this->companyPosition,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at
                ? $this->updated_at->toDateTimeString()
                : $this->created_at->toDateTimeString(),
            'account' => $this->account->toArrayAdminCompanyAccountsRelated()
        ];
    }

    public function toArrayMyCompanyAccountsRelated()
    {
        return [
            'id' => $this->id,
            'company_position' => $this->companyPosition,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at
                ? $this->updated_at->toDateTimeString()
                : $this->created_at->toDateTimeString(),
            'account' => $this->account->toArrayMyCompanyAccountsRelated()
        ];
    }
}
