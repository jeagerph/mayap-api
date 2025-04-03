<?php

namespace App\Exports\Assistances\mayap\Sheets;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class AssistanceByBarangaySheet implements FromArray, WithTitle, WithEvents
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
                $event->sheet->mergeCells('A1:N1');
                $event->sheet->mergeCells('A3:N3');
                $event->sheet->mergeCells('A4:N4');
                $event->sheet->mergeCells('A6:N6');
                $event->sheet->mergeCells('A7:N7');

                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('G')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('H')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('I')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('J')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('K')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('L')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('M')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('N')->setWidth(20);

                $event->sheet->getStyle('A1:N1')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                    'font' => [
                        'size' => 16,
                        'bold' => true,
                        'color' => ['argb' => '228CDB'],
                    ]
                ]);

                $event->sheet->getStyle('A3:N3')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                    'font' => [
                        'size' => 14,
                        'bold' => true,
                        'underline' => true,
                    ]
                ]);

                $event->sheet->getStyle('A4:N4')->applyFromArray([
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
                $event->sheet->getStyle('A6:N6')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                    'font' => [
                        'bold' => true,
                        'size' => 12
                    ]
                ]);

                // QUERIES
                $event->sheet->getStyle('A7:N7')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                    'font' => [
                        'bold' => true,
                        'size' => 10
                    ]
                ]);

                // HEADING

                $event->sheet->getStyle('A9:N9')->applyFromArray([
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
                    $event->sheet->getStyle('A'.$index.':N'.$index)->applyFromArray([
                        'font' => [
                            'size' => 10
                        ],
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        ],
                    ]);
                endfor;

                // TOTAL

                $event->sheet->getStyle('A'.$this->totalRow.':N'.$this->totalRow)->applyFromArray([
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
            if (array_key_exists('assistanceType', $filters)):

                $queryAssistanceType = $filters['assistanceType'];
                $queryAssistanceTypeLabel = 'ASSISTANCE TYPE: ';
                
                if ($queryAssistanceType):
    
                    $queryAssistanceTypeLabel .= $queryAssistanceType;
    
                    $queries .= $queryAssistanceTypeLabel . ' | ';
    
                endif;
                
            endif;
    
            if (array_key_exists('isAssisted', $filters)):
    
                $queryAssisted = $filters['isAssisted'];
                $queryAssistedLabel = 'ASSISTED: ';
                
                if ($queryAssisted):
    
                    $queryAssistedLabel .= $queryAssisted ? 'YES':'NO';
    
                    $queries .= $queryAssistedLabel . ' | ';
    
                endif;
                
            endif;
    
            if (array_key_exists('assistedBy', $filters)):
    
                $queryAssistedBy = $filters['assistedBy'];
                $queryAssistedByLabel = 'ASSISTED BY: ';
                
                if ($queryAssistedBy):
    
                    $queryAssistedByLabel .= $queryAssistedBy;
    
                    $queries .= $queryAssistedByLabel . ' | ';
    
                endif;
                
            endif;
    
            if (array_key_exists('assistanceFrom', $filters)):
    
                $queryAssistanceFrom = $filters['assistanceFrom'];
                $queryAssistanceFromLabel = 'ASSISTANCE FROM: ';
                
                if ($queryAssistanceFrom):
    
                    $queryAssistanceFromLabel .= $queryAssistanceFrom;
    
                    $queries .= $queryAssistanceFromLabel . ' | ';
    
                endif;
                
            endif;
    
            if (array_key_exists('benefFirstName', $filters)):
    
                $queryBenefFirstName = $filters['benefFirstName'];
                $queryBenefFirstNameLabel = 'FIRST NAME: ';
                
                if ($queryBenefFirstName):
    
                    $queryBenefFirstNameLabel .= $queryBenefFirstName;
    
                    $queries .= $queryBenefFirstNameLabel . ' | ';
    
                endif;
                
            endif;
    
            if (array_key_exists('benefMiddleName', $filters)):
    
                $queryBenefMiddleName = $filters['benefMiddleName'];
                $queryBenefMiddleNameLabel = 'MIDDLE NAME: ';
                
                if ($queryBenefMiddleName):
    
                    $queryBenefMiddleNameLabel .= $queryBenefMiddleName;
    
                    $queries .= $queryBenefMiddleNameLabel . ' | ';
    
                endif;
                
            endif;
    
            if (array_key_exists('benefLastName', $filters)):
    
                $queryBenefLastName = $filters['benefLastName'];
                $queryBenefLastNameLabel = 'LAST NAME: ';
                
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
                $queryPurokLabel = 'PUROK: ';
                
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
                'ASSISTANCE BY BARANGAY REPORT',
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
                'REQUESTED',
                'ASSISTED',
                'TOTAL',
                'TRAINING',
                'SCHOLARSHIP',
                'BURIAL',
                'INFRASTRACTURE',
                'GUARANTEE LETTER',
                'MEDICINE/MEDICAL',
                'FINANCIAL ASSISTANCE',
                'ASSISTANCE AMOUNT',
            ],
        ];

        $row = [];

        $currentRow = $this->startRow;

        $total = 0;
        $requestedTotal = 0;
        $assistedTotal = 0;
        
        $trainTotal = 0;
        $scholarTotal = 0;
        $burialTotal = 0;
        $infraTotal = 0;
        $glTotal = 0;
        $medTotal = 0;
        $faTotal = 0;
        $assistanceAmountTotal = 0;

        foreach ($this->data['barangays'] as $barangay):

            $row[] = [
                $barangay->province_name,
                $barangay->city_name,
                strtoupper($barangay->barangay_name),
                $barangay->requested ?: '0',
                $barangay->assisted ?: '0',
                ($barangay->requested + $barangay->assisted) ?: '0',
                $barangay->train_total ?: '0',
                $barangay->scholar_total ?: '0',
                $barangay->burial_total ?: '0',
                $barangay->infra_total ?: '0',
                $barangay->gl_total ?: '0',
                $barangay->med_total ?: '0',
                $barangay->fa_total ?: '0',
                $barangay->amount ?: '0'
            ];

            $requestedTotal += $barangay->requested;
            $assistedTotal += $barangay->assisted;
            $total += $barangay->requested + $barangay->assisted;

            $trainTotal += $barangay->train_total;
            $scholarTotal += $barangay->scholar_total;
            $burialTotal += $barangay->burial_total;
            $infraTotal += $barangay->infra_total;
            $glTotal += $barangay->gl_total;
            $medTotal += $barangay->med_total;
            $faTotal += $barangay->fa_total;
            $assistanceAmountTotal += $barangay->amount;

            $currentRow++;

        endforeach;

        $this->endRow = $currentRow;

        $row[] = [
            'TOTAL',
            '',
            '',
            $requestedTotal ?: '0',
            $assistedTotal ?: '0',
            $total ?: '0',
            $trainTotal ?: '0',
            $scholarTotal ?: '0',
            $burialTotal ?: '0',
            $infraTotal ?: '0',
            $glTotal ?: '0',
            $medTotal ?: '0',
            $faTotal ?: '0',
            $assistanceAmountTotal ?: '0',
        ];

        $this->totalRow = $this->endRow + 1;

        $data = array_merge($data, $row);

        return $data;
    }
}
