<?php

namespace App\Http\Repositories\Base;

use App\Models\Beneficiary;
use App\Models\Voter;
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
        $code .= leadingZeros($count + 1);

        return $code;
    }

    public function download($identification)
    {
        $beneficiary = Beneficiary::where('id', $identification->beneficiary_id)->first();

        $data = [
            'identification' => $identification,
            'voter_details' => null,
        ];

        if ($beneficiary && $beneficiary->verify_voter != null) {
            $voter = Voter::where('first_name', $beneficiary->first_name)
                ->where('middle_name', $beneficiary->middle_name)
                ->where('last_name', $beneficiary->last_name)
                ->first();

            if ($voter) {
                $data['voter_details'] = [
                    'precinct_no' => $voter->precinct_no
                ];
            }
        }

        $view = $identification->view
            ? json_decode($identification->view)
            : null;
        $file = property_exists($view, 'index') ? $view->index : 'default';

        $now = now()->format('Y-m-d');

        $filename = $this->pdfRepository->fileName(
            $now,
            $now,
            'beneficiary-identification-' . $identification->code,
            '.pdf'
        );

        $pdf = \PDF::loadView(
            "identifications.{$file}.index",
            $data
        )
            ->setPaper('A4', 'landscape')
            ->setOption('margin-bottom', '0mm')
            ->setOption('margin-top', '0mm')
            ->setOption('margin-right', '0mm')
            ->setOption('margin-left', '0mm');

        return response(
            [
                'path' => $this->pdfRepository->export(
                    $pdf->output(),
                    $filename,
                    'pdf/identifications/'
                )
            ],
            200
        );
    }

    public function downloadIdentifications($identifications)
    {
        $file = 'batch-default';

        $now = now()->format('Y-m-d');

        $filename = $this->pdfRepository->fileName(
            $now,
            $now,
            'beneficiary-identification-cards-' . $now,
            '.pdf'
        );

        $pdf = \PDF::loadView(
            "identifications.{$file}.index",
            [
                'identifications' => $identifications
            ]
        )
            ->setPaper('A4', 'landscape')
            ->setOption('margin-bottom', '0mm')
            ->setOption('margin-top', '0mm')
            ->setOption('margin-right', '0mm')
            ->setOption('margin-left', '0mm');

        return response(
            [
                'path' => $this->pdfRepository->export(
                    $pdf->output(),
                    $filename,
                    'pdf/identifications/'
                )
            ],
            200
        );
    }

    public function updatePrintedIdentifications($codesArray)
    {
        // Ensure $codesArray is an array and extract the "code" values
        $codes = array_column($codesArray, 'code');

        // Ensure it's an array before using whereIn()
        if (!is_array($codes) || empty($codes)) {
            return response()->json([
                'message' => 'No valid codes provided.',
            ], 400);
        }

        // Update records where code is in the extracted array
        BeneficiaryIdentification::whereIn('code', $codes)
            ->update(['is_printed' => 1]);

        return response()->json([
            'message' => 'Successfully updated printed identifications'
        ], 200);
    }

}
?>