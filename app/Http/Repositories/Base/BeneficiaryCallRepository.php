<?php

namespace App\Http\Repositories\Base;

use Illuminate\Support\Facades\Auth;

use App\Models\BeneficiaryCall;

class BeneficiaryCallRepository
{
    public function new($data, $company)
    {
        return new BeneficiaryCall([
            'company_id' => $company->id,
            'call_date' => $data['call_date'],
            'mobile_number' => $data['mobile_number'],
            'call_minutes' => 0.00,
            'call_url' => null,
            'company_call_transaction_id' => $data['company_call_transaction_id'],
            'status' => $data['status'],
            'created_by' => Auth::id() ?: 1
        ]);
    }
}
?>