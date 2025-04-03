<?php

namespace App\Http\Repositories\Base;

use Illuminate\Support\Facades\Auth;

use App\Models\Member;
use App\Models\Province;
use App\Models\City;
use App\Models\Barangay;

use App\Http\Repositories\Base\SlugRepository;
use App\Http\Repositories\Base\CompanySmsTransactionRepository;
use App\Http\Repositories\Base\SystemSettingRepository;
use App\Http\Repositories\Base\CompanyRepository;

use App\Traits\FileStorage;

class MemberRepository
{
    use FileStorage;
    
    public function __construct()
    {
        $this->slugRepository = new SlugRepository;
        $this->smsTransactionRepository = new CompanySmsTransactionRepository;
        $this->systemSettingRepository = new SystemSettingRepository;
        $this->companyRepository = new CompanyRepository;
    }

    public function store($data, $company)
    {   
        $model = new Member;

        $model->code = self::generateCode($company);

        $model->company_id = $company->id;

        $model->date_registered = $data['date_registered'];

        $model->company_classification_id = $data['company_classification_id'];

        $model->province_id = $data['province_id'];
        $model->city_id = $data['city_id'];
        $model->barangay_id = $data['barangay_id'];
        $model->house_no = $data['house_no'];

        $model->first_name = strtoupper($data['first_name']);
        $model->middle_name = $data['middle_name']
            ? strtoupper($data['middle_name'])
            : null;
        $model->last_name = strtoupper($data['last_name']);
        $model->suffix = $data['suffix']
            ? strtoupper($data['suffix'])
            : null;
        $model->gender = $data['gender'];
        $model->contact_no = $data['contact_no'] ?: null;
        $model->email = $data['email'] ?: null;
        $model->address = $data['address'];

        $model->place_of_birth = $data['place_of_birth'] ?: null;
        $model->date_of_birth = $data['date_of_birth'] ?: null;
        $model->civil_status = $data['civil_status'] ?: null;

        $model->is_household = $data['is_household'] ?: 0;
        $model->resident_type = $data['resident_type'] ?: null;
        $model->precinct_no = $data['precinct_no'] ?: null;
        $model->citizenship = $data['citizenship'] ?: null;
        $model->religion = $data['religion'] ?: null;
        $model->eligibility = $data['eligibility'] ?: null;
        $model->blood_type = $data['blood_type'] ?: null;

        $model->health_history = $data['health_history'] ?: null;
        $model->skills = $data['skills'] ?: null;
        $model->pending = $data['pending'] ?: null;

        $model->gsis_sss_no = $data['gsis_sss_no'] ?: null;
        $model->philhealth_no = $data['philhealth_no'] ?: null;
        $model->pagibig_no = $data['pagibig_no'] ?: null;
        $model->tin_no = $data['tin_no'] ?: null;
        $model->voters_no = $data['voters_no'] ?: null;
        $model->organ_donor = $data['organ_donor'] ?: 'NO';

        $model->primary_school = $data['primary_school'] ?: null;
        $model->primary_year_graduated = $data['primary_year_graduated'] ?: null;

        $model->secondary_school = $data['secondary_school'] ?: null;
        $model->secondary_course = $data['secondary_course'] ?: null;
        $model->secondary_year_graduated = $data['secondary_year_graduated'] ?: null;

        $model->tertiary_school = $data['tertiary_school'] ?: null;
        $model->tertiary_course = $data['tertiary_course'] ?: null;
        $model->tertiary_year_graduated = $data['tertiary_year_graduated'] ?: null;

        $model->other_school = $data['other_school'] ?: null;
        $model->other_course = $data['other_course'] ?: null;
        $model->other_year_graduated = $data['other_year_graduated'] ?: null;
        
        $model->work_status = $data['work_status'] ?: null;
        $model->work_experiences = count($data['work_experiences'])
            ? json_encode($data['work_experiences'])
            : null;
        $model->monthly_income_start = $data['monthly_income_start'] ?: 0.00;
        $model->monthly_income_end = $data['monthly_income_end'] ?: 0.00;

        $model->emergency_contact_name = $data['emergency_contact_name'] ?: null;
        $model->emergency_contact_address = $data['emergency_contact_address'] ?: null;
        $model->emergency_contact_no = $data['emergency_contact_no'] ?: null;

        $model->educational_attainment = isset($data['educational_attainment'])
            ? strtoupper($data['educational_attainment'])
            : null;
        $model->occupation = isset($data['occupation'])
            ? strtoupper($data['occupation'])
            : null;
        $model->monthly_income = isset($data['monthly_income'])
            ? $data['monthly_income']
            : null;
        $model->remarks = isset($data['remarks'])
            ? $data['remarks']
            : null;

        $model->created_by = Auth::id() ?: 1;
        $model->save();

        $model->slug()->save(
            $this->slugRepository->new(
                $model->first_name . ' ' . $model->last_name . ' Member'
            )
        );

        return $model;
    }

    public function update($data)
    {
        return [
            'company_classification_id' => $data['company_classification_id'],
            'province_id' => $data['province_id'],
            'city_id' => $data['city_id'],
            'barangay_id' => $data['barangay_id'],
            'house_no' => $data['house_no'],
            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'],
            'last_name' => $data['last_name'],
            'gender' => $data['gender'],
            'contact_no' => $data['contact_no'],
            'email' => $data['email'],
            'address' => $data['address'],
            'place_of_birth' => $data['place_of_birth'],
            'date_of_birth' => $data['date_of_birth'],
            'civil_status' => $data['civil_status'],
            'citizenship' => $data['citizenship'],
            'religion' => $data['religion'],
            'eligibility' => $data['eligibility'],
            'blood_type' => $data['blood_type'],
            'health_history' => $data['health_history'],
            'skills' => $data['skills'],
            'pending' => $data['pending'],
            'emergency_contact_name' => $data['emergency_contact_name'],
            'emergency_contact_address' => $data['emergency_contact_address'],
            'emergency_contact_no' => $data['emergency_contact_no'],
            'precinct_no' => $data['precinct_no'],
            'is_household' => $data['is_household'],
            'resident_type' => $data['resident_type'],
            'gsis_sss_no' => $data['gsis_sss_no'],
            'philhealth_no' => $data['philhealth_no'],
            'pagibig_no' => $data['pagibig_no'],
            'tin_no' => $data['tin_no'],
            'voters_no' => $data['voters_no'],
            'organ_donor' => $data['organ_donor'],
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
            'work_status' => $data['work_status'],
            'work_experiences' => count($data['work_experiences'])
                ? json_encode($data['work_experiences'])
                : null,
            'monthly_income_start' => $data['monthly_income_start'],
            'monthly_income_end' => $data['monthly_income_end'],
            'updated_by' => Auth::id()
        ];
    }

    public function uploadPhoto($currentPath, $data, $folderDir = 'member/photo')
    {
        if($currentPath):
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

    public function uploadThumbmark($photo, $input, $folderDir = 'member/thumbmark')
    {
        $fileName = randomFileName();

        $filePath = randomFilePath($folderDir, $fileName);

        $this->saveFile($filePath, self::base64ToFile($photo), true);
        
        return [
            $input => $filePath,
            'updated_by' => Auth::id()
        ];
    }

    public function updateAddress($member)
    {
        $address = $member->house_no ? $member->house_no . ', ' : '';
        $address .= $member->street ? $member->street->name . ', ' : '';
        $address .= $member->barangay ? $member->barangay->name . ', ' : '';
        // $address .= $member->city ? $member->city->name . ', ' : '';
        // $address .= $member->province ? $member->province->name : '';

        // Customization of address: Determined if Olongapo City member, not include province
        if($member->city_id == '037107'):
            $address .= $member->city ? $member->city->name : '';
        else:
            $address .= $member->city ? $member->city->name . ', ' : '';
            $address .= $member->province ? $member->province->name : '';
        endif;

        $member->update([
            'address' => strtoupper($address),
            'updated_by' => Auth::id() ?: 1
        ]);
    }

    public function generateCode($company)
    {
        $count = Member::where('company_id', $company->id)->count();

        return 'MEMBER-' .$company->id.'-'. leadingZeros($count+1);
    }

    public function isAllowedToUpdate($member)
    {
        $company = Auth::user()->company();

        if($member->company_id != $company->id):

            return abort(403, 'Forbidden: Member is not under your account.');

        endif;
    }

    public function isAllowedToDestroy($member)
    {
        // if($member->documents->count()):
        //     return abort(403, 'Forbidden: Member contains related documents.');
        // endif;

        // if($member->identifications->count()):
        //     return abort(403, 'Forbidden: Member contains related IDs.');
        // endif;

        // if($member->relatives->count() || $member->relatedRelatives->count()):
        //     return abort(403, 'Forbidden: Member contains related family/relative.');
        // endif;
    }

    // public function hasBarangayIDTemplate($resident)
    // {
    //     $profile = $resident->barangayProfile;

    //     $idTemplate = $profile->identificationTemplates()->latest()->first();

    //     if(!$idTemplate)
    //         return abort(404, 'Barangay has no ID template yet.');

    //     return $idTemplate;
    // }

    public function hasValidContactNo($member)
    {
        if (!$member->contact_no)
            return abort(403, 'Forbidden: member has no contact number.');

        if ($member->contact_no[0] == '0' && $member->contact_no[1] == '9' && strlen($member->contact_no) != 11)
            return abort(403, 'Forbidden: Member has invalid contact number.');
    }

    public function isRelativeAlreadyExists($resident, $relativeId)
    {
        $checking = $resident->relatives()->where('related_resident_id', $relativeId)->first();

        if ($checking) return abort(403, 'Selected relative already exists.');
    }

    public function isRelativeRelated($resident, $id)
    {
        $checking = $resident->relatives()->where('id', $id)->first();

        if (!$checking) return abort(404, 'Relative is not related to resident.');

        return $checking;
    }

    public function isValidRelative($resident, $id)
    {
        if ($resident->id == $id)
            return abort(403, 'Selected relative is the same with resident.');
    }

    public function isAttachmentRelated($resident, $id)
    {
        $checking = $resident->attachments()->where('id', $id)->first();

        if (!$checking) return abort(404, 'Attachment is not related to resident.');

        return $checking;
    }
}
?>