<?php

namespace App\Models;

use App\Models\Model;

class Account extends Model
{
    public $searchFields = [
        'fullName' => ':full_name',
        'username' => 'user:username'
    ];

    public $filterFields = [
        'accountType' => ':account_type_id',
        'userStatus' => 'user:locked',
    ];

    public $sortFields = [
        'fullName' => ':full_name',
        'created' => ':created_at',
    ];

    public $accountTypeOptions = [
        1 => 'ADMIN',
        2 => 'COMPANY'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function companyAccount()
    {
        return $this->hasOne('App\Models\CompanyAccount', 'account_id');
    }

    public function slug()
    {
        return $this->morphOne('App\Models\Slug', 'slug');
    }

    public function permissions()
    {
        return $this->hasMany('App\Models\AccountPermission');
    }

    public function activities()
    {
        return $this->hasMany('App\Models\Activity', 'audit_by');
    }

    public function toArray()
    {
        $arr = [
            'slug' => $this->slug,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'mobile_number' => $this->mobile_number,
            'photo' => $this->photo
                ? env('CDN_URL', '') . '/storage/' . $this->photo
                : '',
        ];

        if(request()->has('my-account-related')):
            $arr['account_type'] = [
                'id' => $this->account_type,
                'name' => $this->accountTypeOptions[$this->account_type]
            ];
            $arr['user'] = $this->user;
            $arr['permissions'] = $this->permissions;

            if($this->account_type == 2):
                $arr['company'] = $this->companyAccount->company
                    ? $this->companyAccount->company->name
                    : 'DELETED';
            endif;
        endif;

        if(
            request()->has('admin-accounts-related')
        ):
            $arr['user'] = $this->user;
            $arr['account_type'] = [
                'id' => $this->account_type,
                'name' => $this->accountTypeOptions[$this->account_type]
            ];
            
            if($this->account_type == 2):
                $arr['company'] = $this->companyAccount->company
                    ? $this->companyAccount->company->name
                    : 'DELETED';
            endif;
        endif;

        return $arr;
    }

    public function toArrayMyAccountRelated()
    {
        $arr = [
            'slug' => $this->slug,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'mobile_number' => $this->mobile_number,
            'photo' => $this->photo
                ? env('CDN_URL', '') . '/storage/' . $this->photo
                : '',
            'account_type' => [
                'id' => $this->account_type,
                'name' => $this->accountTypeOptions[$this->account_type]
            ],
            'user' => $this->user,
            'permissions' => $this->permissions,
        ];

        if($this->account_type == 2):
            
            $companyAccount = $this->companyAccount;

            $arr['company'] = $this->companyAccount->company
                ? $this->companyAccount->company->toArrayMyAccountRelated()
                : 'DELETED';

            
            $arr['company_position'] = $companyAccount->companyPosition;
        endif;

        return $arr;
    }

    public function toArrayEdit()
    {
        return [
            'account_type' => $this->account_type,
            'username' => $this->user->username,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'mobile_number' => $this->mobile_number,
        ];
    }

    public function toArrayAdminAccountsRelated()
    {
        $arr = [
            'slug' => $this->slug,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'mobile_number' => $this->mobile_number,
            'photo' => $this->photo
                ? env('CDN_URL', '') . '/storage/' . $this->photo
                : '',
            'account_type' => [
                'id' => $this->account_type,
                'name' => $this->accountTypeOptions[$this->account_type]
            ],
            'permissions' => $this->permissions,
            'user' => $this->user,
        ];

        return $arr;
    }

    public function toArrayAdminCompanyAccountsRelated()
    {
        return [
            'slug' => $this->slug,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'mobile_number' => $this->mobile_number,
            'photo' => $this->photo
                ? env('CDN_URL', '') . '/storage/' . $this->photo
                : '',
            'user' => $this->user,
            'permissions' => $this->permissions,
        ];
    }

    public function toArrayMyCompanyAccountsRelated()
    {
        return [
            'slug' => $this->slug,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'mobile_number' => $this->mobile_number,
            'photo' => $this->photo
                ? env('CDN_URL', '') . '/storage/' . $this->photo
                : '',
            'user' => $this->user,
            'permissions' => $this->permissions,
        ];
    }

    public function toArrayAdminDocumentTemplateOptions()
    {
        return [
            'full_name' => $this->full_name,
            'photo' => $this->photo
                ? env('CDN_URL', '') . '/storage/' . $this->photo
                : '',
        ];
    }

    public function toArrayAdminIdentificationTemplateOptions()
    {
        return [
            'full_name' => $this->full_name,
            'photo' => $this->photo
                ? env('CDN_URL', '') . '/storage/' . $this->photo
                : '',
        ];
    }

    public function toArrayMyCompanyOptions()
    {
        return [
            'full_name' => $this->full_name,
            'photo' => $this->photo
                ? env('CDN_URL', '') . '/storage/' . $this->photo
                : '',
        ];
    }

    public function toArrayCompanyDocumentOptions()
    {
        return [
            'full_name' => $this->full_name,
            'photo' => $this->photo
                ? env('CDN_URL', '') . '/storage/' . $this->photo
                : '',
        ];
    }

    public function toArrayAdminCompanyOptions()
    {
        return [
            'full_name' => $this->full_name,
            'photo' => $this->photo
                ? env('CDN_URL', '') . '/storage/' . $this->photo
                : '',
        ];
    }

    public function toArrayAdminCompanyInvoiceOptions()
    {
        return [
            'full_name' => $this->full_name,
            'photo' => $this->photo
                ? env('CDN_URL', '') . '/storage/' . $this->photo
                : '',
        ];
    }

    public function toArrayCreator()
    {
        return [
            'full_name' => $this->full_name,
        ];
    }
}
