<?php

namespace App\Models;

use App\Models\Model;

class CompanyAssignatory extends Model
{
    public $searchFields = [
        'name' => ':name',
        'position' => ':position',
    ];

    public $filterFields = [
        'companyCode' => 'slug:company_id',
        'enabled' => ':enabled',
    ];

    public $sortFields = [
        'orderNo' => ':order_no',
        'name' => ':name',
        'enabled' => ':enabled',
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company');
    }

    public function toArray()
    {
        $arr = [
            'order_no' => $this->order_no,
            'name' => $this->name,
            'position' => $this->position,
            'signature_photo' => $this->signature_photo
                ? env('CDN_URL', '') . '/storage/' . $this->signature_photo
                : '',
            'enabled' => $this->enabled,
        ];

        if(
            request()->has('my-company-assignatories-related') ||
            request()->has('my-company-id-template-options') ||
            request()->has('my-company-document-template-options')
        ):
            $arr['id'] = $this->id;
        endif;

        return $arr;
    }

    public function toArrayMyCompanyAssignatoriesRelated()
    {
        return [
            'id' => $this->id,
            'order_no' => $this->order_no,
            'name' => $this->name,
            'position' => $this->position,
            'signature_photo' => $this->signature_photo
                ? env('CDN_URL', '') . '/storage/' . $this->signature_photo
                : '',
            'enabled' => $this->enabled,
        ];
    }
}
