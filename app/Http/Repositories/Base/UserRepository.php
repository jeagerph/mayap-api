<?php

namespace App\Http\Repositories\Base;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use App\Models\User;

use App\Http\Repositories\Base\PassportRepository;

class UserRepository
{
    public function __construct()
    {
        $this->passportRepository = new PassportRepository;
    }

    public function store($data)
	{
        $model = new User;
        $model->username = $data['username'];
        $model->password = bcrypt('@'.$data['username']);
        $model->code = Str::random(6);
        $model->pin = randomNumbers(4);
        $model->locked = 0;
        $model->created_by = Auth::id();
        $model->save();

        return $model;
    }

    public function credentialPermission($username, $password)
    {
        if(in_array(Auth::user()->account->account_type, [1,2])) return true;

        $user = User::where('username', $username)->whereNull('deleted_at')->first();

        if(!$user) return abort('403', 'Forbidden! Credential permission is required.');

        $accountTypeId = $user->account->account_type;

		return in_array($accountTypeId, [1,2]) && $this->passportRepository->validatePassword($user, $password);
    }

    public function pinPermission($pin)
    {
        if(Auth::user()->account->account_type == 1) return true;

        $user = User::where('pin', $pin)->first();

        if(!$user) return abort('403', 'Invalid PIN.');

        $accountTypeId = $user->account->account_type;

		return in_array($accountTypeId, [1,2]);
    }
}
?>