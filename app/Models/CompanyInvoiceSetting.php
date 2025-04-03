<?php

namespace App\Models;

use App\Models\Model;

class CompanyInvoiceSetting extends Model
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
            'domain_hosting' => $this->domain_hosting,
            'branding_sms' => $this->branding_sms,
            'regular_sms' => $this->regular_sms,
            'virtual_storage' => $this->virtual_storage,
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
            'domain_hosting' => $this->domain_hosting,
            'branding_sms' => $this->branding_sms,
            'regular_sms' => $this->regular_sms,
            'virtual_storage' => $this->virtual_storage,
            'show_left_representative' => $this->show_left_representative,
            'left_representative_name' => $this->left_representative_name,
            'left_representative_position' => $this->left_representative_position,
            'show_right_representative' => $this->show_right_representative,
            'right_representative_name' => $this->right_representative_name,
            'right_representative_position' => $this->right_representative_position,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at
                ? $this->updated_at->toDateTimeString()
                : $this->created_at->toDateTimeString()
        ];
    }
}
