<?php

namespace App\Models;

use App\Models\Model;

class BeneficiaryDocument extends Model
{
    public $searchFields = [];

    public $filterFields = [
        'beneficiaryCode' => 'slug:beneficiary_id',
    ];

    public $sortFields = [
        'documentDate' => ':document_date',
        'created' => ':created_at'
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company');
    }

    public function beneficiary()
    {
        return $this->belongsTo('App\Models\Beneficiary');
    }

    public function expirationDate()
    {
        $expirationDate = now();

        $options = $this->options
            ? json_decode($this->options)
            : null;

        if ($options):
            if($options->expiration_date->default === 'months'):
                $expirationDate = now()->addMonths($options->expiration_date->months)->format('M d, Y');
            elseif($options->expiration_date->default === 'specific'):
                $expirationDate = (new \Carbon\Carbon($options->expiration_date->specific))->format('M d, Y');
            else:
                $expirationDate = now()->addMonths(12)->format('M d, Y');
            endif;
        endif;

        return $expirationDate;
    }

    public function issuanceDate()
    {
        $content = $this->content
            ? json_decode($this->content)
            : null;

        return property_exists($content, 'issuance_date')
            ? $content->issuance_date
            : null;
    }

    public function toArray()
    {
        $arr = [
            'code' => $this->code,
            'document_date' => $this->document_date,
            'name' => $this->name,
            'description' => $this->description,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at
                ? $this->updated_at->toDateTimeString()
                : $this->created_at->toDateTimeString()
        ];

        if (request()->has('beneficiary-documents-related')):

            $arr['id'] = $this->id;
            $arr['issuance_date'] = $this->issuanceDate();
            $arr['expiration_date'] = $this->expirationDate();

        endif;

        return $arr;
    }

    public function toArrayBeneficiaryDocumentsRelated()
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'document_date' => $this->document_date,
            'name' => $this->name,
            'description' => $this->description,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at
                ? $this->updated_at->toDateTimeString()
                : $this->created_at->toDateTimeString(),
            'issuance_date' => $this->issuanceDate(),
            'expiration_date' => $this->expirationDate(),
        ];
    }
}
