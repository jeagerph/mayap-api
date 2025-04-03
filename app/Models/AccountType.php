<?php

namespace App\Models;

use App\Models\Model;

class AccountType extends Model
{
    public $searchFields = [
        'name' => ':name'
    ];

    public $sortFields = [
        'name' => ':name',
        'created' => ':created_at'
    ];

    public function permissions()
    {
        return $this->hasMany('App\Models\AccountTypePermission');
    }

    public function toArray()
    {
        $arr = [
            'name' => $this->name,
            'description' => $this->description,
        ];

        if(request()->has('my-account-related')):
            $arr['id'] = $this->id;
        endif;

        if(request()->has('admin-account-options')):
            $arr['id'] = $this->id;
        endif;

        if(request()->has('admin-accounts-list-related')):
            $arr['id'] = $this->id;
        endif;

        return $arr;
    }

    public function toArrayEdit()
    {
        return [
            'name' => $this->name,
            'description' => $this->description
        ];
    }

    public function toArrayView()
    {
        return [
            'name' => $this->name,
            'description' => $this->description
        ];
    }
}
