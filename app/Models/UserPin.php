<?php

namespace App\Models;

use App\Models\Model;

class UserPin extends Model
{
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function toArray()
    {
        $arr = [
            'token' => $this->token,
            'otp' => $this->otp,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
        
        if(request()->has('otp-related')):
            $arr['full_name'] = $this->user->account->full_name;
        endif;

        return $arr;
    }

    public function toArrayAdminAccountsViewRelated()
    {
        return [
            'token' => $this->token,
            'otp' => $this->otp,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
