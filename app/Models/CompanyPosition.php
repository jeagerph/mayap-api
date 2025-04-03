<?php

namespace App\Models;

use App\Models\Model;

class CompanyPosition extends Model
{
    public $searchFields = [
        'name' => ':name',
        'description' => ':description',
    ];

    public $filterFields = [
        'enabled' => ':enabled',
    ];

    public $sortFields = [
        'name' => ':name',
        'enabled' => ':enabled',
    ];

    public $rangeFields = [];

    public function toArray()
    {
        $arr = [
            'name' => $this->name,
            'description' => $this->description,
            'enabled' => $this->enabled,
        ];

        if(
            request()->has('admin-company-options') ||
            request()->has('admin-companies-related') ||
            request()->has('my-company-options') ||
            request()->has('my-company-related') ||
            request()->has('my-company-account-options')
        ):
            $arr['id'] = $this->id;
        endif;

        return $arr;
    }

    public function toArrayAdminCompanyAccountsRelated()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }

    public function toArrayMyCompanyAccountsRelated()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}
