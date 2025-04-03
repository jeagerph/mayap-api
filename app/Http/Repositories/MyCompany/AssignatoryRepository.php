<?php

namespace App\Http\Repositories\MyCompany;

use Illuminate\Support\Facades\Auth;

use App\Models\CompanyAssignatory;

use App\Http\Repositories\Base\CompanyRepository;
use App\Http\Repositories\Base\CompanyAssignatoryRepository as BaseRepository;

class AssignatoryRepository
{
    public function __construct()
    {
        $this->companyRepository = new CompanyRepository;
        $this->baseRepository = new BaseRepository;
    }

    public function arrangeAssignatories($request)
    {
        $company = Auth::user()->company();

        $assignatories = $request->input('assignatories');

        foreach($assignatories as $row):

            $assignatory = CompanyAssignatory::find($row['id']);

            $assignatory->update([
                'order_no' => $row['order_no'],
                'updated_by' => Auth::id()
            ]);

        endforeach;

        $this->baseRepository->refreshOrderNo($company);

        return response([
            'message' => 'Assignatories have been re-arranged.'
        ], 200);
    }

    public function storeAssignatory($request)
    {
        $company = Auth::user()->company();

        $newAssignatory = $company->assignatories()->save(
            $this->baseRepository->new($request, $company)
        );

        return $newAssignatory;
    }

    public function updateAssignatory($request, $id)
    {
        $company = Auth::user()->company();

        $assignatory = $this->companyRepository->isAssignatoryRelated($company, $id);

        $assignatory->update(
            $this->baseRepository->update($request)
        );

        return (CompanyAssignatory::find($assignatory->id))->toArrayMyCompanyAssignatoriesRelated();
    }

    public function updateAssignatorySignature($request, $id)
    {
        $company = Auth::user()->company();

        $assignatory = $this->companyRepository->isAssignatoryRelated($company, $id);

        $filePath = $this->baseRepository->uploadSignature(
            $assignatory->signature_photo,
            $request,
            'signature/assignatory'
        );

        $assignatory->update([
            'signature_photo' => $filePath,
            'updated_by' => Auth::id()
        ]);

        return (CompanyAssignatory::find($assignatory->id))->toArrayMyCompanyAssignatoriesRelated();
    }

    public function updateAssignatoryStatus($request, $id)
    {
        $company = Auth::user()->company();

        $assignatory = $this->companyRepository->isAssignatoryRelated($company, $id);

        $assignatory->update([
            'enabled' => !$assignatory->enabled,
            'updated_by' => Auth::id()
        ]);

        return (CompanyAssignatory::find($assignatory->id))->toArrayMyCompanyAssignatoriesRelated();
    }

    public function destroyAssignatory($request, $id)
    {
        $company = Auth::user()->company();

        $assignatory = $this->companyRepository->isAssignatoryRelated($company, $id);

        $this->baseRepository->isAllowedToDelete($assignatory);

        $assignatory->update([
            'name' => 'DELETED',
            'updated_by' => Auth::id()
        ]);

        $assignatory->delete();

        $this->baseRepository->refreshOrderNo($company);

        return response([
            'message' => 'Data deleted successfully.'
        ], 200);
    }
}
?>