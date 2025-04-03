<?php

namespace App\Http\Repositories\Base;

use Illuminate\Support\Facades\Auth;

use App\Models\Slug;
use App\Models\Beneficiary;
use App\Models\BeneficiaryFamily;

class BeneficiaryFamilyRepository
{
    public function new($data, $beneficiary)
    {
        $checking = $beneficiary->relatives()->orderBy('order_no', 'desc')->first();
        $orderNo = 1;

        if($checking) $orderNo = $checking->order_no + 1;

        return new BeneficiaryFamily([
            'order_no' => $orderNo,
            'full_name' => strtoupper($data['full_name']),
            'date_of_birth' => $data['date_of_birth'],
            'gender' => $data['gender'],
            'education' => isset($data['education'])
                ? strtoupper($data['education'])
                : null,
            'occupation' => isset($data['occupation'])
                ? strtoupper($data['occupation'])
                : null,
            'address' => isset($data['address'])
                ? strtoupper($data['address'])
                : null,
            'relationship' => $data['relationship']
                ? strtoupper($data['relationship'])
                : 'NOT INDICATED',
            'created_by' => Auth::id() ?: 1
        ]);
    }
    
    public function refreshOrderNo($beneficiary)
    {
        $families = $beneficiary->families()->orderBy('order_no', 'asc')->get();
        $orderNo = 1;

        foreach($families as $family):

            $family->update([
                'order_no' => $orderNo,
                'updated_by' => Auth::id()
            ]);

            $orderNo++;

        endforeach;
    }

    public function update($data)
    {
        return [
            'full_name' => strtoupper($data['full_name']),
            'date_of_birth' => $data['date_of_birth'],
            'gender' => $data['gender'],
            'education' => isset($data['education'])
                ? strtoupper($data['education'])
                : null,
            'occupation' => isset($data['occupation'])
                ? strtoupper($data['occupation'])
                : null,
            'address' => isset($data['address'])
                ? strtoupper($data['address'])
                : null,
            'relationship' => $data['relationship']
                ? strtoupper($data['relationship'])
                : 'NOT INDICATED',
        ];
    }
}
?>