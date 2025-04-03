<?php

namespace App\Http\Repositories\MyCompany;

use Illuminate\Support\Facades\Auth;

use App\Models\CompanyOfficerClassification;

use App\Http\Repositories\Base\CompanyRepository;
use App\Http\Repositories\Base\CompanyOfficerClassificationRepository as BaseRepository;

class OfficerClassificationRepository
{
    public function __construct()
    {
        $this->companyRepository = new CompanyRepository;
        $this->baseRepository = new BaseRepository;
    }

    public function store($request)
    {
        $company = Auth::user()->company();

        $newClassification = $company->officerClassifications()->save(
            $this->baseRepository->new($request, $company)
        );

        return $newClassification;
    }

    public function update($request, $id)
    {
        $company = Auth::user()->company();

        $classification = $this->companyRepository->isOfficerClassificationRelated($company, $id);

        $classification->update(
            $this->baseRepository->update($request)
        );

        return (CompanyOfficerClassification::find($classification->id))->toArrayMyCompanyOfficerClassificationsRelated();
    }

    public function updateStatus($request, $id)
    {
        $company = Auth::user()->company();

        $classification = $this->companyRepository->isOfficerClassificationRelated($company, $id);

        $classification->update([
            'enabled' => !$classification->enabled,
            'updated_by' => Auth::id()
        ]);

        return (CompanyOfficerClassification::find($classification->id))->toArrayMyCompanyOfficerClassificationsRelated();
    }

    public function destroy($request, $id)
    {
        $company = Auth::user()->company();

        $classification = $this->companyRepository->isOfficerClassificationRelated($company, $id);

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