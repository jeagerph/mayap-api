<?php

namespace App\Http\Repositories\MyCompany;

use Illuminate\Support\Facades\Auth;

use App\Models\CompanyClassification;

use App\Http\Repositories\Base\CompanyRepository;
use App\Http\Repositories\Base\CompanyClassificationRepository as BaseRepository;

class ClassificationRepository
{
    public function __construct()
    {
        $this->companyRepository = new CompanyRepository;
        $this->baseRepository = new BaseRepository;
    }

    public function storeClassification($request)
    {
        $company = Auth::user()->company();

        $newClassification = $company->classifications()->save(
            $this->baseRepository->new($request, $company)
        );

        return $newClassification;
    }

    public function updateClassification($request, $id)
    {
        $company = Auth::user()->company();

        $classification = $this->companyRepository->isClassificationRelated($company, $id);

        $classification->update(
            $this->baseRepository->update($request)
        );

        return (CompanyClassification::find($classification->id))->toArrayMyCompanyClassificationsRelated();
    }

    public function updateClassificationStatus($request, $id)
    {
        $company = Auth::user()->company();

        $classification = $this->companyRepository->isClassificationRelated($company, $id);

        $classification->update([
            'enabled' => !$classification->enabled,
            'updated_by' => Auth::id()
        ]);

        return (CompanyClassification::find($classification->id))->toArrayMyCompanyClassificationsRelated();
    }

    public function destroyClassification($request, $id)
    {
        $company = Auth::user()->company();

        $classification = $this->companyRepository->isClassificationRelated($company, $id);

        $this->baseRepository->isAllowedToDelete($classification);

        $classification->update([
            'name' => 'DELETED',
            'updated_by' => Auth::id()
        ]);

        $classification->delete();

        return response([
            'message' => 'Data deleted successfully.'
        ], 200);
    }
}
?>