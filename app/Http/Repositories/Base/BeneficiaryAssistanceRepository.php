<?php

namespace App\Http\Repositories\Base;

use Illuminate\Support\Facades\Auth;

use App\Models\BeneficiaryAssistance;
use App\Models\Slug;

class BeneficiaryAssistanceRepository
{
    public function new($data, $company)
    {
        return new BeneficiaryAssistance([
            'company_id' => $company->id,
            'assistance_date' => $data['assistance_date'],
            'assistance_type' => $data['assistance_type'],
            'remarks' => $data['remarks'],
            'is_assisted' => $data['is_assisted'],
            'assisted_date' => $data['is_assisted']
                ? $data['assisted_date']
                : null,
            'assisted_by' => $data['is_assisted']
                ? $data['assisted_by']
                : null,
            'assistance_from' => isset($data['assistance_from'])
                ? $data['assistance_from']
                : null,
            'assistance_amount' => isset($data['assistance_amount'])
                ? $data['assistance_amount']
                : 0,
            'province_id' => isset($data['province_id'])
                ? $data['province_id']
                : null,
            'city_id' => isset($data['city_id'])
                ? $data['city_id']
                : null,
            'barangay_id' => isset($data['barangay_id'])
                ? $data['barangay_id']
                : null,
            'created_by' => Auth::id() ?: 1
        ]);
    }

    public function store($data, $company)
    {
        $model = Slug::findCodeOrDie($data['beneficiaryCode']);

        $beneficiary = $model->slug;

        $data['province_id'] = $beneficiary->province_id;
        $data['city_id'] = $beneficiary->city_id;
        $data['barangay_id'] = $beneficiary->barangay_id;

        $newAssistance = $beneficiary->assistances()->save(
            $this->new($data, $company)
        );

        return $newAssistance;
    }

    public function storeBeneficiary($data, $beneficiary)
    {
        $data['province_id'] = $beneficiary->province_id;
        $data['city_id'] = $beneficiary->city_id;
        $data['barangay_id'] = $beneficiary->barangay_id;

        $newAssistance = $beneficiary->assistances()->save(
            $this->new($data, $beneficiary->company)
        );

        return $newAssistance;
    }

    public function update($data)
    {
        return [
            'assistance_date' => $data['assistance_date'],
            'assistance_type' => $data['assistance_type'],
            'remarks' => $data['remarks'],
            'is_assisted' => $data['is_assisted'],
            'assisted_date' => $data['is_assisted']
                ? $data['assisted_date']
                : null,
            'assisted_by' => $data['is_assisted']
                ? $data['assisted_by']
                : null,
            'assistance_from' => $data['assistance_from']
                ? $data['assistance_from']
                : null,
            'assistance_amount' => $data['assistance_amount']
                ? $data['assistance_amount']
                : 0,
            'updated_by' => Auth::id() ?: 1
        ];
    }
}
?>