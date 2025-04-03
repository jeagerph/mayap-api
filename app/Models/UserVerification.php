<?php

namespace App\Models;

use App\Models\Model;

class UserVerification extends Model
{
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function toArray()
    {
        $arr = [
            'action' => $this->action,
            'token' => $this->token,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
        
        if(request()->has('forgot-password-related')):
            $arr['full_name'] = $this->user->account->full_name;
        endif;

        return $arr;
    }
}
