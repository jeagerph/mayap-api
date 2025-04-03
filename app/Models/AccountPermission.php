<?php

namespace App\Models;

use App\Models\Model;

class AccountPermission extends Model
{
    public $searchFields = [];

    public $filterFields = [
        'accountCode' => 'slug:account_id'
    ];

    public $sortFields = [];

    public function module()
    {
        return $this->belongsTo('App\Models\Module');
    }

    public function account()
    {
        return $this->belongsTo('App\Models\Account');
    }

    public function toArray()
    {
        $arr = [
            'access' => $this->access,
            'index' => $this->index,
            'store' => $this->store,
            'update' => $this->update,
            'destroy' => $this->destroy,
        ];

        if(
            request()->has('admin-accounts-related') ||
            request()->has('my-account-related') ||
            request()->has('my-barangay-related') ||
            request()->has('admin-company-accounts-related') ||
            request()->has('my-company-accounts-related')
        ):
            $arr['module'] = $this->module;
        endif;

        return $arr;
    }
}
