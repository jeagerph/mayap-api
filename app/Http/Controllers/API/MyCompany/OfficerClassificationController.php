<?php

namespace App\Http\Controllers\API\MyCompany;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\CompanyOfficerClassification;

use App\Http\Repositories\MyCompany\OfficerClassificationRepository as Repository;

use App\Http\Requests\MyCompany\OfficerClassification\StoreRequest;
use App\Http\Requests\MyCompany\OfficerClassification\UpdateRequest;

class OfficerClassificationController extends Controller
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
            'companyCode' => $company->slug->code,
        ];

        $sorts = [
            'name' => 'asc'
        ];

        $request->merge([
            'my-company-officer-classifications-related' => true,
            'filter' => $this->handleQueries('filter', $request, $filters),
            'sort' => $this->handleQueries('sort', $request, $sorts),
            'all' => true
        ]);

        $model = new CompanyOfficerClassification;

        return $model->build();
    }

    public function store(Request $request, StoreRequest $formRequest)
    {
        $request->merge([
            'my-company-officer-classifications-related' => true
        ]);

        return $this->repository->store($formRequest);
    }

    public function update(Request $request, UpdateRequest $formRequest, $id)
    {
        $request->merge([
            'my-company-officer-classifications-related' => true
        ]);

        return $this->repository->update($formRequest, $id);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->merge([
            'my-company-officer-classifications-related' => true
        ]);

        return $this->repository->updateStatus($request, $id);
    }

    public function destroy(Request $request, $id)
    {
        $request->merge([
            'my-company-officer-classification-deletion' => true
        ]);

        return $this->repository->destroy($request, $id);
    }
}
