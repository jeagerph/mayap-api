<?php

namespace App\Http\Repositories\Base;

use Illuminate\Support\Facades\Auth;

use App\Models\Slug;
use App\Models\Company;
use App\Models\BeneficiaryIdentification;
use App\Models\CompanyIdTemplate;

use App\Http\Repositories\Base\PDFRepository;

use App\Traits\FileStorage;

class BeneficiaryIdentificationRepository
{
    use FileStorage;

    public function __construct()
    {
        $this->pdfRepository = new PDFRepository;
    }

    public function new($data, $company)
    {   
        return new BeneficiaryIdentification([
            'code' => self::generateCode($data['identification_date'], $data['name'], $company),
            'company_id' => $company->id,
            'identification_date' => $data['identification_date'],
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
            'approvals' => $data['approvals']
                ? json_encode($data['approvals'])
                : null,
            'left_signature' => $data['left_signature'],
            'right_signature' => $data['right_signature'],
            'created_by' => Auth::id() ?: 1
        ]);
    }

    public function generateCode($date, $templateName, $company)
    {
        $count = $company->beneficiaryIdentifications()->count();

        $code = substr($templateName, 0, 3) . '-';
        $code .= customDateFormat($date) . '-';
        $code .= leadingZeros($count+1);

        return $code;
    }

    public function download($identification)
    {
        $view = $identification->view
                ? json_decode($identification->view)
                : null;
        $file = property_exists($view, 'index') ? $view->index : 'default';

        $now = now()->format('Y-m-d');

        $filename = $this->pdfRepository->fileName(
            $now,
            $now,
            'beneficiary-identification-' . $identification->code,
            '.pdf');

        $pdf = \PDF::loadView(
            "identifications.{$file}.index",
            [
                'identification' => $identification
            ]
        )
        ->setPaper('A4', 'landscape')
        ->setOption('margin-bottom', '0mm')
        ->setOption('margin-top', '0mm')
        ->setOption('margin-right', '0mm')
        ->setOption('margin-left', '0mm');

        return response([
            'path' => $this->pdfRepository->export(
                $pdf->output(),
                $filename,
                'pdf/identifications/'
            )
        ],
        200);
    }
}
?>