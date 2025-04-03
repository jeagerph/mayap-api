<?php

namespace App\Http\Repositories\MyCompany;

use Illuminate\Support\Facades\Auth;

use App\Models\CompanyQuestionnaire;

use App\Http\Repositories\Base\CompanyRepository;
use App\Http\Repositories\Base\CompanyQuestionnaireRepository as BaseRepository;

class QuestionnaireRepository
{
    public function __construct()
    {
        $this->companyRepository = new CompanyRepository;
        $this->baseRepository = new BaseRepository;
    }

    public function storeQuestionnaire($request)
    {
        $company = Auth::user()->company();

        $newQuestionnaire = $company->questionnaires()->save(
            $this->baseRepository->new($request, $company)
        );

        return $newQuestionnaire;
    }

    public function updateQuestionnaire($request, $id)
    {
        $company = Auth::user()->company();

        $questionnaire = $this->companyRepository->isQuestionnaireRelated($company, $id);

        $questionnaire->update(
            $this->baseRepository->update($request)
        );

        return (CompanyQuestionnaire::find($questionnaire->id))->toArrayMyCompanyQuestionnairesRelated();
    }

    public function updateQuestionnaireStatus($request, $id)
    {
        $company = Auth::user()->company();

        $questionnaire = $this->companyRepository->isQuestionnaireRelated($company, $id);

        $questionnaire->update([
            'enabled' => !$questionnaire->enabled,
            'updated_by' => Auth::id()
        ]);

        return (CompanyQuestionnaire::find($questionnaire->id))->toArrayMyCompanyQuestionnairesRelated();
    }

    public function destroyQuestionnaire($request, $id)
    {
        $company = Auth::user()->company();

        $questionnaire = $this->companyRepository->isQuestionnaireRelated($company, $id);

        $this->baseRepository->isAllowedToDelete($questionnaire);

        $questionnaire->update([
            'question' => 'DELETED',
            'updated_by' => Auth::id()
        ]);

        $questionnaire->delete();

        $this->baseRepository->refreshOrderNo($company);

        return response([
            'message' => 'Data deleted successfully.'
        ], 200);
    }
}
?>