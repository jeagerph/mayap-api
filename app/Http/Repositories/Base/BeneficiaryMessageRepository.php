<?php

namespace App\Http\Repositories\Base;

use Illuminate\Support\Facades\Auth;

use App\Models\BeneficiaryMessage;

class BeneficiaryMessageRepository
{
    public function new($data, $company)
    {
        return new BeneficiaryMessage([
            'company_id' => $company->id,
            'message_date' => $data['message_date'],
            'message_type' => $data['message_type'],
            'message_sender_name' => $data['message_sender_name'],
            'mobile_number' => $data['mobile_number'],
            'message' => $data['message'],
            'created_by' => Auth::id() ?: 1
        ]);
    }
}
?>