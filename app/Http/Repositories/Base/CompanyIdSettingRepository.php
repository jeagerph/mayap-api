<?php

namespace App\Http\Repositories\Base;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

use App\Models\CompanyIdSetting;

class CompanyIdSettingRepository
{
    public $defaultData = [
        'name' => 'COMPANY NAME',
        'address' => 'ADDRESS',
        'contact_no' => 'CONTACT NO.',
    ];

    public function new($data)
    {
        return new CompanyIdSetting([
            'name' => $data['name'],
            'address' => $data['address'],
            'contact_no' => $data['contact_no'],
            'created_by' => Auth::id() ?: 1
        ]);
    }
}
?>