<?php

namespace App\Exports\Accounts\base\Sheets;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class DateSummarySheet implements FromArray, WithTitle, WithEvents
{
    public function __construct($data)
    {
        $this->companyAccount = $data['companyAccount'];
        $this->company = $data['company'];
        $this->request = $data['request'];

        $this->data = $data;

        $this->startRow = 10;
        $this->endRow = 10;
        $this->totalRow = 10;
    }

    public function title(): string
    {
        return 'ENCODING REPORT';
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
                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(20);

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
                        'size' => 10,
                        'bold' => true,
                    ],
                    'borders' => [
                        'top' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ]
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

            },
        ];
    }

    public function array(): array
    {
        $from = $this->request->get('from');
        $to = $this->request->get('to');

        $account = $this->companyAccount->account;
        $listOfDates = listDatesFromDateRange($from, $to);

        $filters = $this->request->get('filter');
        $queries = 'ENCODER: ' . $account->full_name;        

        $data = [
            [
                $this->company->name
            ],
            [''],
            [
                'ENCODING REPORT',
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
                'BENEFICIARIES',
                'ASSISTANCES',
                'PATIENTS',
            ],
        ];

        $row = [];

        $totalBeneficiary = 0;
        $totalAssistance = 0;
        $totalPatient = 0;

        $currentRow = $this->startRow;

        foreach ($listOfDates as $date):

            $beneficiary = 0;
            $assistance = 0;
            $patient = 0;

            foreach ($this->data['summary']['beneficiaries'] as $rowBeneficiary):
                if ($rowBeneficiary->date == $date):
                    $beneficiary = $rowBeneficiary->total ?: 0;
                    break;
                endif;
            endforeach;

            foreach ($this->data['summary']['assistances'] as $rowAssistance):
                if ($rowAssistance->date == $date):
                    $assistance = $rowAssistance->total ?: 0;
                    break;
                endif;
            endforeach;

            foreach ($this->data['summary']['patients'] as $rowPatient):
                if ($rowPatient->date == $date):
                    $patient = $rowPatient->total ?: 0;
                    break;
                endif;
            endforeach;

            $row[] = [
                (new \Carbon\Carbon($date))->format('F d, Y'),
                $beneficiary ?: '0',
                $assistance ?: '0',
                $patient ?: '0',
            ];

            $totalBeneficiary += $beneficiary;
            $totalAssistance += $assistance;
            $totalPatient += $patient;

            $currentRow++;

        endforeach;

        $this->endRow = $currentRow;

        $row[] = [
            'TOTAL',
            $totalBeneficiary ?: '0',
            $totalAssistance ?: '0',
            $totalPatient ?: '0',
        ];

        $this->totalRow = $this->endRow;

        

        $data = array_merge($data, $row);

        return $data;
    }
}
