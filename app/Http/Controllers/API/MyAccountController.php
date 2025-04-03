<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Slug;
use App\Models\Account;
use App\Models\Activity;

use App\Http\Repositories\MyAccount\MyAccountRepository as Repository;

use App\Http\Requests\MyAccount\UpdateRequest;
use App\Http\Requests\MyAccount\UpdatePhotoRequest;
use App\Http\Requests\MyAccount\UpdatePasswordRequest;

class MyAccountController extends Controller
{
    public function __construct()
    {
        $this->repository = new Repository;
    }

    public function index(Request $request)
    {
        $request->merge([
            'my-account-related' => true
        ]);

        return Auth::user()->account->toArrayMyAccountRelated();
    }

    public function update(Request $request, UpdateRequest $formRequest)
    {
        $request->merge([
            'my-account-related' => true
        ]);

        return $this->repository->update($formRequest);
    }

    public function updatePassword(Request $request, UpdatePasswordRequest $formRequest)
    {
        $request->merge([
            'my-account-related' => true,
        ]);

        return $this->repository->updatePassword($formRequest);
    }

    public function updatePhoto(Request $request, UpdatePhotoRequest $formRequest)
    {
        $request->merge([
            'my-account-related' => true,
        ]);

        return $this->repository->updatePhoto($formRequest);
    }

    public function showActivities(Request $request)
    {
        $filters = [
            'auditBy' => Auth::user()->account->id
        ];

        $sorts = [
            'created' => 'desc'
        ];

        $request->merge([
            'my-account-related' => true,
            'filter' => $this->handleQueries('filter', $request, $filters),
            'sort' => $this->handleQueries('sort', $request, $sorts),
        ]);

        $model = new Activity;

        return $model->build();
    }
}
