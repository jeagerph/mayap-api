<?php

namespace App\Http\Repositories\Base;

use Illuminate\Support\Facades\Auth;

use App\Models\Activity;

class ActivityRepository
{
    public function new($data)
    {
        $companyId = null;

        if(Auth::id()):
            $accountType = Auth::user()->account->account_type;

            if($accountType == 2):
                $companyId = Auth::user()->company()->id;
            endif;

        endif;

        return new Activity([
            'description' => $data['description'],
            'action' => $data['action'],
            'data' => $data['data']
                ? json_encode($data['data'])
                : null,
            'audit_by' => Auth::id() ? Auth::user()->account->id : 1,
            'audit_at' => now(),
            'audit_company_id' => $companyId,
            'created_by' => Auth::id() ?: 1
        ]);
    }
}
?>