<?php

namespace App\Exports\SummaryReport\base\Sheets;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class SummarySheet implements FromArray, WithTitle, WithEvents, WithColumnFormatting
{
    public function __construct($data)
    {
        $this->company = $data['company'];
        $this->request = $data['request'];

        $this->data = $data;

        $this->rowCount = 9;
        $this->rowShaded = [];
    }

    public function title(): string
    {
        return 'SUMMARY REPORT';
    }

    public function columnFormats(): array
    {
        return [
            // 'D' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
        ];
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
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(10);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(10);
                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(20);

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
                // $event->sheet->getStyle('A7:D7')->applyFromArray([
                //     'alignment' => [
                //         'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                //     ],
                //     'font' => [
                //         'bold' => true,
                //         'size' => 10
                //     ]
                // ]);

                $event->sheet->getStyle('D')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 12
                    ]
                ]);

                // BENEFICIARIES
                $event->sheet->mergeCells('A9:D9');

                $event->sheet->getStyle('A9:D9')->applyFromArray([
                    'font' => [
                        'bold' => true,
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

                // OFFICERS/LEADERS
                $event->sheet->mergeCells('A13:D13');

                $event->sheet->getStyle('A13:D13')->applyFromArray([
                    'font' => [
                        'bold' => true,
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

                // REFERRAL/NETWORK
                $event->sheet->mergeCells('A17:D17');

                $event->sheet->getStyle('A17:D17')->applyFromArray([
                    'font' => [
                        'bold' => true,
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

                // HOUSEHOLD
                $event->sheet->mergeCells('A22:D22');

                $event->sheet->getStyle('A22:D22')->applyFromArray([
                    'font' => [
                        'bold' => true,
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

                // ASSISTANCE
                $event->sheet->mergeCells('A28:D28');

                $event->sheet->getStyle('A28:D28')->applyFromArray([
                    'font' => [
                        'bold' => true,
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

                // $event->sheet->getStyle('A11:D11')->applyFromArray([
                //     'font' => [
                //         'bold' => true,
                //     ],
                // ]);

                // $event->sheet->getStyle('A12:D12')->applyFromArray([
                //     'font' => [
                //         'bold' => true,
                //         'size' => 12
                //     ],
                //     'borders' => [
                //         'top' => [
                //             'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                //         ]
                //     ],
                // ]);

            },
        ];
    }

    public function array(): array
    {
        $date = $this->request->get('date');

        $data = [
            [
                $this->company->name
            ],
            [''],
            [
                'SUMMARY REPORT',
            ],
            [
                '(Data downloaded as of ' . now()->format('M d, Y h:i A') . ')'
            ],
            [''],
            [
                'DATE: ' . (new \Carbon\Carbon($date))->format('F d, Y')
            ],
            [
                '' // OTHER LABEL
            ],
            [''], // NEXT ROW
        ];

        $row = [
            [
                'BENEFICIARIES',
            ],
            [
                'TOTAL',
                '',
                '',
                $this->data['beneficiaries']['total'] ?: '0',
            ],
            [
                'NEW',
                '',
                '',
                $this->data['beneficiaries']['date'] ?: '0',
            ],
            [''],
            [
                'OFFICERS/LEADERS',
                '',
                '',
                '',
            ],
            [
                'TOTAL',
                '',
                '',
                $this->data['officers']['total'] ?: '0',
            ],
            [
                'NEW',
                '',
                '',
                $this->data['officers']['date'] ?: '0',
            ],
            [''],
            [
                'REFERRAL/NETWORK',
                '',
                '',
                '',
            ],
            [
                'TOTAL',
                '',
                '',
                $this->data['networks']['total'] ?: '0',
            ],
            [
                'NEW',
                '',
                '',
                $this->data['networks']['date'] ?: '0',
            ],
            [
                'INCENTIVES',
                '',
                '',
                $this->data['incentives']['date'] ?: '0',
            ],
            [''],
            [
                'HOUSEHOLD',
                '',
                '',
                '',
            ],
            [
                'TOTAL',
                '',
                '',
                $this->data['household']['total'] ?: '0',
            ],
            [
                'NEW',
                '',
                '',
                $this->data['household']['date'] ?: '0',
            ],
            [
                'NO. OF BARANGAY COVERED',
                '',
                '',
                count($this->data['householdByBarangay']) ?: '0',
            ],
            [
                'NO. OF SITIO/PUROK COVERED',
                '',
                '',
                count($this->data['householdByPurok']) ?: '0',
            ],

            [''],
            [
                'ASSISTANCE',
                '',
                '',
                '',
            ],
            [
                'NEW REQUESTED',
                '',
                '',
                $this->data['requested']['date'] ?: '0',
            ],
            [
                'NEW ASSISTED',
                '',
                '',
                $this->data['assisted']['date'] ?: '0',
            ],
            [''],
        ];

        foreach ($this->data['assistancesByType'] as $assistance):

            $row[] = [
                $assistance->name,
                '',
                '',
                $assistance->total ?: '0'
            ];

        endforeach;

        $data = array_merge($data, $row);

        return $data;
    }
}
