<?php

namespace App\Http\Controllers\API\MyCompany;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\CompanyIdTemplate;
use App\Models\CompanyAssignatory;

use App\Http\Repositories\MyCompany\IdTemplateRepository as Repository;

use App\Http\Requests\MyCompany\IdTemplate\StoreRequest;
use App\Http\Requests\MyCompany\IdTemplate\UpdateRequest;
use App\Http\Requests\MyCompany\IdTemplate\UpdatePhotoRequest;

class IdTemplateController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth.permission');

        $this->repository = new Repository;
    }

    public function index(Request $request)
    {
        $company = Auth::user()->company();

        $filters = [
            'companyCode' => $company->slug->code
        ];

        $sorts = [
            'orderNo' => 'asc'
        ];

        $request->merge([
            'my-company-id-templates-related' => true,
            'filter' => $this->handleQueries('filter', $request, $filters),
            'sort' => $this->handleQueries('sort', $request, $sorts),
            'all' => true
        ]);

        $model = new CompanyIdTemplate;

        return $model->build();
    }

    public function store(Request $request, StoreRequest $formRequest)
    {
        $request->merge([
            'my-company-id-templates-related' => true
        ]);

        return $this->repository->store($formRequest);
    }

    public function show(Request $request, $id)
    {
        $request->merge([
            'my-company-id-templates-related' => true,
        ]);

        return $this->repository->show($request, $id);
    }

    public function edit(Request $request, $id)
    {
        $request->merge([
            'my-company-id-templates-related' => true,
        ]);

        return $this->repository->edit($request, $id);
    }

    public function update(Request $request, UpdateRequest $formRequest, $id)
    {
        $request->merge([
            'my-company-id-templates-related' => true
        ]);

        return $this->repository->update($formRequest, $id);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->merge([
            'my-company-id-templates-related' => true
        ]);

        return $this->repository->updateStatus($request, $id);
    }

    public function updateLeftSignature(Request $request, UpdatePhotoRequest $formRequest, $id)
    {
        $request->merge([
            'my-company-id-templates-related' => true,
        ]);

        return $this->repository->updateLeftSignature($formRequest, $id);
    }

    public function updateRightSignature(Request $request, UpdatePhotoRequest $formRequest, $id)
    {
        $request->merge([
            'my-company-id-templates-related' => true,
        ]);

        return $this->repository->updateRightSignature($formRequest, $id);
    }

    public function destroy(Request $request, $id)
    {
        $request->merge([
            'my-company-id-template-deletion' => true
        ]);

        return $this->repository->destroy($request, $id);
    }

    public function assignatoryOptions(Request $request)
    {
        $filters = [
            'enabled' => 1
        ];

        $sorts = [
            'orderNo' => 'asc'
        ];

        $request->merge([
            'my-company-id-template-options' => true,
            'filter' => $this->handleQueries('filter', $request, $filters),
            'sort' => $this->handleQueries('sort', $request, $sorts),
            'all' => true
        ]);

        $model = new CompanyAssignatory;

        return $model->build();
    }
}
