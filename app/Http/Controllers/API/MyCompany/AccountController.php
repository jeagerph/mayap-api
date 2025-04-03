<?php

namespace App\Http\Controllers\API\MyCompany;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Module;
use App\Models\CompanyAccount;
use App\Models\CompanyPosition;

use App\Http\Repositories\MyCompany\AccountRepository as Repository;

use App\Http\Requests\MyCompany\Account\StoreAccountRequest;
use App\Http\Requests\MyCompany\Account\UpdateAccountRequest;
use App\Http\Requests\MyCompany\Account\UpdatePhotoRequest;

class AccountController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth.permission');

        $this->repository = new Repository;
    }
    
    public function showAccounts(Request $request)
    {
        $company = Auth::user()->company();

        $filters = [
            'companyCode' => $company->slug->code,
        ];

        $sorts = [
            'created' => 'desc'
        ];

        $request->merge([
            'my-company-accounts-related' => true,
            'filter' => $this->handleQueries('filter', $request, $filters),
            'sort' => $this->handleQueries('sort', $request, $sorts),
            'all' => true
        ]);

        $model = new CompanyAccount;

        return $model->build();
    }

    public function downloadSummaryReport(Request $request)
    {
        $request->merge([
            'my-company-accounts-related' => true,
        ]);

        return $this->repository->downloadSummaryReport($request);
    }

    public function storeAccount(Request $request, StoreAccountRequest $formRequest)
    {
        $request->merge([
            'my-company-accounts-related' => true
        ]);

        return $this->repository->storeAccount($formRequest);
    }

    public function updateAccount(Request $request, UpdateAccountRequest $formRequest, $code)
    {
        $request->merge([
            'my-company-accounts-related' => true
        ]);

        return $this->repository->updateAccount($formRequest, $code);
    }

    public function updateAccountPhoto(Request $request, UpdatePhotoRequest $formRequest, $code)
    {
        $request->merge([
            'my-company-accounts-related' => true
        ]);

        return $this->repository->updateAccountPhoto($formRequest, $code);
    }

    public function updateAccountPermission(Request $request, $code)
    {
        $request->merge([
            'my-company-accounts-related' => true
        ]);
        
        return $this->repository->updateAccountPermission($request, $code);
    }

    public function destroyAccount(Request $request, $code)
    {
        $request->merge([
            'my-company-account-deletion' => true
        ]);

        return $this->repository->destroyAccount($request, $code);
    }

    public function showAccountSummaryTotal(Request $request, $code)
    {
        $request->merge([
            'my-company-accounts-related' => true,
        ]);

        $dates = $this->queryDates($request);

        return $this->repository->showAccountSummaryTotal($dates, $request, $code);
    }

    public function downloadAccountSummaryReport(Request $request, $code)
    {
        $request->merge([
            'my-company-accounts-related' => true,
        ]);

        return $this->repository->downloadAccountSummaryReport($request, $code);
    }

    public function positionOptions(Request $request)
    {
        $filters = [
            'enabled' => 1
        ];

        $request->merge([
            'my-company-account-options' => true,
            'filter' => $this->handleQueries('filter', $request, $filters),
            'all' => true
        ]);

        $model = new CompanyPosition;

        return $model->build();
    }

    public function moduleOptions(Request $request)
    {
        $filters = [
            'isAdmin' => 0
        ];

        $sorts = [
            'name' => 'asc'
        ];

        $request->merge([
            'my-company-account-options' => true,
            'filter' => $this->handleQueries('filter', $request, $filters),
            'sort' => $this->handleQueries('sort', $request, $sorts),
            'all' => true
        ]);

        $model = new Module;

        return $model->build();
    }
}
