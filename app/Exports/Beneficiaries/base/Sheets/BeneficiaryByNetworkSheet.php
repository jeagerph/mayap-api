<?php

namespace App\Exports\Beneficiaries\Base\Sheets;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class BeneficiaryByNetworkSheet implements FromArray, WithTitle, WithEvents
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
        return 'BENEFICIARY BY NETWORK';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Merging header cells
                $event->sheet->mergeCells('A1:N1');
                $event->sheet->mergeCells('A3:N3');
                $event->sheet->mergeCells('A4:N4');
                $event->sheet->mergeCells('A6:N6');
                $event->sheet->mergeCells('A7:N7');

                // Column width adjustments
                $columns = range('A', 'N');
                foreach ($columns as $col) {
                    $event->sheet->getDelegate()->getColumnDimension($col)->setWidth(15);
                }

                $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(30);
                $event->sheet->getDelegate()->getColumnDimension('G')->setWidth(10);
                $event->sheet->getDelegate()->getColumnDimension('H')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('I')->setWidth(10);
                $event->sheet->getDelegate()->getColumnDimension('J')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('K')->setWidth(50);

                // Styling headers
                $headerStyles = [
                    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                    'font' => ['bold' => true, 'size' => 12]
                ];
                $event->sheet->getStyle('A1:N1')->applyFromArray([
                    'font' => ['size' => 16, 'bold' => true, 'color' => ['argb' => '228CDB']],
                    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                ]);

                $event->sheet->getStyle('A3:N3')->applyFromArray([
                    'font' => ['size' => 14, 'bold' => true, 'underline' => true],
                    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                ]);

                $event->sheet->getStyle('A4:N4')->applyFromArray([
                    'font' => ['bold' => true, 'italic' => true, 'size' => 10],
                    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                ]);

                // Formatting rows
                for ($index = $this->startRow; $index <= $this->endRow; $index++) {
                    $event->sheet->getStyle("A$index:N$index")->applyFromArray([
                        'font' => ['size' => 10],
                        'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                    ]);
                }
            },
        ];
    }

    public function array(): array
    {
        // Table Headers
        $data = [
            [$this->company->name],
            [''],
            ['OFFICERS/LEADERS REPORT'],
            ['(Data downloaded as of ' . now()->format('M d, Y h:i A') . ')'],
            [''],
            [''],
            [''],
            [''],
            ['FULL NAME', 'DATE OF REGISTRATION', 'FIRST NETWORK', 'SECOND NETWORK', 'THIRD NETWORK', 'FOURTH NETWORK', 'FIFT NETWORK'],
        ];

        $rows = [];
        $currentRow = $this->startRow;
        $firstRow = true;
        $seenBeneficiaryNames = [];

        // Officer details
        $officer = $this->request->get('officer');
        $officerFullName = $officer['full_name'] ?? ($officer['first_name'] . ' ' . $officer['middle_name'] . ' ' . $officer['last_name']);
        $officerRegisterDate = Carbon::parse($officer['date_registered'])->format('M d, Y');
        $totalBeneficiaries = count($this->data['beneficiaries']);

        foreach ($this->data['beneficiaries'] as $beneficiaryData) {
            $beneficiary = $beneficiaryData['beneficiary'] ?? null;
            $networks = $beneficiaryData['networks'] ?? [];
            
            $beneficiaryFullName = $beneficiary['full_name'] ?? 'Unknown';
            $beneficiaryNetworkCount = $beneficiary['networks_count'] ?? 0;
            $firstNetworkName = $beneficiaryFullName;

            
            if ($firstRow) {
                $rows[] = [
                    "$officerFullName ($totalBeneficiaries)", 
                    $officerRegisterDate, 
                    "$firstNetworkName ($beneficiaryNetworkCount)",  
                    "",
                    "",
                    ""
                ];
                $firstRow = false;
            } else {
                $rows[] = [
                    "",
                    "",
                    "$firstNetworkName ($beneficiaryNetworkCount)",
                    "",
                    "",
                    ""
                ];
            }

            // Now, loop through networks if they exist
            if (!empty($networks)) {
                foreach ($networks as $network) {
                    $networkBeneficiary2 =$network->beneficiary ?? null;

                   
                        $networkFullName = $networkBeneficiary2->last_name . ", " . $networkBeneficiary2->first_name;

                        $networkNetworkCount = $networkBeneficiary2->parentingNetworks()->count() ?? 0;

                        if( $networkBeneficiary2){
                            $rows[] = [
                                "",
                                "",
                                "",
                                "$networkFullName ($networkNetworkCount)",
                                "",
                                ""
                            ];
                            $thirdLevels = $networkBeneficiary2->parentingNetworks()->orderBy('order_no', 'asc')->get();
                            foreach($thirdLevels as $thirdLevel){
                                $thirdLevelBeneficiary = $thirdLevel->beneficiary ?? null;

                                if($thirdLevelBeneficiary){
                                    $thirdLevelFullName = $thirdLevelBeneficiary->last_name . ", " .  $thirdLevelBeneficiary->first_name;
                                    $thirdLevelCount =  $thirdLevelBeneficiary->parentingNetworks()->count() ?? 0;

                                    $rows[] = [
                                        "",
                                        "",
                                        "",
                                        "",
                                        " $thirdLevelFullName ($thirdLevelCount)",
                                        ""
                                    ];
                                }

                                $fourthLevels =  $thirdLevelBeneficiary->parentingNetworks()->orderBy('order_no', 'asc')->get();
                                foreach( $fourthLevels as $fourthLevel){
                                    $fourthLevelBeneficiary = $fourthLevel->beneficiary ?? null;
    
                                    if($fourthLevelBeneficiary){
                                        $fourthLevelFullName = $fourthLevelBeneficiary->last_name . ", " .  $fourthLevelBeneficiary->first_name;
                                        $fourthLevelCount =  $fourthLevelBeneficiary->parentingNetworks()->count() ?? 0;
    
                                        $rows[] = [
                                            "",
                                            "",
                                            "",
                                            "",
                                            " ",
                                            "$fourthLevelFullName ($fourthLevelCount)"
                                        ];
                                    }

                                    $fifthLevels =    $fourthLevelBeneficiary->parentingNetworks()->orderBy('order_no', 'asc')->get();
                                    foreach( $fifthLevels as $fifthLevel){
                                        $fifthLevelBeneficiary = $fifthLevel->beneficiary ?? null;
        
                                        if($fifthLevelBeneficiary){
                                            $fifthLevelFullName = $fifthLevelBeneficiary->last_name . ", " .  $fifthLevelBeneficiary->first_name;
                                            $fifthLevelCount =  $fifthLevelBeneficiary->parentingNetworks()->count() ?? 0;
        
                                            $rows[] = [
                                                "",
                                                "",
                                                "",
                                                "",
                                                " ",
                                                "",
                                                "$fifthLevelFullName ($fifthLevelCount)"
                                            ];
                                        }
                                    }
                                }
                            }
                        }
                       
                    
                    }
            }

            $currentRow++;
        }

        $this->endRow = $currentRow;
        return array_merge($data, $rows);
    }




}
