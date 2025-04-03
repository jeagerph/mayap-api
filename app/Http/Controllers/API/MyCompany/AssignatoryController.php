<?php

namespace App\Http\Controllers\API\MyCompany;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\CompanyAssignatory;

use App\Http\Repositories\MyCompany\AssignatoryRepository as Repository;

use App\Http\Requests\MyCompany\Assignatory\ArrangeRequest;
use App\Http\Requests\MyCompany\Assignatory\StoreRequest;
use App\Http\Requests\MyCompany\Assignatory\UpdateRequest;
use App\Http\Requests\MyCompany\Assignatory\UpdatePhotoRequest;

class AssignatoryController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth.permission');

        $this->repository = new Repository;
    }

    public function showAssignatories(Request $request)
    {
        $company = Auth::user()->company();

        $filters = [
            'companyCode' => $company->slug->code
        ];

        $sorts = [
            'orderNo' => 'asc'
        ];

        $request->merge([
            'my-company-assignatories-related' => true,
            'filter' => $this->handleQueries('filter', $request, $filters),
            'sort' => $this->handleQueries('sort', $request, $sorts),
            'all' => true
        ]);

        $model = new CompanyAssignatory;

        return $model->build();
    }

    public function arrangeAssignatories(Request $request, ArrangeRequest $formRequest)
    {
        $request->merge([
            'my-company-assignatories-related' => true,
        ]);

        return $this->repository->arrangeAssignatories($formRequest);
    }

    public function storeAssignatory(Request $request, StoreRequest $formRequest)
    {
        $request->merge([
            'my-company-assignatories-related' => true
        ]);

        return $this->repository->storeAssignatory($formRequest);
    }

    public function updateAssignatory(Request $request, UpdateRequest $formRequest, $id)
    {
        $request->merge([
            'my-company-assignatories-related' => true
        ]);

        return $this->repository->updateAssignatory($formRequest, $id);
    }

    public function updateAssignatorySignature(Request $request, UpdatePhotoRequest $formRequest, $id)
    {
        $request->merge([
            'my-company-assignatories-related' => true
        ]);

        return $this->repository->updateAssignatorySignature($formRequest, $id);
    }

    public function updateAssignatoryStatus(Request $request, $id)
    {
        $request->merge([
            'my-company-assignatories-related' => true
        ]);

        return $this->repository->updateAssignatoryStatus($request, $id);
    }

    public function destroyAssignatory(Request $request, $id)
    {
        $request->merge([
            'my-company-assignatory-deletion' => true
        ]);

        return $this->repository->destroyAssignatory($request, $id);
    }
}
