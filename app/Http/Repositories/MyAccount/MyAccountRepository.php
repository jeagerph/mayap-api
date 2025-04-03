<?php

namespace App\Http\Repositories\MyAccount;

use Illuminate\Support\Facades\Auth;

use App\Models\Slug;
use App\Models\Account;

use App\Http\Repositories\Base\SlugRepository;
use App\Http\Repositories\Base\UserRepository;
use App\Http\Repositories\Base\AccountRepository as Repository;

class MyAccountRepository
{
    public function __construct()
    {
        $this->accountRepository = new Repository;
        $this->userRepository = new UserRepository;
        $this->slugRepository = new SlugRepository;
    }

    public function update($request)
    {
        $account = Auth::user()->account;

        $account->update(
            $this->accountRepository->update([
                'account_type' => $account->account_type,
                'full_name' => $request->input('full_name'),
                'email' => $request->input('email'),
                'mobile_number' => $request->input('mobile_number'),
                'updated_by' => Auth::id()
            ])
        );

        $user = $account->user;

        if($request->input('username') != $user->username):
            $user->update([
                'username' => $request->input('username'),
                'updated_by' => Auth::id()
            ]);
        endif;

        $account->slug()->update(
            $this->slugRepository->update([
                'name' => $request->input('full_name') . ' Account',
                'code' => $account->slug->code,
            ])
        );

        return Account::find($account->id);
    }

    public function updatePhoto($request)
    {
        $account = Auth::user()->account;

        $account->update(
            $this->accountRepository->updatePhoto($account->photo, $request)
        );

        return Account::find($account->id);
    }

    public function updatePassword($request)
    {
        $account = Auth::user()->account;

        $account->user()->update([
            'password' => bcrypt($request->input('password')),
            'updated_by' => Auth::id()
        ]);

        return Account::find($account->id);
    }
}
?>