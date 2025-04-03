<?php

namespace App\Http\Repositories\Administration;

use Illuminate\Support\Facades\Auth;

use App\Models\Slug;
use App\Models\Account;

use App\Http\Repositories\Base\SlugRepository;
use App\Http\Repositories\Base\UserRepository;
use App\Http\Repositories\Base\AccountPermissionRepository;
use App\Http\Repositories\Base\AccountRepository as Repository;

class AccountRepository
{
    public function __construct()
    {
        $this->accountRepository = new Repository;
        $this->userRepository = new UserRepository;
        $this->slugRepository = new SlugRepository;
        $this->permissionRepository = new AccountPermissionRepository;
    }

    public function store($request)
	{
        $newUser = $this->userRepository->store($request);

        $newAccount = $newUser->account()->save(
            $this->accountRepository->new($request)
        );

        $newAccount->slug()->save(
            $this->slugRepository->new(
                $request->input('full_name') . ' Account'
            )
        );

        return $newAccount;
    }

    public function update($request, $code)
    {
        $model = Slug::findCodeOrDie($code);

        $account = $model->slug;

        $account->update(
            $this->accountRepository->update($request)
        );

        $account->user()->update([
            'username' => $request->input('username'),
            'updated_by' => Auth::id()
        ]);

        $account->slug()->update(
            $this->slugRepository->update([
                'name' => strtoupper($request->input('full_name')) . ' Account',
                'code' => $code,
            ])
        );

        return (Account::find($account->id))->toArrayAdminAccountsRelated();
    }

    public function updatePhoto($request, $code)
    {
        $model = Slug::findCodeOrDie($code);

        $account = $model->slug;

        $account->update(
            $this->accountRepository->updatePhoto($account->photo, $request)
        );

        return (Account::find($account->id))->toArrayAdminAccountsRelated();
    }

    public function updatePassword($request, $code)
    {
        $model = Slug::findCodeOrDie($code);

        $account = $model->slug;

        $account->user()->update([
            'password' => bcrypt('@'.$account->user->username),
            'updated_by' => Auth::id()
        ]);

        return (Account::find($account->id))->toArrayAdminAccountsRelated();
    }

    public function updatePermission($request, $code)
    {
        $model = Slug::findCodeOrDie($code);

        $account = $model->slug;

        if($account->account_type === 1) return abort(403, 'You cannot update this resource data.');

        $permission = $account->permissions()->where('module_id', $request->input('module_id'))->first();

        if($permission):

            $permission->update([
                'access' => $request->input('access'),
                'index' => $request->input('index'),
                'store' => $request->input('store'),
                'update' => $request->input('update'),
                'destroy' => $request->input('destroy'),
                'updated_by' => Auth::id()
            ]);

            return $permission;
        else:
            $newPermission = $account->permissions()->save(
                $this->permissionRepository->new([
                    'module_id' => $request->input('module_id'),
                    'access' => $request->input('access'),
                    'index' => $request->input('index'),
                    'store' => $request->input('store'),
                    'update' => $request->input('update'),
                    'destroy' => $request->input('destroy'),
                ])
            );
            
            return $newPermission;
        endif;
    }
}
?>