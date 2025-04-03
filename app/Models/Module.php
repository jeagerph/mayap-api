<?php

namespace App\Models;

use App\Models\Model;

class Module extends Model
{
    public $searchFields = [
        'name' => ':name'
    ];

    public $filterFields = [
        'isAdmin' => ':is_admin'
    ];

    public $sortFields = [
        'name' => ':name',
        'created' => ':created_at'
    ];
    
    public function toArray()
    {
        $arr = [
            'name' => $this->name,
            'route_name' => $this->route_name,
            'is_admin' => $this->is_admin
        ];

        if(
            request()->has('admin-accounts-related') ||
            request()->has('admin-account-options') ||
            request()->has('my-company-options') ||
            request()->has('my-company-related') ||
            request()->has('admin-company-options') ||
            request()->has('admin-company-accounts-related') ||
            request()->has('my-company-accounts-related') ||
            request()->has('my-company-account-options')
        ):
            $arr['id'] = $this->id;
        endif;

        return $arr;
    }
}
