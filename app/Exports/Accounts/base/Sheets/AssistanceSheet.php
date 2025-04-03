<?php

namespace App\Exports\Accounts\base\Sheets;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class AssistanceSheet implements FromArray, WithTitle, WithEvents
{
    public function __construct($data)
    {
        $this->companyAccount = $data['companyAccount'];
        $this->company = $data['company'];
        $this->request = $data['request'];

        $this->data = $data;

        $this->startRow = 10;
        $this->endRow = 10;
    }

    public function title(): string
    {
        return 'ASSISTANCE REPORT';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->mergeCells('A1:S1');
                $event->sheet->mergeCells('A3:S3');
                $event->sheet->mergeCells('A4:S4');
                $event->sheet->mergeCells('A6:S6');
                $event->sheet->mergeCells('A7:S7');

                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(10);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(30);
                $event->sheet->getDelegate()->getColumnDimension('G')->setWidth(10);
                $event->sheet->getDelegate()->getColumnDimension('H')->setWidth(10);
                $event->sheet->getDelegate()->getColumnDimension('I')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('J')->setWidth(30);
                $event->sheet->getDelegate()->getColumnDimension('K')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('L')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('M')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('N')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('O')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('P')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('Q')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('R')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('S')->setWidth(100);

                $event->sheet->getStyle('A1:S1')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                    'font' => [
                        'size' => 16,
                        'bold' => true,
                        'color' => ['argb' => '228CDB'],
                    ]
                ]);

                $event->sheet->getStyle('A3:S3')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                    'font' => [
                        'size' => 14,
                        'bold' => true,
                        'underline' => true,
                    ]
                ]);

                $event->sheet->getStyle('A4:S4')->applyFromArray([
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
                $event->sheet->getStyle('A6:S6')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                    'font' => [
                        'bold' => true,
                        'size' => 12
                    ]
                ]);

                // QUERIES
                $event->sheet->getStyle('A7:S7')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                    'font' => [
                        'bold' => true,
                        'size' => 10
                    ]
                ]);

                // HEADING

                $event->sheet->getStyle('A9:S9')->applyFromArray([
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
                    $event->sheet->getStyle('A'.$index.':S'.$index)->applyFromArray([
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

        $account = $this->companyAccount->account;

        $filters = $this->request->get('filter');
        $queries = 'ENCODER: ' . $account->full_name;        

        $data = [
            [
                $this->company->name
            ],
            [''],
            [
                'ENCODING REPORT (ASSISTANCES)',
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
                'FULL NAME',
                'GENDER',
                'AGE',
                'MOBILE NO',
                'ADDRESS',
                'TYPE OF ASSISTANCE',
                'ASSISTANCE AMOUNT',
                'ASSISTED',
                'DATE ASSISTED',
                'ASSISTED BY',
                'ASSISTANCE FROM',
                'NO. OF ASSISTANCE',
                'ENCODER',
                'REMARKS',
            ],
        ];

        $row = [];

        $currentRow = $this->startRow;

        foreach ($this->data['assistances'] as $assistance):

            $beneficiary = $assistance->beneficiary;

            $row[] = [
                (new \Carbon\Carbon($assistance->assistance_date))->format('F d, Y'),
                $beneficiary->province->name,
                $beneficiary->city->name,
                $beneficiary->barangay->name,
                $beneficiary->purok ?: 'NOT INDICATED',
                $beneficiary->fullName(),
                $beneficiary->genderOptions[$beneficiary->gender],
                \Carbon\Carbon::parse($beneficiary->date_of_birth)->age,
                $beneficiary->mobile_no,
                $beneficiary->address,
                $assistance->assistance_type,
                $assistance->assistance_amount ?: '0',
                $assistance->is_assisted ? 'YES':'NO',
                $assistance->assisted_date
                    ? (new \Carbon\Carbon($assistance->assisted_date))->format('F d, Y')
                    : '',
                $assistance->assisted_by,
                $assistance->assistance_from,
                $beneficiary->assistances_count ?: '0',
                ($assistance->creator())['full_name'],
                $assistance->remarks,
            ];

            $currentRow++;

        endforeach;

        $this->endRow = $currentRow;

        $data = array_merge($data, $row);

        return $data;
    }
}
