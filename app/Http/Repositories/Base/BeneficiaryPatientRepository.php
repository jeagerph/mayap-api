<?php

namespace App\Http\Repositories\Base;

use Illuminate\Support\Facades\Auth;

use App\Models\Slug;
use App\Models\BeneficiaryPatient;

class BeneficiaryPatientRepository
{
    public function new($data, $company)
    {
        return new BeneficiaryPatient([
            'company_id' => $company->id,
            'patient_date' => $data['patient_date'],
            'first_name' => strtoupper($data['first_name']),
            'middle_name' => $data['middle_name']
                ? strtoupper($data['middle_name'])
                : null,
            'last_name' => strtoupper($data['last_name']),
            'relation_to_patient' => $data['relation_to_patient'],
            'problem_presented' => $data['problem_presented'],
            'findings' => $data['findings'],
            'assessment_recommendation' => $data['assessment_recommendation'],
            'needs' => $data['needs'],
            'remarks' => $data['remarks'],
            'status' => 1,
            'created_by' => Auth::id() ?: 1
        ]);
    }

    public function store($data, $company)
    {
        $model = Slug::findCodeOrDie($data['beneficiaryCode']);

        $beneficiary = $model->slug;

        $newPatient = $beneficiary->patients()->save(
            $this->new($data, $company)
        );

        return $newPatient;
    }

    public function update($data)
    {
        return [
            'patient_date' => $data['patient_date'],
            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'],
            'last_name' => $data['last_name'],
            'relation_to_patient' => $data['relation_to_patient'],
            'problem_presented' => $data['problem_presented'],
            'findings' => $data['findings'],
            'assessment_recommendation' => $data['assessment_recommendation'],
            'needs' => $data['needs'],
            'remarks' => $data['remarks'],
            'updated_by' => Auth::id() ?: 1
        ];
    }
}
?>