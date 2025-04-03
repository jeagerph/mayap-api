<?php

namespace App\Http\Repositories\Base;

use Illuminate\Support\Facades\Auth;

use App\Models\CompanyClassification;

class CompanyClassificationRepository
{
    public function new($data, $company)
    {
        return new CompanyClassification([
            'name' => strtoupper($data['name']),
            'description' => $data['description'],
            'enabled' => 1,
            'created_by' => Auth::id() ?: 1
        ]);
    }

    public function update($data)
    {
        return [
            'name' => strtoupper($data['name']),
            'description' => $data['description'],
            'updated_by' => Auth::id() ?: 1
        ];
    }

    public function isAllowedToDelete($classification)
    {
        // if ($classification->beneficiaries->count()):
        //     return abort(403, 'Forbidden. Classification has related Beneficiary records. Kindly delete it first before deleting Classification.');
        // endif;
    }
}
?>