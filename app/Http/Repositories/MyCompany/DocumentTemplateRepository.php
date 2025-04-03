<?php

namespace App\Http\Repositories\MyCompany;

use Illuminate\Support\Facades\Auth;

use App\Models\CompanyDocumentTemplate;

use App\Http\Repositories\Base\CompanyRepository;
use App\Http\Repositories\Base\CompanyDocumentTemplateRepository as BaseRepository;

class DocumentTemplateRepository
{
    public function __construct()
    {
        $this->companyRepository = new CompanyRepository;
        $this->baseRepository = new BaseRepository;
    }

    public function store($request)
    {
        $company = Auth::user()->company();

        $newDocumentTemplate = $company->idTemplates()->save(
            $this->baseRepository->new($request, $company)
        );

        if ($request->input('options.with_left_approval_signature')):
            $newDocumentTemplate->update(
                $this->baseRepository->uploadSignature(
                    $newDocumentTemplate->left_signature,
                    $request->input('left_signature'),
                    'left_signature'
                )
            );
        endif;

        if ($request->input('options.with_right_approval_signature')):
            $newDocumentTemplate->update(
                $this->baseRepository->uploadSignature(
                    $newDocumentTemplate->right_signature,
                    $request->input('right_signature'),
                    'right_signature'
                )
            );
        endif;

        if ($request->input('header_border')):
            $newDocumentTemplate->update(
                $this->baseRepository->uploadBorder(
                    $newDocumentTemplate->header_border,
                    $request->input('header_border'),
                    'header_border'
                )
            );
        endif;

        return $newDocumentTemplate;
    }

    public function show($request, $id)
    {
        $company = Auth::user()->company();

        $documentTemplate = $this->companyRepository->isDocumentTemplateRelated($company, $id);

        return $documentTemplate->toArrayMyCompanyDocumentTemplatesRelated(); 
    }

    public function edit($request, $id)
    {
        $company = Auth::user()->company();

        $documentTemplate = $this->companyRepository->isDocumentTemplateRelated($company, $id);

        return $documentTemplate->toArrayEdit(); 
    }

    public function update($request, $id)
    {
        $company = Auth::user()->company();

        $documentTemplate = $this->companyRepository->isDocumentTemplateRelated($company, $id);

        $documentTemplate->update(
            $this->baseRepository->update($request)
        );

        return (CompanyDocumentTemplate::find($documentTemplate->id))->toArrayMyCompanyDocumentTemplatesRelated();
    }

    public function updateStatus($request, $id)
    {
        $company = Auth::user()->company();

        $documentTemplate = $this->companyRepository->isDocumentTemplateRelated($company, $id);

        $documentTemplate->update([
            'enabled' => !$documentTemplate->enabled,
            'updated_by' => Auth::id()
        ]);

        return (CompanyDocumentTemplate::find($documentTemplate->id))->toArrayMyCompanyDocumentTemplatesRelated();
    }

    public function updateLeftSignature($request, $id)
    {
        $company = Auth::user()->company();

        $documentTemplate = $this->companyRepository->isDocumentTemplateRelated($company, $id);

        $documentTemplate->update(
            $this->baseRepository->uploadSignature(
                $documentTemplate->left_signature,
                $request->input('photo'),
                'left_signature'
            )
        );

        return (CompanyDocumentTemplate::find($documentTemplate->id))->toArrayMyCompanyDocumentTemplatesRelated();
    }

    public function updateRightSignature($request, $id)
    {
        $company = Auth::user()->company();

        $documentTemplate = $this->companyRepository->isDocumentTemplateRelated($company, $id);

        $documentTemplate->update(
            $this->baseRepository->uploadSignature(
                $documentTemplate->right_signature,
                $request->input('photo'),
                'right_signature'
            )
        );

        return (CompanyDocumentTemplate::find($documentTemplate->id))->toArrayMyCompanyDocumentTemplatesRelated();
    }

    public function updateBorder($request, $id)
    {
        $company = Auth::user()->company();

        $documentTemplate = $this->companyRepository->isDocumentTemplateRelated($company, $id);

        $documentTemplate->update(
            $this->baseRepository->uploadBorder(
                $documentTemplate->header_border,
                $request->input('photo'),
                'header_border'
            )
        );

        return (CompanyDocumentTemplate::find($documentTemplate->id))->toArrayMyCompanyDocumentTemplatesRelated();
    }

    public function destroy($request, $id)
    {
        $company = Auth::user()->company();

        $documentTemplate = $this->companyRepository->isDocumentTemplateRelated($company, $id);

        $this->baseRepository->isAllowedToDelete($documentTemplate);

        $documentTemplate->update([
            'name' => 'DELETED',
            'updated_by' => Auth::id()
        ]);

        $documentTemplate->delete();

        return response([
            'message' => 'Data deleted successfully.'
        ], 200);
    }
}
?>