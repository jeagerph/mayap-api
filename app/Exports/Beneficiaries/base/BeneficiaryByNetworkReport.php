<?php

namespace App\Exports\Beneficiaries\base;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class BeneficiaryByNetworkReport implements WithMultipleSheets
{
    
    public function __construct($request, $company)
    {
        $this->company = $company;

        $this->request = $request;
    }
  
    public function sheets(): array
    {
        $sheets = [];

        $company = $this->company;
        $request = $this->request;

        $parentingNetworks = $request->parentingNetworks;
       
      $arrParentingNetworks = [];
        foreach ($parentingNetworks as $parentingNetwork):
            $secondNetworks = $parentingNetwork->beneficiary->parentingNetworks()->orderBy('order_no', 'asc')->get();
          $arrParentingNetworks[] = [
              'id' => $parentingNetwork->id,
              'beneficiary' => $parentingNetwork->beneficiary->toArrayBeneficiaryNetworksRelated(),
              'networks' => $secondNetworks
          ];

         
      endforeach;
 
        $sheets[] = new \App\Exports\Beneficiaries\base\Sheets\BeneficiaryByNetworkSheet([
            'company' => $this->company,
            'request' => $this->request,
            'beneficiaries' => $arrParentingNetworks,

        ]);

        return $sheets;
    }

  
}