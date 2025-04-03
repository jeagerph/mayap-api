<?php

namespace App\Http\Repositories\Base;

use Illuminate\Support\Facades\Auth;

use App\Models\Slug;
use App\Models\Company;
use App\Models\BeneficiaryDocument;
use App\Models\CompanyIdTemplate;

use App\Http\Repositories\Base\PDFRepository;

use App\Traits\FileStorage;

class BeneficiaryDocumentRepository
{
    use FileStorage;

    public function __construct()
    {
        $this->pdfRepository = new PDFRepository;
    }

    public function new($data, $company)
    {   
        return new BeneficiaryDocument([
            'code' => self::generateCode($data['document_date'], $data['name'], $company),
            'company_id' => $company->id,
            'document_date' => $data['document_date'],
            'name' => $data['name'],
            'description' => $data['description'],
            'view' => $data['view']
                ? json_encode($data['view'])
                : null,
            'options' => $data['options']
                ? json_encode($data['options'])
                : null,
            'content' => $data['content']
                ? json_encode($data['content'])
                : null,
            'inputs' => $data['inputs']
                ? json_encode($data['inputs'])
                : null,
            'tables' => $data['tables']
                ? json_encode($data['tables'])
                : null,
            'approvals' => $data['approvals']
                ? json_encode($data['approvals'])
                : null,
            'header_border' => $data['header_border'],
            'left_signature' => $data['left_signature'],
            'right_signature' => $data['right_signature'],
            'created_by' => Auth::id() ?: 1
        ]);
    }

    public function generateCode($date, $templateName, $company)
    {
        $count = $company->beneficiaryDocuments()->count();

        $code = substr($templateName, 0, 3) . '-';
        $code .= customDateFormat($date) . '-';
        $code .= leadingZeros($count+1);

        return $code;
    }

    public function download($document)
    {
        $view = $document->view
                ? json_decode($document->view)
                : null;
        $file = property_exists($view, 'index') ? $view->index : 'default';

        $now = now()->format('Y-m-d');

        $filename = $this->pdfRepository->fileName(
            $now,
            $now,
            'beneficiary-document-' . $document->code,
            '.pdf');

        $pdf = \PDF::loadView(
            "documents.{$file}.index",
            [
                'document' => $document
            ]
        )
        ->setOption('margin-bottom', '0mm')
        ->setOption('margin-top', '0mm')
        ->setOption('margin-right', '0mm')
        ->setOption('margin-left', '0mm');

        return response([
            'path' => $this->pdfRepository->export(
                $pdf->output(),
                $filename,
                'pdf/documents/'
            )
        ],
        200);
    }
}
?>