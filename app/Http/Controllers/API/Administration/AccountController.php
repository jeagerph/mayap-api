<?php

namespace App\Http\Controllers\API\Administration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Slug;
use App\Models\Account;
use App\Models\Activity;
use App\Models\AccountPermission;
use App\Models\AccountType;
use App\Models\Module;

use App\Http\Requests\Administration\Account\StoreRequest;
use App\Http\Requests\Administration\Account\UpdateRequest;
use App\Http\Requests\Administration\Account\UpdatePhotoRequest;

use App\Http\Repositories\Administration\AccountRepository as Repository;

class AccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.permission');

        $this->repository = new Repository;
    }

    public function index(Request $request)
    {
        $sorts = [
            'created' => 'desc'
        ];

        $request->merge([
            'admin-accounts-related' => true,
            'sort' => $this->handleQueries('sort', $request, $sorts)
        ]);

        $model = new Account;

        return $model->build();
    }

    public function show(Request $request, $code)
    {
        $request->merge([
            'admin-accounts-related' => true
        ]);

        $model = Slug::findCodeOrDie($code);

        return $model->slug->toArrayAdminAccountsRelated();
    }

    public function edit(Request $request, $code)
    {
        $request->merge([
            'admin-accounts-related' => true
        ]);

        $model = Slug::findCodeOrDie($code);

        return $model->slug->toArrayEdit();
    }

    public function store(Request $request, StoreRequest $formRequest)
    {
        $request->merge([
            'admin-accounts-related' => true
        ]);

        return $this->repository->store($formRequest);
    }

    public function update(Request $request, UpdateRequest $formRequest, $code)
    {
        $request->merge([
            'admin-accounts-related' => true
        ]);

        return $this->repository->update($formRequest, $code);
    }

    public function updatePassword(Request $request, $code)
    {
        $request->merge([
            'admin-accounts-related' => true,
        ]);

        return $this->repository->updatePassword($request, $code);
    }

    public function updatePhoto(Request $request, UpdatePhotoRequest $formRequest, $code)
    {
        $request->merge([
            'admin-accounts-related' => true,
        ]);

        return $this->repository->updatePhoto($request, $code);
    }

    public function updateStatus(Request $request, $code)
    {
        $request->merge([
            'admin-accounts-related' => true,
        ]);

        $model = Slug::findCodeOrDie($code);

        $account = $model->slug;

        $user = $account->user;

        $user->update([
            'locked' => !$user->locked,
            'updated_by' => Auth::id()
        ]);

        return (Account::find($account->id))->toArrayView();
    }

    public function destroy(Request $request, $code)
    {
        $request->merge([
            'admin-account-deletion' => true
        ]);

        return abort(403, 'You are not allowed to delete this resource data.');

        $model = Account::findCodeOrDie($code);

        $model->delete();

        return response([
            'message' => 'Data deleted successfully.'
        ], 200);
    }

    public function showActivities(Request $request, $code)
    {
        $module = Slug::findCodeOrDie($code);

        $filters = [
            'auditBy' => $module->slug_id
        ];

        $sorts = [
            'created' => 'desc'
        ];

        $request->merge([
            'admin-accounts-related' => true,
            'filter' => $this->handleQueries('filter', $request, $filters),
            'sort' => $this->handleQueries('sort', $request, $sorts),
        ]);

        $model = new Activity;

        return $model->build();
    }

    public function showPermissions(Request $request, $code)
    {
        $filters = [
            'accountCode' => $code
        ];

        $request->merge([
            'admin-accounts-related' => true,
            'filter' => $this->handleQueries('filter', $request, $filters)
        ]);

        $model = new AccountPermission;

        return $model->build();
    }

    public function updatePermission(Request $request, $code)
    {
        $request->merge([
            'admin-accounts-related' => true
        ]);
        
        return $this->repository->updatePermission($request, $code);
    }

    public function showAccountModules(Request $request, $code)
    {
        $accountSlug = Slug::findCodeOrDie($code);
        $account = $accountSlug->slug;

        $sorts = [
            'name' => 'asc',
        ];
        
        $request->merge([
            'admin-account-options' => true,
            'all' => true,
            'sort' => $this->handleQueries('sort', $request, $sorts)
        ]);

        if($account->account_type == 2):
            $request->merge([
                'filter' => $this->handleQueries('filter', $request, [
                    'isAdmin' => 0
                ])
            ]);
        endif;

        $model = new Module;

        return $model->build();
    }

    public function showLatestUserPin(Request $request, $code)
    {
        $request->merge([
            'admin-accounts-view-related' => true,
        ]);

        $model = Slug::findCodeOrDie($code);

        $account = $model->slug;

        $user = $account->user;

        $userPin = $user->pins()->latest()->first();

        if ($userPin) return $userPin->toArrayAdminAccountsViewRelated();

        return null;
    }

    public function moduleOptions(Request $request)
    {
        $sorts = [
            'name' => 'asc',
        ];
        
        $request->merge([
            'admin-account-options' => true,
            'all' => true,
            'sort' => $this->handleQueries('sort', $request, $sorts)
        ]);

        $model = new Module;

        return $model->build();
    }
}
