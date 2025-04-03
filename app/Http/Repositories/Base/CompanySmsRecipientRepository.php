<?php

namespace App\Http\Repositories\Base;

use Illuminate\Support\Facades\Auth;

use App\Models\CompanySmsRecipient;

use App\Traits\MeSender;

class CompanySmsRecipientRepository
{
    use MeSender;

    public function new($data)
    {
        return new CompanySmsRecipient([
            'mobile_number' => $data['mobile_number'],
            'message' => $data['message'],
            'status' => 1,
            'status_code' => null,
            'sent_at' => null,
            'failure_message' => null,
            'created_by' => Auth::id() ?: 1
        ]);
    }
}
?>