<?php

namespace App\Exports\Accounts\base\Sheets;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class BeneficiarySheet implements FromArray, WithTitle, WithEvents
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
        return 'BENEFICIARIES';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->mergeCells('A1:AA1');
                $event->sheet->mergeCells('A3:AA3');
                $event->sheet->mergeCells('A4:AA4');
                $event->sheet->mergeCells('A6:AA6');
                $event->sheet->mergeCells('A7:AA7');

                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(10);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(30);
                $event->sheet->getDelegate()->getColumnDimension('G')->setWidth(10);
                $event->sheet->getDelegate()->getColumnDimension('H')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('I')->setWidth(10);
                $event->sheet->getDelegate()->getColumnDimension('J')->setWidth(10);
                $event->sheet->getDelegate()->getColumnDimension('K')->setWidth(10);
                $event->sheet->getDelegate()->getColumnDimension('L')->setWidth(50);
                $event->sheet->getDelegate()->getColumnDimension('M')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('N')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('O')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('P')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('Q')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('R')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('S')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('T')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('U')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('V')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('W')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('X')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('Y')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('Z')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('AA')->setWidth(20);

                $event->sheet->getStyle('A1:AA1')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                    'font' => [
                        'size' => 16,
                        'bold' => true,
                        'color' => ['argb' => '228CDB'],
                    ]
                ]);

                $event->sheet->getStyle('A3:AA3')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                    'font' => [
                        'size' => 14,
                        'bold' => true,
                        'underline' => true,
                    ]
                ]);

                $event->sheet->getStyle('A4:AA4')->applyFromArray([
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
                $event->sheet->getStyle('A6:AA6')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                    'font' => [
                        'bold' => true,
                        'size' => 12
                    ]
                ]);

                // QUERIES
                $event->sheet->getStyle('A7:AA7')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                    'font' => [
                        'bold' => true,
                        'size' => 10
                    ]
                ]);

                // HEADING

                $event->sheet->getStyle('A9:AA9')->applyFromArray([
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
                    $event->sheet->getStyle('A'.$index.':AA'.$index)->applyFromArray([
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
                'ENCODING REPORT (BENEFICIARIES)',
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
                'DATE REGISTERED',
                'PROVINCE',
                'CITY/MUNICIPALITY',
                'BARANGAY',
                'PUROK/SITIO',
                'FULL NAME',
                'GENDER',
                'DATE OF BIRTH',
                'AGE',
                'MOBILE NO',
                'CIVIL STATUS',
                'ADDRESS',
                'PLACE OF BIRTH',
                'EDUCATIONAL ATTAINMENT',
                'CLASSIFICATION',
                'MONTHLY INCOME',
                'SOURCE OF INCOME',
                'OCCUPATION',
                'OFFICER/LEADER',
                'HEAD OF HOUSEHOLD',
                'HOUSEHOLD MEMBER',
                'HOUSEHOLD VOTERS',
                'CITIZENSHIP',
                'RELIGION',
                'VOTER TYPE',
                'NO. OF ASSISTANCE',
                'ENCODER',
            ],
        ];

        $row = [];

        $currentRow = $this->startRow;

        foreach ($this->data['beneficiaries'] as $beneficiary):

            $row[] = [
                $beneficiary->date_registered,
                $beneficiary->province->name,
                $beneficiary->city->name,
                $beneficiary->barangay->name,
                $beneficiary->purok ?: 'NOT INDICATED',
                $beneficiary->fullName(),
                $beneficiary->genderOptions[$beneficiary->gender],
                (new \Carbon\Carbon($beneficiary->date_of_birth))->format('F d, Y'),
                \Carbon\Carbon::parse($beneficiary->date_of_birth)->age,
                $beneficiary->mobile_no,
                $beneficiary->civil_status,
                $beneficiary->address,
                $beneficiary->place_of_birth,
                $beneficiary->educational_attainment,
                $beneficiary->classification,
                $beneficiary->monthly_income,
                $beneficiary->source_of_income,
                $beneficiary->occupation,
                $beneficiary->is_officer ? 'YES':'NO',
                $beneficiary->is_household ? 'YES':'NO',
                $beneficiary->household_count,
                $beneficiary->household_voters_count,
                $beneficiary->citizenship,
                $beneficiary->religion,
                $beneficiary->voterTypeOptions[$beneficiary->voter_type]['name'],
                $beneficiary->assistances_count ?: '0',
                ($beneficiary->creator())['full_name'],
            ];

            $currentRow++;

        endforeach;

        $this->endRow = $currentRow;

        $data = array_merge($data, $row);

        return $data;
    }
}
