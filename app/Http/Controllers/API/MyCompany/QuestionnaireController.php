<?php

namespace App\Http\Controllers\API\MyCompany;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\CompanyQuestionnaire;

use App\Http\Repositories\MyCompany\QuestionnaireRepository as Repository;

use App\Http\Requests\MyCompany\Questionnaire\StoreRequest;
use App\Http\Requests\MyCompany\Questionnaire\UpdateRequest;

class QuestionnaireController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth.permission');

        $this->repository = new Repository;
    }

    public function showQuestionnaires(Request $request)
    {
        $company = Auth::user()->company();

        $filters = [
            'companyCode' => $company->slug->code
        ];

        $sorts = [
            'orderNo' => 'asc'
        ];

        $request->merge([
            'my-company-questionnaires-related' => true,
            'filter' => $this->handleQueries('filter', $request, $filters),
            'sort' => $this->handleQueries('sort', $request, $sorts),
            'all' => true
        ]);

        $model = new CompanyQuestionnaire;

        return $model->build();
    }

    public function storeQuestionnaire(Request $request, StoreRequest $formRequest)
    {
        $request->merge([
            'my-company-questionnaires-related' => true
        ]);

        return $this->repository->storeQuestionnaire($formRequest);
    }

    public function updateQuestionnaire(Request $request, UpdateRequest $formRequest, $id)
    {
        $request->merge([
            'my-company-questionnaires-related' => true
        ]);

        return $this->repository->updateQuestionnaire($formRequest, $id);
    }

    public function updateQuestionnaireStatus(Request $request, $id)
    {
        $request->merge([
            'my-company-questionnaires-related' => true
        ]);

        return $this->repository->updateQuestionnaireStatus($request, $id);
    }

    public function destroyQuestionnaire(Request $request, $id)
    {
        $request->merge([
            'my-company-questionnaire-deletion' => true
        ]);

        return $this->repository->destroyQuestionnaire($request, $id);
    }
}
