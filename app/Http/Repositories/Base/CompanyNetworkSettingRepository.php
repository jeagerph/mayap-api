<?php

namespace App\Http\Repositories\Base;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

use App\Models\CompanyNetworkSetting;

class CompanyNetworkSettingRepository
{
    public $defaultData = [
        'master_degree_enabled' => 1,
        'master_degree_points' => 50,

        'first_degree_enabled' => 1,
        'first_degree_points' => 40,

        'second_degree_enabled' => 1,
        'second_degree_points' => 30,

        'third_degree_enabled' => 1,
        'third_degree_points' => 20,

        'fourth_degree_enabled' => 1,
        'fourth_degree_points' => 10,

        'fifth_degree_enabled' => 1,
        'fifth_degree_points' => 5,
    ];

    public function new($data)
    {
        return new CompanyNetworkSetting([
            'master_degree_enabled' => $data['master_degree_enabled'],
            'master_degree_points' => $data['master_degree_points'],
            'first_degree_enabled' => $data['first_degree_enabled'],
            'first_degree_points' => $data['first_degree_points'],
            'second_degree_enabled' => $data['second_degree_enabled'],
            'second_degree_points' => $data['second_degree_points'],
            'third_degree_enabled' => $data['third_degree_enabled'],
            'third_degree_points' => $data['third_degree_points'],
            'fourth_degree_enabled' => $data['fourth_degree_enabled'],
            'fourth_degree_points' => $data['fourth_degree_points'],
            'fifth_degree_enabled' => $data['fifth_degree_enabled'],
            'fifth_degree_points' => $data['fifth_degree_points'],
            'created_by' => Auth::id() ?: 1
        ]);
    }
}
?>