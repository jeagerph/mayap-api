<?php

namespace App\Http\Repositories\Base;

use Illuminate\Support\Facades\Auth;

use App\Models\BeneficiaryIncentive;

class BeneficiaryIncentiveRepository
{
    public function new($data, $company)
    {
        return new BeneficiaryIncentive([
            'company_id' => $company->id,
            'incentive_date' => $data['incentive_date'],
            'points' => $data['points'],
            'mode' => $data['mode'],
            'remarks' => $data['remarks'],
            'created_by' => Auth::id() ?: 1
        ]);
    }
}
?>