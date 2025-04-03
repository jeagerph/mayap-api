<?php

namespace App\Models;

use App\Models\Model;

class CompanyQuestionnaire extends Model
{
    public $searchFields = [
        'question' => ':question',
        'description' => ':description',
    ];

    public $filterFields = [
        'companyCode' => 'slug:company_id',
        'enabled' => ':enabled',
    ];

    public $sortFields = [
        'orderNo' => ':order_no',
        'enabled' => ':enabled',
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company');
    }

    public function toArray()
    {
        $arr = [
            'question' => $this->question,
            'description' => $this->description,
            'enabled' => $this->enabled,
        ];

        if(
            request()->has('my-company-questionnaires-related') ||
            request()->has('beneficiary-options')
        ):
            $arr['id'] = $this->id;
        endif;

        return $arr;
    }

    public function toArrayMyCompanyQuestionnairesRelated()
    {
        return [
            'id' => $this->id,
            'question' => $this->question,
            'description' => $this->description,
            'enabled' => $this->enabled,
        ];
    }
}
