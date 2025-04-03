<?php

namespace App\Http\Repositories\Base;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use App\Models\UserVerification;

class UserVerificationRepository
{
    public function new($data)
	{
        return new UserVerification([
            'action' => $data['action'], 
            'token' => Str::random(32),
            'code' => randomNumbers(6),
            'created_by' => Auth::id() ?: 1
        ]);
	}
}
?>