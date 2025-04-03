<?php

namespace App\Exports\Patients\base\Sheets;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class PatientByPurokSheet implements FromArray, WithTitle, WithEvents
{
    public function __construct($data)
    {
        $this->company = $data['company'];
        $this->request = $data['request'];

        $this->data = $data;

        $this->startRow = 9;
        $this->endRow = 9;
        $this->totalRow = 9;
    }

    public function title(): string
    {
        return 'SUMMARY';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->mergeCells('A1:E1');
                $event->sheet->mergeCells('A3:E3');
                $event->sheet->mergeCells('A4:E4');
                $event->sheet->mergeCells('A6:E6');
                $event->sheet->mergeCells('A7:E7');

                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(15);

                $event->sheet->getStyle('A1:E1')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                    'font' => [
                        'size' => 16,
                        'bold' => true,
                        'color' => ['argb' => '228CDB'],
                    ]
                ]);

                $event->sheet->getStyle('A3:E3')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                    'font' => [
                        'size' => 14,
                        'bold' => true,
                        'underline' => true,
                    ]
                ]);

                $event->sheet->getStyle('A4:E4')->applyFromArray([
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
                $event->sheet->getStyle('A6:E6')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                    'font' => [
                        'bold' => true,
                        'size' => 12
                    ]
                ]);

                // QUERIES
                $event->sheet->getStyle('A7:E7')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                    'font' => [
                        'bold' => true,
                        'size' => 10
                    ]
                ]);

                // HEADING

                $event->sheet->getStyle('A9:E9')->applyFromArray([
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
                    $event->sheet->getStyle('A'.$index.':E'.$index)->applyFromArray([
                        'font' => [
                            'size' => 10
                        ],
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        ],
                    ]);
                endfor;

                // TOTAL

                $event->sheet->getStyle('A'.$this->totalRow.':E'.$this->totalRow)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 10
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                    'borders' => [
                        'top' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ]
                    ],
                ]);
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
                'PATIENT BY PUROK/SITIO REPORT',
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
                'PROVINCE',
                'CITY/MUNICIPALITY',
                'BARANGAY',
                'PUROK/SITIO',
                'TOTAL',
            ],
        ];

        $row = [];

        $currentRow = $this->startRow;

        $total = 0;

        foreach ($this->data['barangays'] as $barangay):

            $row[] = [
                $barangay->province_name,
                $barangay->city_name,
                strtoupper($barangay->barangay_name),
                $barangay->purok ?: 'NOT INDICATED',
                $barangay->total ?: '0'
            ];

            $total += $barangay->total;

            $currentRow++;

        endforeach;

        $this->endRow = $currentRow;

        $row[] = [
            'TOTAL',
            '',
            '',
            '',
            $total ?: '0',
        ];

        $this->totalRow = $this->endRow + 1;

        $data = array_merge($data, $row);

        return $data;
    }
}
