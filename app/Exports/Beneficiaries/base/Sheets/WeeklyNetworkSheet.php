<?php

namespace App\Exports\Beneficiaries\Base\Sheets;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class WeeklyNetworkSheet implements FromArray, WithTitle, WithEvents
{
    private $dates;
    private $company;
    private $request;
    private $startRow;
    private $data;
    private $endRow;

    public function __construct($data)
    {
        $this->company = $data['company'];
        $this->request = $data['request'];
        $this->data = $data;
        $this->startRow = 10;
        $this->endRow = 1000;
        $this->dates = $data['dates'];
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
                $event->sheet->mergeCells('A5:N5');
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
                $event->sheet->getStyle('A5:N5')->applyFromArray([
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
            ['Total Officer that has new network for the week: '.count($this->data['beneficiaries'])],
            [''],
            [''],
            [''],
            ['Network Of', 'DATE OF REGISTRATION', 'Network List', 'Network Registered At'],
        ];

        $rows = [];
        $currentRow = $this->startRow;
    
        $officer = $this->request->get('officer');
        $dates =  $this->dates;

        foreach ($this->data['beneficiaries'] as $officer) {
            $officerFullName  = $officer["full_name"] ?? $officer["last_name"].' '.$officer["first_name"].' '.$officer["middle_name"];
              $networks = $officer->parentingNetworks()->whereBetween('created_at', $dates)->orderBy('order_no', 'asc')->get();
              $officerRegisterDate = Carbon::parse($officer['date_registered'])->format('M d, Y');
          
                $rows[] = [
                    " $officerFullName", 
                   
                    "$officerRegisterDate",  
                    "",
                    "",
                    ""
                ];
              
           

            if (!empty($networks)) {
              foreach ($networks as $network) {
                  $networkBeneficiary2 =$network->beneficiary ?? null;

                 
                      $networkFullName = $networkBeneficiary2->last_name . ", " . $networkBeneficiary2->first_name;

                    
                      $networkRegisterDate = Carbon::parse( $networkBeneficiary2['created_at'])->format('M d, Y');
                      if( $networkBeneficiary2){
                          $rows[] = [
                              "",
                              "",
                              "$networkFullName",
                              "$networkRegisterDate",
                             
                          ];
                        
                      }
                     
                  
                  }
          }
            $currentRow++;
        }

        $this->endRow = $currentRow;
        return array_merge($data, $rows);
    }




}
