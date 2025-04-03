<?php

namespace App\Models\Diafaan;

use Illuminate\Database\Eloquent\Model;

class DefaultSMS extends Model
{
    protected $connection = 'diafaanDefaultMysql';

    protected $table = 'MessageOut';
}
