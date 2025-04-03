<?php

namespace App\Models\Diafaan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use DB;

use App\Models\SystemSetting;

class OtpSMS extends Model
{
    protected $connection = 'diafaanOtpMysql';

    protected $table = 'MessageOut';

    public function setConfig()
    {
        $setting = SystemSetting::where('is_default', 1)->first();

        Config::set("database.connections.diafaanOtpMysql", [
            'driver' => 'mysql',
            "host" => $setting->diafaan_otp_host,
            "database" => $setting->diafaan_otp_db,
            "username" => $setting->diafaan_otp_username,
            "password" => $setting->diafaan_otp_password,
            "port" => $setting->diafaan_otp_port,
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ]);
    }
}
