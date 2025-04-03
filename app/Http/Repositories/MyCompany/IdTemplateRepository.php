<?php

namespace App\Http\Repositories\MyCompany;

use Illuminate\Support\Facades\Auth;

use App\Models\CompanyIdTemplate;

use App\Http\Repositories\Base\CompanyRepository;
use App\Http\Repositories\Base\CompanyIdTemplateRepository as BaseRepository;

class IdTemplateRepository
{
    public function __construct()
    {
        $this->companyRepository = new CompanyRepository;
        $this->baseRepository = new BaseRepository;
    }

    public function store($request)
    {
        $company = Auth::user()->company();

        $newIdTemplate = $company->idTemplates()->save(
            $this->baseRepository->new($request, $company)
        );

        if ($request->input('options.with_left_approval_signature')):
            $newIdTemplate->update(
                $this->baseRepository->uploadSignature(
                    $newIdTemplate->left_signature,
                    $request->input('left_signature'),
                    'left_signature'
                )
            );
        endif;

        if ($request->input('options.with_right_approval_signature')):
            $newIdTemplate->update(
                $this->baseRepository->uploadSignature(
                    $newIdTemplate->right_signature,
                    $request->input('right_signature'),
                    'right_signature'
                )
            );
        endif;

        return $newIdTemplate;
    }

    public function show($request, $id)
    {
        $company = Auth::user()->company();

        $idTemplate = $this->companyRepository->isIdTemplateRelated($company, $id);

        return $idTemplate->toArrayMyCompanyIdTemplatesRelated(); 
    }

    public function edit($request, $id)
    {
        $company = Auth::user()->company();

        $idTemplate = $this->companyRepository->isIdTemplateRelated($company, $id);

        return $idTemplate->toArrayEdit(); 
    }

    public function update($request, $id)
    {
        $company = Auth::user()->company();

        $idTemplate = $this->companyRepository->isIdTemplateRelated($company, $id);

        $idTemplate->update(
            $this->baseRepository->update($request)
        );

        return (CompanyIdTemplate::find($idTemplate->id))->toArrayMyCompanyIdTemplatesRelated();
    }

    public function updateStatus($request, $id)
    {
        $company = Auth::user()->company();

        $idTemplate = $this->companyRepository->isIdTemplateRelated($company, $id);

        $idTemplate->update([
            'enabled' => !$idTemplate->enabled,
            'updated_by' => Auth::id()
        ]);

        return (CompanyIdTemplate::find($idTemplate->id))->toArrayMyCompanyIdTemplatesRelated();
    }

    public function updateLeftSignature($request, $id)
    {
        $company = Auth::user()->company();

        $idTemplate = $this->companyRepository->isIdTemplateRelated($company, $id);

        $idTemplate->update(
            $this->baseRepository->uploadSignature(
                $idTemplate->left_signature,
                $request->input('photo'),
                'left_signature'
            )
        );

        return (CompanyIdTemplate::find($idTemplate->id))->toArrayMyCompanyIdTemplatesRelated();
    }

    public function updateRightSignature($request, $id)
    {
        $company = Auth::user()->company();

        $idTemplate = $this->companyRepository->isIdTemplateRelated($company, $id);

        $idTemplate->update(
            $this->baseRepository->uploadSignature(
                $idTemplate->right_signature,
                $request->input('photo'),
                'right_signature'
            )
        );

        return (CompanyIdTemplate::find($idTemplate->id))->toArrayMyCompanyIdTemplatesRelated();
    }

    public function destroy($request, $id)
    {
        $company = Auth::user()->company();

        $idTemplate = $this->companyRepository->isIdTemplateRelated($company, $id);

        $this->baseRepository->isAllowedToDelete($idTemplate);

        $idTemplate->update([
            'name' => 'DELETED',
            'updated_by' => Auth::id()
        ]);

        $idTemplate->delete();

        return response([
            'message' => 'Data deleted successfully.'
        ], 200);
    }
}
?>