<?php

namespace App\Http\Controllers\API\MyCompany;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\CompanyClassification;

use App\Http\Repositories\MyCompany\ClassificationRepository as Repository;

use App\Http\Requests\MyCompany\Classification\StoreClassificationRequest;
use App\Http\Requests\MyCompany\Classification\UpdateClassificationRequest;

class ClassificationController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth.permission');

        $this->repository = new Repository;
    }

    public function showClassifications(Request $request)
    {
        $company = Auth::user()->company();

        $filters = [
            'companyCode' => $company->slug->code
        ];

        $sorts = [
            'name' => 'asc'
        ];

        $request->merge([
            'my-company-classifications-related' => true,
            'filter' => $this->handleQueries('filter', $request, $filters),
            'sort' => $this->handleQueries('sort', $request, $sorts),
            'all' => true
        ]);

        $model = new CompanyClassification;

        return $model->build();
    }

    public function storeClassification(Request $request, StoreClassificationRequest $formRequest)
    {
        $request->merge([
            'my-company-classifications-related' => true
        ]);

        return $this->repository->storeClassification($formRequest);
    }

    public function updateClassification(Request $request, UpdateClassificationRequest $formRequest, $id)
    {
        $request->merge([
            'my-company-classifications-related' => true
        ]);

        return $this->repository->updateClassification($formRequest, $id);
    }

    public function updateClassificationStatus(Request $request, $id)
    {
        $request->merge([
            'my-company-classifications-related' => true
        ]);

        return $this->repository->updateClassificationStatus($request, $id);
    }

    public function destroyClassification(Request $request, $id)
    {
        $request->merge([
            'my-company-classification-deletion' => true
        ]);

        return $this->repository->destroyClassification($request, $id);
    }
}
