<?php

namespace App\Models;

use App\Models\Model;

class CompanyIdTemplate extends Model
{
    public $searchFields = [];

    public $filterFields = [
        'companyCode' => 'slug:company_id',
    ];

    public $sortFields = [
        'name' => ':name',
        'created' => ':created_at',
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company');
    }

    public function toArray()
    {
        $arr = [
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,
            'enabled' => $this->enabled,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at
                ? $this->updated_at->toDateTimeString()
                : $this->created_at->toDateTimeString()
        ];

        if (request()->has('my-company-id-templates-related')):
            $arr['id'] = $this->id;
        endif;

        if (request()->has('beneficiary-options')):
            $arr['id'] = $this->id;
            $arr['view'] = $this->view
                ? json_decode($this->view)
                : null;
            $arr['options'] = $this->options
                ? json_decode($this->options)
                : null;
            $arr['content'] = $this->content
                ? json_decode($this->content)
                : null;
            $arr['approvals'] = $this->approvals
                ? json_decode($this->approvals)
                : null;
            $arr['left_signature'] = $this->left_signature;
            $arr['right_signature'] = $this->right_signature;
        endif;

        return $arr;
    }

    public function toArrayEdit()
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'enabled' => $this->enabled,
            'view' => $this->view
                ? json_decode($this->view)
                : null,
            'options' => $this->options
                ? json_decode($this->options)
                : null,
            'content' => $this->content
                ? json_decode($this->content)
                : null,
            'approvals' => $this->approvals
                ? json_decode($this->approvals)
                : null,
        ];
    }

    public function toArrayMyCompanyIdTemplatesRelated()
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,
            'enabled' => $this->enabled,
            'view' => $this->view
                ? json_decode($this->view)
                : null,
            'options' => $this->options
                ? json_decode($this->options)
                : null,
            'content' => $this->content
                ? json_decode($this->content)
                : null,
            'approvals' => $this->approvals
                ? json_decode($this->approvals)
                : null,
            'left_signature' => $this->left_signature
                ? env('CDN_URL', '') . '/storage/' . $this->left_signature
                : null,
            'right_signature' => $this->right_signature
                ? env('CDN_URL', '') . '/storage/' . $this->right_signature
                : null,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at
                ? $this->updated_at->toDateTimeString()
                : $this->created_at->toDateTimeString()
        ];
    }
}
