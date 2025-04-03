<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Laravel\Passport\HasApiTokens;

use App\Traits\QueryBuilder;
use App\Traits\SoftDeletes;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens, QueryBuilder, SoftDeletes;

    protected $fillable = [
        'username', 'password', 'locked', 'code'
    ];

    protected $hidden = [
        'password'
    ];

    public function findForPassport($username) 
	{
        return $this->where('username', $username)->first();
    }

    public function account()
    {
        return $this->hasOne('App\Models\Account');
    }

    public function company()
    {
        return $this->account->companyAccount->company;
    }

    public function pins()
    {
        return $this->hasMany('App\Models\UserPin');
    }

    public function verifications()
    {
        return $this->hasMany('App\Models\UserVerification');
    }

    public function toArray()
    {
        $arr = [
            'code' => $this->code,
            'username' => $this->username,
            'locked' => $this->locked,
            'pin' => $this->pin,
        ];

        if(false):
            $arr['account'] = $this->account;
        endif;

        return $arr;
    }
}
