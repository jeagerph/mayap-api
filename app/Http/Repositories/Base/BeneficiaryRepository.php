<?php

namespace App\Http\Repositories\Base;

use Illuminate\Support\Facades\Auth;

use App\Models\Beneficiary;
use App\Models\Slug;
use App\Models\Barangay;

use App\Http\Repositories\Base\SlugRepository;
use App\Http\Repositories\Base\CompanySmsTransactionRepository;
use App\Http\Repositories\Base\SystemSettingRepository;
use App\Http\Repositories\Base\CompanyRepository;

use App\Traits\FileStorage;

class BeneficiaryRepository
{
    use FileStorage;

    private $slugRepository;
    private $smsTransactionRepository;
    private $systemSettingRepository;
    private $companyRepository;

    public function __construct()
    {
        $this->slugRepository = new SlugRepository;
        $this->smsTransactionRepository = new CompanySmsTransactionRepository;
        $this->systemSettingRepository = new SystemSettingRepository;
        $this->companyRepository = new CompanyRepository;
    }

    public function store($data, $company)
    {
        $model = new Beneficiary;

        $model->code = self::generateCode($company, $data['barangay_id']);

        $model->company_id = $company->id;

        $model->date_registered = $data['date_registered'];

        $model->province_id = $data['province_id'];
        $model->city_id = $data['city_id'];
        $model->barangay_id = $data['barangay_id'];
        $model->house_no = $data['house_no'];
        $model->zone = isset($data['zone'])
            ? strtoupper($data['zone'])
            : null;
        $model->street = isset($data['street'])
            ? strtoupper($data['street'])
            : null;
        $model->purok = isset($data['purok'])
            ? strtoupper($data['purok'])
            : null;
        $model->landmark = isset($data['landmark'])
            ? strtoupper($data['landmark'])
            : null;
        $model->house_ownership = isset($data['house_ownership'])
            ? $data['house_ownership']
            : null;
        $model->house_ownership_remarks = isset($data['house_ownership_remarks'])
            ? $data['house_ownership_remarks']
            : null;

        $model->first_name = strtoupper($data['first_name']);
        $model->middle_name = $data['middle_name']
            ? strtoupper($data['middle_name'])
            : null;
        $model->last_name = strtoupper($data['last_name']);
        $model->gender = isset($data['gender'])
            ? $data['gender']
            : 1;
        $model->mobile_no = isset($data['mobile_no'])
            ? $data['mobile_no']
            : null;
        $model->email = isset($data['email'])
            ? $data['email']
            : null;

        $model->place_of_birth = isset($data['place_of_birth'])
            ? $data['place_of_birth']
            : null;
        $model->date_of_birth = isset($data['date_of_birth'])
            ? $data['date_of_birth']
            : null;
        $model->civil_status = isset($data['civil_status'])
            ? $data['civil_status']
            : null;
        $model->citizenship = isset($data['citizenship'])
            ? $data['citizenship']
            : null;
        $model->religion = isset($data['religion'])
            ? $data['religion']
            : null;

        $model->educational_attainment = isset($data['educational_attainment'])
            ? $data['educational_attainment']
            : null;
        $model->occupation = isset($data['occupation'])
            ? $data['occupation']
            : null;
        $model->monthly_income = isset($data['monthly_income'])
            ? $data['monthly_income']
            : null;
        $model->source_of_income = isset($data['source_of_income'])
            ? $data['source_of_income']
            : null;
        $model->classification = isset($data['classification'])
            ? $data['classification']
            : null;

        $model->primary_school = isset($data['primary_school'])
            ? $data['primary_school']
            : null;
        $model->primary_year_graduated = isset($data['primary_year_graduated'])
            ? $data['primary_year_graduated']
            : null;

        $model->secondary_school = isset($data['secondary_school'])
            ? $data['secondary_school']
            : null;
        $model->secondary_course = isset($data['secondary_course'])
            ? $data['secondary_course']
            : null;
        $model->secondary_year_graduated = isset($data['secondary_year_graduated'])
            ? $data['secondary_year_graduated']
            : null;

        $model->tertiary_school = isset($data['tertiary_school'])
            ? $data['tertiary_school']
            : null;
        $model->tertiary_course = isset($data['tertiary_course'])
            ? $data['tertiary_course']
            : null;
        $model->tertiary_year_graduated = isset($data['tertiary_year_graduated'])
            ? $data['tertiary_year_graduated']
            : null;

        $model->other_school = isset($data['other_school'])
            ? $data['other_school']
            : null;
        $model->other_course = isset($data['other_course'])
            ? $data['other_course']
            : null;
        $model->other_year_graduated = isset($data['other_year_graduated'])
            ? $data['other_year_graduated']
            : null;

        $model->is_household = isset($data['is_household'])
            ? $data['is_household']
            : 0;
        $model->household_count = isset($data['household_count'])
            ? $data['household_count']
            : 0;
        $model->household_voters_count = isset($data['household_voters_count'])
            ? $data['household_voters_count']
            : 0;
        $model->household_families_count = isset($data['household_families_count'])
            ? $data['household_families_count']
            : 0;
        $model->is_priority = isset($data['is_priority'])
            ? $data['is_priority']
            : 0;

        $model->emergency_contact_name = isset($data['emergency_contact_name'])
            ? $data['emergency_contact_name']
            : null;
        $model->emergency_contact_address = isset($data['emergency_contact_address'])
            ? $data['emergency_contact_address']
            : null;
        $model->emergency_contact_no = isset($data['emergency_contact_no'])
            ? $data['emergency_contact_no']
            : null;

        $model->health_issues = isset($data['health_issues'])
            ? $data['health_issues']
            : null;
        $model->problem_presented = isset($data['problem_presented'])
            ? $data['problem_presented']
            : null;
        $model->findings = isset($data['findings'])
            ? $data['findings']
            : null;
        $model->assessment_recommendation = isset($data['assessment_recommendation'])
            ? $data['assessment_recommendation']
            : null;
        $model->needs = isset($data['needs'])
            ? $data['needs']
            : null;
        $model->remarks = isset($data['remarks'])
            ? $data['remarks']
            : null;

        $model->questionnaires = isset($data['questionnaires']) && count($data['questionnaires'])
            ? json_encode($data['questionnaires'])
            : null;

        $model->latitude = isset($data['latitude'])
            ? $data['latitude']
            : null;
        $model->longitude = isset($data['longitude'])
            ? $data['longitude']
            : null;

        $model->is_voter = isset($data['is_voter'])
            ? $data['is_voter']
            : 0;
        $model->voter_type = isset($data['voter_type'])
            ? $data['voter_type']
            : 1;

        $model->created_by = Auth::id() ?: 1;
        $model->save();

        $model->slug()->save(
            $this->slugRepository->new(
                $model->first_name . ' ' . $model->last_name . ' Beneficiary'
            )
        );

        return $model;
    }

    public function bulkInsert($datas, $company)
    {
        $beneficiaries = [];
        $now = now();
        $createdBy = Auth::id() ?: 1;

        foreach ($datas as $data) {
            $existingBeneficiaries = Beneficiary::where('first_name', 'LIKE', '%' . strtoupper($data['first_name']) . '%')
                ->where('middle_name', 'LIKE', '%' . isset($data['middle_name']) ? strtoupper($data['middle_name']) : null . '%')
                ->where('last_name', 'LIKE', '%' . strtoupper($data['last_name']) . '%')->exists();

            if (!$existingBeneficiaries) {
                $beneficiaries[] = [
                    'code' => self::generateCode($company, $data['barangay_id']),
                    'company_id' => $company->id,
                    'date_registered' => $data['date_registered'],
                    'province_id' => $data['province_id'],
                    'city_id' => $data['city_id'],
                    'barangay_id' => $data['barangay_id'],
                    'house_no' => $data['house_no'],
                    'zone' => isset($data['zone']) ? strtoupper($data['zone']) : null,
                    'street' => isset($data['street']) ? strtoupper($data['street']) : null,
                    'purok' => isset($data['purok']) ? strtoupper($data['purok']) : null,
                    'landmark' => isset($data['landmark']) ? strtoupper($data['landmark']) : null,
                    'first_name' => strtoupper($data['first_name']),
                    'middle_name' => isset($data['middle_name']) ? strtoupper($data['middle_name']) : null,
                    'last_name' => strtoupper($data['last_name']),
                    'gender' => $data['gender'] ?? 1,
                    'mobile_no' => $data['mobile_no'] ?? null,
                    'email' => $data['email'] ?? null,
                    'latitude' => $data['latitude'] ?? null,
                    'longitude' => $data['longitude'] ?? null,
                    'created_by' => $createdBy,
                    'created_at' => $now,
                    'updated_at' => $now
                ];
            }

        }

        if (empty($beneficiaries)) {
            return null;
        }


        Beneficiary::insert($beneficiaries);


        $insertedBeneficiaries = Beneficiary::latest('id')
            ->with(['barangay', 'city', 'province'])
            ->limit(count($beneficiaries))
            ->get();


        $slugs = [];
        foreach ($insertedBeneficiaries as $beneficiary) {
            $fullName = $beneficiary->first_name . ' ' . $beneficiary->last_name . ' Beneficiary';
            $slug = $this->slugRepository->new($fullName);

            $slugs[] = [
                'beneficiary_id' => $beneficiary->id,
                'full' => $slug->full,
                'code' => $slug->code,
                'name' => $slug->name,
                'created_by' => $createdBy,
                'created_at' => $now,
                'updated_at' => $now
            ];
        }

        if (!empty($slugs)) {
            Slug::insert($slugs);
        }

        return $insertedBeneficiaries;
    }


    public function update($data)
    {
        return [
            'province_id' => $data['province_id'],
            'city_id' => $data['city_id'],
            'barangay_id' => $data['barangay_id'],
            'house_no' => $data['house_no'],
            'zone' => $data['zone'],
            'purok' => $data['purok'],
            'street' => $data['street'],
            'landmark' => $data['landmark'],
            'house_ownership' => $data['house_ownership'],
            'house_ownership_remarks' => $data['house_ownership_remarks'],

            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'],
            'last_name' => $data['last_name'],
            'gender' => $data['gender'],
            'mobile_no' => $data['mobile_no'],
            'email' => $data['email'],

            'place_of_birth' => $data['place_of_birth'],
            'date_of_birth' => $data['date_of_birth'],
            'civil_status' => $data['civil_status'],
            'citizenship' => $data['citizenship'],
            'religion' => $data['religion'],

            'educational_attainment' => $data['educational_attainment'],
            'occupation' => $data['occupation'],
            'monthly_income' => $data['monthly_income'],
            'source_of_income' => $data['source_of_income'],
            'classification' => $data['classification'],

            'is_household' => $data['is_household'],
            'household_count' => $data['household_count'],
            'household_voters_count' => $data['household_voters_count'],
            'household_families_count' => $data['household_families_count'],
            'is_priority' => $data['is_priority'],

            'primary_school' => $data['primary_school'],
            'primary_year_graduated' => $data['primary_year_graduated'],
            'secondary_school' => $data['secondary_school'],
            'secondary_course' => $data['secondary_course'],
            'secondary_year_graduated' => $data['secondary_year_graduated'],
            'tertiary_school' => $data['tertiary_school'],
            'tertiary_course' => $data['tertiary_course'],
            'tertiary_year_graduated' => $data['tertiary_year_graduated'],
            'other_school' => $data['other_school'],
            'other_course' => $data['other_course'],
            'other_year_graduated' => $data['other_year_graduated'],

            'emergency_contact_name' => $data['emergency_contact_name'],
            'emergency_contact_address' => $data['emergency_contact_address'],
            'emergency_contact_no' => $data['emergency_contact_no'],

            'health_issues' => $data['health_issues'],
            'problem_presented' => $data['problem_presented'],
            'findings' => $data['findings'],
            'assessment_recommendation' => $data['assessment_recommendation'],
            'needs' => $data['needs'],
            'remarks' => $data['remarks'],

            'questionnaires' => count($data['questionnaires'])
                ? json_encode($data['questionnaires'])
                : null,

            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],

            'is_voter' => $data['is_voter'],
            'voter_type' => $data['voter_type'],

            'updated_by' => Auth::id()
        ];
    }

    public function uploadPhoto($currentPath, $data, $folderDir = 'beneficiary/photo')
    {
        if ($currentPath):
            $this->deleteFile($currentPath);
        endif;

        $photo = $data['photo'];

        $fileName = randomFileName();

        $filePath = randomFilePath($folderDir, $fileName);

        $this->saveFile($filePath, self::base64ToFile($photo), true);

        return [
            'photo' => $filePath,
            'updated_by' => Auth::id()
        ];
    }

    public function updateAddress($beneficiary)
    {
        $address = $beneficiary->house_no ? $beneficiary->house_no . ', ' : '';
        $address .= $beneficiary->zone ? $beneficiary->zone . ', ' : '';
        $address .= $beneficiary->street ? $beneficiary->street . ', ' : '';
        $address .= $beneficiary->purok ? $beneficiary->purok . ', ' : '';
        $address .= $beneficiary->barangay ? $beneficiary->barangay->name . ', ' : '';

        // Customization of address: Determined if Olongapo City beneficiary, not include province
        if ($beneficiary->city_id == '037107'):
            $address .= $beneficiary->city ? $beneficiary->city->name : '';
        else:
            $address .= $beneficiary->city ? $beneficiary->city->name . ', ' : '';
            $address .= $beneficiary->province ? $beneficiary->province->name : '';
        endif;

        $beneficiary->update([
            'address' => strtoupper($address),
            'updated_by' => Auth::id() ?: 1
        ]);
    }

    public function bulkUpdateAddress($beneficiaries)
    {
        $updatedBy = Auth::id() ?: 1;
        $now = now();

        $bulkUpdateData = [];

        foreach ($beneficiaries as $beneficiary) {
            $address = $beneficiary->house_no ? $beneficiary->house_no . ', ' : '';
            $address .= $beneficiary->zone ? $beneficiary->zone . ', ' : '';
            $address .= $beneficiary->street ? $beneficiary->street . ', ' : '';
            $address .= $beneficiary->purok ? $beneficiary->purok . ', ' : '';
            $address .= $beneficiary->barangay ? $beneficiary->barangay->name . ', ' : '';

            // Customization: Exclude province if city_id is '037107' (Olongapo City)
            if ($beneficiary->city_id == '037107') {
                $address .= $beneficiary->city ? $beneficiary->city->name : '';
            } else {
                $address .= $beneficiary->city ? $beneficiary->city->name . ', ' : '';
                $address .= $beneficiary->province ? $beneficiary->province->name : '';
            }

            $bulkUpdateData[] = [
                'id' => $beneficiary->id,
                'address' => strtoupper($address),
                'updated_by' => $updatedBy,
                'updated_at' => $now
            ];
        }

        if (!empty($bulkUpdateData)) {
            Beneficiary::upsert($bulkUpdateData, ['id'], ['address', 'updated_by', 'updated_at']);
        }

        return true;
    }
    public function updateAssistancesCount($beneficiary)
    {
        $count = $beneficiary->assistances()->count();

        $beneficiary->update([
            'assistances_count' => $count,
            'updated_by' => Auth::id() ?: 1
        ]);
    }

    public function generateCode($company, $barangayId)
    {
        $barangay = Barangay::where('id', $barangayId)->first();

        $latestBeneficiary = Beneficiary::where('barangay_id', $barangay->id)->orderBy('id', 'desc')->first();

        $nextNumber = $latestBeneficiary ? ((int) str_replace('BNF-' . $barangay->psgc_code . '-', '', $latestBeneficiary->code) + 1) : 1;

        $code = 'BNF-';
        $code .= $barangay->psgc_code . '-';
        $code .= leadingZeros($nextNumber++);

        return $code;
    }

    public function refreshIncentives($beneficiary)
    {
        $replenish = $beneficiary->incentives()->where('mode', 1)->sum('points');
        $withdraw = $beneficiary->incentives()->where('mode', 2)->sum('points');

        $total = $replenish - $withdraw;

        $beneficiary->update([
            'incentive' => $total,
            'updated_by' => Auth::id() ?: 1
        ]);
    }

    public function isAllowedToUpdate($beneficiary)
    {
        $company = Auth::user()->company();

        if ($beneficiary->company_id != $company->id):

            return abort(403, 'Forbidden: Beneficiary is not under your account.');

        endif;
    }

    public function isAllowedToDestroy($beneficiary)
    {
        if ($beneficiary->network):
            return abort(403, 'Forbidden: Beneficiary contains related network.');
        endif;

        if ($beneficiary->parentingNetworks->count()):
            return abort(403, 'Forbidden: Beneficiary contains related parenting networks.');
        endif;

        if ($beneficiary->relatedRelatives->count()):
            return abort(403, 'Forbidden: Beneficiary contains related relatives');
        endif;

        if ($beneficiary->relatives->count() || $beneficiary->relatedRelatives->count()):
            return abort(403, 'Forbidden: Beneficiary contains related family/relative.');
        endif;

        if ($beneficiary->assistances->count()):
            return abort(403, 'Forbidden: Beneficiary contains related assistances.');
        endif;

        if ($beneficiary->patients->count()):
            return abort(403, 'Forbidden: Beneficiary contains related patients.');
        endif;

        if ($beneficiary->incentives->count()):
            return abort(403, 'Forbidden: Beneficiary contains related incentives.');
        endif;
    }

    public function isRelativeAlreadyExists($beneficiary, $relativeId)
    {
        $checking = $beneficiary->relatives()->where('related_beneficiary_id', $relativeId)->first();

        if ($checking)
            return abort(403, 'Selected relative already exists.');
    }

    public function isRelativeRelated($beneficiary, $id)
    {
        $checking = $beneficiary->relatives()->where('id', $id)->first();

        if (!$checking)
            return abort(404, 'Relative is not related to beneficiary.');

        return $checking;
    }

    public function isValidRelative($beneficiary, $id)
    {
        if ($beneficiary->id == $id)
            return abort(403, 'Selected relative is the same with beneficiary.');
    }

    public function isParentingNetworkRelated($beneficiary, $networkId)
    {
        $checking = $beneficiary->parentingNetworks()->where('id', $networkId)->first();

        if (!$checking)
            return abort(404, 'Network is not related to beneficiary.');

        return $checking;
    }

    public function isIncentiveRelated($beneficiary, $incentiveId)
    {
        $checking = $beneficiary->incentives()->where('id', $incentiveId)->first();

        if (!$checking)
            return abort(404, 'Incentive is not related to beneficiary.');

        return $checking;
    }

    public function isAssistanceRelated($beneficiary, $assistanceId)
    {
        $checking = $beneficiary->assistances()->where('id', $assistanceId)->first();

        if (!$checking)
            return abort(404, 'Assistance is not related to beneficiary.');

        return $checking;
    }

    public function isPatientRelated($beneficiary, $patientId)
    {
        $checking = $beneficiary->patients()->where('id', $patientId)->first();

        if (!$checking)
            return abort(404, 'Patient is not related to beneficiary.');

        return $checking;
    }

    public function isIdentificationRelated($beneficiary, $identificationId)
    {
        $checking = $beneficiary->identifications()->where('id', $identificationId)->first();

        if (!$checking)
            return abort(404, 'ID is not related to beneficiary.');

        return $checking;
    }

    public function isDocumentRelated($beneficiary, $documentId)
    {
        $checking = $beneficiary->documents()->where('id', $documentId)->first();

        if (!$checking)
            return abort(404, 'Document is not related to beneficiary.');

        return $checking;
    }

    public function isCallRelated($beneficiary, $callId)
    {
        $checking = $beneficiary->calls()->where('id', $callId)->first();

        if (!$checking)
            return abort(404, 'Call is not related to beneficiary.');

        return $checking;
    }

    public function isFamilyRelated($beneficiary, $familyId)
    {
        $checking = $beneficiary->families()->where('id', $familyId)->first();

        if (!$checking)
            return abort(404, 'Family/relative is not related to beneficiary.');

        return $checking;
    }
}
?>