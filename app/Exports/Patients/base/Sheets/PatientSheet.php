<?php

namespace App\Exports\Patients\base\Sheets;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class PatientSheet implements FromArray, WithTitle, WithEvents
{
    public function __construct($data)
    {
        $this->company = $data['company'];
        $this->request = $data['request'];

        $this->data = $data;

        $this->startRow = 10;
        $this->endRow = 10;
    }

    public function title(): string
    {
        return 'PATIENTS REPORT';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->mergeCells('A1:O1');
                $event->sheet->mergeCells('A3:O3');
                $event->sheet->mergeCells('A4:O4');
                $event->sheet->mergeCells('A6:O6');
                $event->sheet->mergeCells('A7:O7');

                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(10);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(30);
                $event->sheet->getDelegate()->getColumnDimension('G')->setWidth(50);
                $event->sheet->getDelegate()->getColumnDimension('H')->setWidth(50);
                $event->sheet->getDelegate()->getColumnDimension('I')->setWidth(50);
                $event->sheet->getDelegate()->getColumnDimension('J')->setWidth(50);
                $event->sheet->getDelegate()->getColumnDimension('K')->setWidth(50);
                $event->sheet->getDelegate()->getColumnDimension('L')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('M')->setWidth(30);
                $event->sheet->getDelegate()->getColumnDimension('N')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('O')->setWidth(15);

                $event->sheet->getStyle('A1:O1')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                    'font' => [
                        'size' => 16,
                        'bold' => true,
                        'color' => ['argb' => '228CDB'],
                    ]
                ]);

                $event->sheet->getStyle('A3:O3')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                    'font' => [
                        'size' => 14,
                        'bold' => true,
                        'underline' => true,
                    ]
                ]);

                $event->sheet->getStyle('A4:O4')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                    'font' => [
                        'bold' => true,
                        'italic' => true,
                        'size' => 10
                    ]
                ]);

                // DATE PERIOD
                $event->sheet->getStyle('A6:O6')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                    'font' => [
                        'bold' => true,
                        'size' => 12
                    ]
                ]);

                // QUERIES
                $event->sheet->getStyle('A7:O7')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                    'font' => [
                        'bold' => true,
                        'size' => 10
                    ]
                ]);

                // HEADING

                $event->sheet->getStyle('A9:O9')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 9
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ]
                    ],
                ]);


                // DATA

                for ($index = $this->startRow; $index <= $this->endRow; $index++):
                    $event->sheet->getStyle('A'.$index.':O'.$index)->applyFromArray([
                        'font' => [
                            'size' => 10
                        ],
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        ],
                    ]);
                endfor;

            },
        ];
    }

    public function array(): array
    {
        $from = $this->request->get('from');
        $to = $this->request->get('to');

        $filters = $this->request->get('filter');
        $queries = '';

        if ($filters):
            if (array_key_exists('firstName', $filters)):

                $queryFirstName = $filters['firstName'];
                $queryFirstNameLabel = 'FIRST NAME: ';
                
                if ($queryFirstName):
    
                    $queryFirstNameLabel .= $queryFirstName;
    
                    $queries .= $queryFirstNameLabel . ' | ';
    
                endif;
                
            endif;
    
            if (array_key_exists('middleName', $filters)):
    
                $queryMiddleName = $filters['middleName'];
                $queryMiddleNameLabel = 'MIDDLE NAME: ';
                
                if ($queryMiddleName):
    
                    $queryMiddleNameLabel .= $queryMiddleName;
    
                    $queries .= $queryMiddleNameLabel . ' | ';
    
                endif;
                
            endif;
    
            if (array_key_exists('lastName', $filters)):
    
                $queryLastName = $filters['lastName'];
                $queryLastNameLabel = 'LAST NAME: ';
                
                if ($queryLastName):
    
                    $queryLastNameLabel .= $queryLastName;
    
                    $queries .= $queryLastNameLabel . ' | ';
    
                endif;
                
            endif;
    
            if (array_key_exists('problemPresented', $filters)):
    
                $queryProblemPresented = $filters['problemPresented'];
                $queryProblemPresentedLabel = 'PROBLEM PRESENTED: ';
                
                if ($queryProblemPresented):
    
                    $queryProblemPresentedLabel .= $queryProblemPresented;
    
                    $queries .= $queryProblemPresentedLabel . ' | ';
    
                endif;
                
            endif;
    
            if (array_key_exists('findings', $filters)):
    
                $queryFindings = $filters['findings'];
                $queryFindingsLabel = 'FINDINGS: ';
                
                if ($queryFindings):
    
                    $queryFindingsLabel .= $queryFindings;
    
                    $queries .= $queryFindingsLabel . ' | ';
    
                endif;
                
            endif;
    
            if (array_key_exists('assessmentRecommendation', $filters)):
    
                $queryAssessmentRecommendation = $filters['assessmentRecommendation'];
                $queryAssessmentRecommendationLabel = 'ASSESSMENT & RECOMMENDATION: ';
                
                if ($queryAssessmentRecommendation):
    
                    $queryAssessmentRecommendationLabel .= $queryAssessmentRecommendation;
    
                    $queries .= $queryAssessmentRecommendationLabel . ' | ';
    
                endif;
                
            endif;
    
            if (array_key_exists('needs', $filters)):
    
                $queryNeeds = $filters['needs'];
                $queryNeedsLabel = 'NEEDS: ';
                
                if ($queryNeeds):
    
                    $queryNeedsLabel .= $queryNeeds;
    
                    $queries .= $queryNeedsLabel . ' | ';
    
                endif;
                
            endif;
    
            if (array_key_exists('remarks', $filters)):
    
                $queryRemarks = $filters['remarks'];
                $queryRemarksLabel = 'OTHER REMARKS: ';
                
                if ($queryRemarks):
    
                    $queryRemarksLabel .= $queryRemarks;
    
                    $queries .= $queryRemarksLabel . ' | ';
    
                endif;
                
            endif;
    
            if (array_key_exists('relationToPatient', $filters)):
    
                $queryRelationToPatient = $filters['relationToPatient'];
                $queryRelationToPatientLabel = 'RELATION TO PATIENT: ';
                
                if ($queryRelationToPatient):
    
                    $queryRelationToPatientLabel .= $queryRelationToPatient;
    
                    $queries .= $queryRelationToPatientLabel . ' | ';
    
                endif;
                
            endif;
    
            if (array_key_exists('status', $filters)):
    
                $queryStatus = $filters['status'];
                $queryStatusLabel = 'STATUS: ';
                
                if ($queryStatus):
    
                    $patientModel = new \App\Models\BeneficiaryPatient;
    
                    $queryStatusLabel .= $patientModel->statusOptions[$queryStatus];
    
                    $queries .= $queryStatusLabel . ' | ';
    
                endif;
                
            endif;
    
            if (array_key_exists('benefFirstName', $filters)):
    
                $queryBenefFirstName = $filters['benefFirstName'];
                $queryBenefFirstNameLabel = '(BENEF) FIRST NAME: ';
                
                if ($queryBenefFirstName):
    
                    $queryBenefFirstNameLabel .= $queryBenefFirstName;
    
                    $queries .= $queryBenefFirstNameLabel . ' | ';
    
                endif;
                
            endif;
    
            if (array_key_exists('benefMiddleName', $filters)):
    
                $queryBenefMiddleName = $filters['benefMiddleName'];
                $queryBenefMiddleNameLabel = '(BENEF) MIDDLE NAME: ';
                
                if ($queryBenefMiddleName):
    
                    $queryBenefMiddleNameLabel .= $queryBenefMiddleName;
    
                    $queries .= $queryBenefMiddleNameLabel . ' | ';
    
                endif;
                
            endif;
    
            if (array_key_exists('benefLastName', $filters)):
    
                $queryBenefLastName = $filters['benefLastName'];
                $queryBenefLastNameLabel = '(BENEF) LAST NAME: ';
                
                if ($queryBenefLastName):
    
                    $queryBenefLastNameLabel .= $queryBenefLastName;
    
                    $queries .= $queryBenefLastNameLabel . ' | ';
    
                endif;
                
            endif;
    
            if (array_key_exists('benefProvCode', $filters)):
    
                $queryProvince = $filters['benefProvCode'];
                $queryProvinceLabel = 'PROVINCE: ';
                
                if ($queryProvince):
    
                    $provinceModel = \App\Models\Province::where('prov_code', $queryProvince)->first();
    
                    $queryProvinceLabel .= $provinceModel->name;
    
                    $queries .= $queryProvinceLabel . ' | ';
    
                endif;
    
            endif;
    
            if (array_key_exists('benefCityCode', $filters)):
    
                $queryCity = $filters['benefCityCode'];
                $queryCityLabel = 'CITY/MUNICIPALITY: ';
                
                if ($queryCity):
    
                    $cityModel = \App\Models\City::where('city_code', $queryCity)->first();
    
                    $queryCityLabel .= $cityModel->name;
    
                    $queries .= $queryCityLabel . ' | ';
    
                endif;
    
            endif;
    
            if (array_key_exists('benefBarangay', $filters)):
    
                $queryBarangay = $filters['benefBarangay'];
                $queryBarangayLabel = 'BARANGAY: ';
                
                if ($queryBarangay):
    
                    $barangayModel = \App\Models\Barangay::where('id', $queryBarangay)->first();
    
                    $queryBarangayLabel .= $barangayModel->name;
    
                    $queries .= $queryBarangayLabel . ' | ';
    
                endif;
    
            endif;
    
            if (array_key_exists('benefPurok', $filters)):
    
                $queryPurok = $filters['benefPurok'];
                $queryPurokLabel = 'PUROK/SITIO: ';
                
                if ($queryPurok):
    
                    $queryPurokLabel .= $queryPurok;
    
                    $queries .= $queryPurokLabel . ' | ';
    
                endif;
    
            endif;
    
            if (array_key_exists('benefStreet', $filters)):
    
                $queryStreet = $filters['benefStreet'];
                $queryStreetLabel = 'STREET: ';
                
                if ($queryStreet):
    
                    $queryStreetLabel .= $queryStreet;
    
                    $queries .= $queryStreetLabel . ' | ';
    
                endif;
    
            endif;
    
            if (array_key_exists('benefZone', $filters)):
    
                $queryZone = $filters['benefZone'];
                $queryZoneLabel = 'ZONE: ';
                
                if ($queryZone):
    
                    $queryZoneLabel .= $queryZone;
    
                    $queries .= $queryZoneLabel . ' | ';
    
                endif;
    
            endif;
        endif;

        $data = [
            [
                $this->company->name
            ],
            [''],
            [
                'PATIENTS REPORT',
            ],
            [
                '(Data downloaded as of ' . now()->format('M d, Y h:i A') . ')'
            ],
            [''],
            [
                'DATE: ' . (new \Carbon\Carbon($from))->format('F d, Y') . ($from != $to ? (' ~ ' . (new \Carbon\Carbon($to))->format('F d, Y')) : '')
            ],
            [
                $queries
            ],
            [''], // NEXT ROW
            [
                'DATE',
                'PROVINCE',
                'CITY/MUNICIPALITY',
                'BARANGAY',
                'PUROK/SITIO',
                'PATIENT',
                'PROBLEM PRESENTED',
                'FINDINGS',
                'ASSESSMENT & RECOMMENDATION',
                'NEEDS',
                'OTHER REMARKS',
                'STATUS',
                '(BENEF) FULL NAME',
                'RELATION TO PATIENT',
                'ENCODER',
            ],
        ];

        $row = [];

        $currentRow = $this->startRow;

        foreach ($this->data['patients'] as $patient):

            $beneficiary = $patient->beneficiary;

            $row[] = [
                (new \Carbon\Carbon($patient->patient_date))->format('F d, Y'),
                $beneficiary->province->name,
                $beneficiary->city->name,
                $beneficiary->barangay->name,
                $beneficiary->purok ?: 'NOT INDICATED',
                $patient->fullName(),
                $patient->problem_presented,
                $patient->findings,
                $patient->assessment_recommendation,
                $patient->needs,
                $patient->remarks,
                $patient->statusOptions[$patient->status],
                $beneficiary->fullName(),
                $patient->relation_to_patient,
                ($patient->creator())['full_name']
            ];

            $currentRow++;

        endforeach;

        $this->endRow = $currentRow;

        $data = array_merge($data, $row);

        return $data;
    }
}
