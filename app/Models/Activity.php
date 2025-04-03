<?php

namespace App\Models;

use App\Models\Model;

class Activity extends Model
{
    public $searchFields = [];

    public $filterFields = [
        'activityType' => ':module_type',
        'activityId' => ':module_id',
        'auditBy' => ':audit_by'
    ];

    public $sortFields = [
        'created' => ':created_at'
    ];

    public $actionOptions = [
        1 => 'created',
        2 => 'updated',
        3 => 'deleted'
    ];

    public function module()
    {
        return $this->morphTo();
    }

    public function auditBy()
    {
        return $this->belongsTo('App\Models\Account', 'audit_by');
    }

    public function auditor()
    {
        return [
            'id' => $this->auditBy
                ? $this->audit_by
                : null,
            'name' => $this->auditBy
                ? $this->auditBy->full_name
                : 'DELETED USER'
        ];
    }
    
    public function toArray()
    {
        $arr = [
            'description' => $this->description,
            'action' => [
                'id' => $this->action,
                'name' => $this->actionOptions[$this->action]
            ],
            'data' => $this->data
                ? json_decode($this->data)
                : [],
            'audit_by' => $this->auditor(),
            'audit_at' => $this->audit_at
        ];

        return $arr;
    }
}
