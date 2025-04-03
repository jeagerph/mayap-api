<?php

namespace App\Http\Repositories\Base;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

use App\Models\CompanyMapSetting;

class CompanyMapSettingRepository
{
    public $defaultData = [
        'api_key' => null,
        'latitude' => '14.8386',
        'longitude' => '120.2842',
    ];

    public function new($data)
    {
        return new CompanyMapSetting([
            'api_key' => $data['api_key'],
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'created_by' => Auth::id() ?: 1
        ]);
    }
}
?>