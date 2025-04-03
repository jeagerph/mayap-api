<?php

namespace App\Exports\Beneficiaries\base\Sheets;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class HouseholdByBarangaySheet implements FromArray, WithTitle, WithEvents
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
                $event->sheet->mergeCells('A1:D1');
                $event->sheet->mergeCells('A3:D3');
                $event->sheet->mergeCells('A4:D4');
                $event->sheet->mergeCells('A6:D6');
                $event->sheet->mergeCells('A7:D7');

                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(15);

                $event->sheet->getStyle('A1:D1')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                    'font' => [
                        'size' => 16,
                        'bold' => true,
                        'color' => ['argb' => '228CDB'],
                    ]
                ]);

                $event->sheet->getStyle('A3:D3')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                    'font' => [
                        'size' => 14,
                        'bold' => true,
                        'underline' => true,
                    ]
                ]);

                $event->sheet->getStyle('A4:D4')->applyFromArray([
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
                $event->sheet->getStyle('A6:D6')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                    'font' => [
                        'bold' => true,
                        'size' => 12
                    ]
                ]);

                // QUERIES
                $event->sheet->getStyle('A7:D7')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                    'font' => [
                        'bold' => true,
                        'size' => 10
                    ]
                ]);

                // HEADING

                $event->sheet->getStyle('A9:D9')->applyFromArray([
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
                    $event->sheet->getStyle('A'.$index.':D'.$index)->applyFromArray([
                        'font' => [
                            'size' => 10
                        ],
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        ],
                    ]);
                endfor;

                // TOTAL

                $event->sheet->getStyle('A'.$this->totalRow.':D'.$this->totalRow)->applyFromArray([
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
            if (array_key_exists('isHousehold', $filters)):

                $queryHousehold = $filters['isHousehold'];
                $queryHouseholdLabel = 'HEAD OF HOUSEHOLD: ';
                
                if ($queryHousehold):
    
                    $queryHouseholdLabel .= $queryHousehold ? 'YES':'NO';
    
                    $queries .= $queryHouseholdLabel . ' | ';
    
                endif;
                
            endif;
        endif;
        

        $data = [
            [
                $this->company->name
            ],
            [''],
            [
                'HOUSEHOLD BY BARANGAY REPORT',
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
            $total ?: '0',
        ];

        $this->totalRow = $this->endRow + 1;

        $data = array_merge($data, $row);

        return $data;
    }
}
