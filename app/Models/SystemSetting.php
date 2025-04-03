<?php

namespace App\Models;

use App\Models\Model;

class SystemSetting extends Model
{
    public function toArray()
	{
		return [
            'sms_service_status' => $this->sms_service_status,
            'call_service_status' => $this->call_service_status,
            'is_default' => $this->is_default
        ];
	}
}
