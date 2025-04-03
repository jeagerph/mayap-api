<?php

namespace App\Http\Repositories\Base;

use Illuminate\Support\Facades\Auth;

use App\Models\Slug;
use App\Models\Beneficiary;
use App\Models\BeneficiaryRelative;

class BeneficiaryRelativeRepository
{
    public function new($beneficiary, $relative, $relationship)
    {
        $checking = $beneficiary->relatives()->orderBy('order_no', 'desc')->first();
        $orderNo = 1;

        if($checking) $orderNo = $checking->order_no + 1;

        return new BeneficiaryRelative([
            'order_no' => $orderNo,
            'related_beneficiary_id' => $relative->id,
            'relationship' => $relationship
                ? strtoupper($relationship)
                : 'NOT INDICATED',
            'created_by' => Auth::id() ?: 1
        ]);
    }
    
    public function refreshOrderNo($beneficiary)
    {
        $relatives = $beneficiary->relatives()->orderBy('order_no', 'asc')->get();
        $orderNo = 1;

        foreach($relatives as $relative):

            $relative->update([
                'order_no' => $orderNo,
                'updated_by' => Auth::id()
            ]);

            $orderNo++;

        endforeach;
    }
}
?>