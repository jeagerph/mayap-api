<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

use App\Http\Repositories\Base\MemberRepository;
use App\Http\Repositories\Base\CompanyClassificationRepository;

class MembersImport implements ToCollection, WithHeadingRow
{
    public function __construct()
    {
        $this->memberRepository = new MemberRepository;
        $this->classificationRepository = new CompanyClassificationRepository;
    }

    public function headingRow(): int
    {
        return 2;
    }

    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        $company = \App\Models\Company::where('id', 4)->first();

        foreach($collection as $row):

            $last_name = $row['last_name']
                ? $row['last_name']
                : null;

            $first_name = $row['first_name']
                ? $row['first_name']
                : null;

            $date_of_birth = self::getDateOfBirth($row['date_of_birth']);
            

            

            if ($last_name && $first_name && $date_of_birth):

                $checking = $company->members()->where(function($q) use ($first_name, $last_name, $date_of_birth)
                                            {
                                                $q->where('first_name', $first_name)
                                                    ->where('last_name', $last_name)
                                                    ->where('date_of_birth', $date_of_birth);
                                            })->first();

                if (!$checking):

                    $date_registered = self::getDateRegistered($row['date_registered']);

                    $middle_name = $row['middle_name']
                        ? $row['middle_name']
                        : null;

                    $gender = self::getGender($row['gender']);

                    $civil_status = self::getCivilStatus($row['civil_status']);

                    $house_no = $row['house_no']
                        ? $row['house_no']
                        : null;

                    $province_id = '0369';

                    $city_id = self::getCity($row['city'], $province_id);

                    $barangay_id = self::getBarangay($row['barangay'], $province_id);

                    $contact_no = $row['contact_no']
                        ? $row['contact_no']
                        : '09';

                    $place_of_birth = $row['place_of_birth']
                        ? $row['place_of_birth']
                        : null;

                    $educational_attainment = $row['educational_attainment']
                        ? $row['educational_attainment']
                        : null;

                    $occupation = $row['occupation']
                        ? $row['occupation']
                        : null;

                    $monthly_income = $row['monthly_income']
                        ? $row['monthly_income']
                        : null;

                    $classification = $row['classification']
                        ? self::getClassification($row['classification'], $company)
                        : 1;

                    $religion = $row['religion']
                        ? $row['religion']
                        : null;

                    $remarks = $row['remarks']
                        ? $row['remarks']
                        : null;

                    $newMember = $this->memberRepository->store([
                        'date_registered' => $date_registered,
                        'province_id' => $province_id,
                        'city_id' => $city_id,
                        'barangay_id' => $barangay_id,
                        'house_no' => $house_no,
                        'first_name' => $first_name,
                        'middle_name' => $middle_name,
                        'last_name' => $last_name,
                        'gender' => $gender,
                        'contact_no' => $contact_no,
                        'place_of_birth' => $civil_status,
                        'date_of_birth' => $date_of_birth,
                        'civil_status' => $civil_status,
                        'religion' => $religion,
                        'educational_attainment' => $educational_attainment,
                        'occupation' => $occupation,
                        'monthly_income' => $monthly_income,
                        'company_classification_id' => $classification,
                        'suffix' => null,
                        'email' => null,
                        'address' => null,
                        'is_household' => 0,
                        'resident_type' => null,
                        'precinct_no' => null,
                        'citizenship' => null,
                        'eligibility' => null,
                        'blood_type' => null,
                        'health_history' => null,
                        'skills' => null,
                        'pending' => null,
                        'gsis_sss_no' => null,
                        'philhealth_no' => null,
                        'pagibig_no' => null,
                        'tin_no' => null,
                        'voters_no' => null,
                        'organ_donor' => null,
                        'primary_school' => null,
                        'primary_year_graduated' => null,
                        'secondary_school' => null,
                        'secondary_course' => null,
                        'secondary_year_graduated' => null,
                        'tertiary_school' => null,
                        'tertiary_course' => null,
                        'tertiary_year_graduated' => null,
                        'other_school' => null,
                        'other_course' => null,
                        'other_year_graduated' => null,
                        'work_status' => null,
                        'work_experiences' => [],
                        'monthly_income_start' => 0,
                        'monthly_income_end' => 0,
                        'emergency_contact_name' => null,
                        'emergency_contact_address' => null,
                        'emergency_contact_no' => null,
                    ], $company);

                    $this->memberRepository->updateAddress($newMember);

                endif;

            endif;

        endforeach;

        
    }

    private function getDateRegistered($dateRegisteredStr)
    {
        $dateRegistered = now()->format('Y-m-d');

        if ($dateRegisteredStr):

            $dateRegistered = (new \Carbon\Carbon($dateRegisteredStr))->format('Y-m-d');

        endif;

        return $dateRegistered;
    }

    private function getGender($genderStr)
    {
        $gender = 1;

        if ($genderStr == 'FEMALE') $gender = 2;

        return $gender;
    }

    private function getCivilStatus($civilStatusStr)
    {
        $civilStatus = null;

        if ( strpos($civilStatusStr, 'single') ):
            $civilStatus = 1;
            
        elseif ( strpos($civilStatusStr, 'marr') ):
            $civilStatus = 2;
            
        elseif ( strpos($civilStatusStr, 'divor') ):
            $civilStatus = 3;
            
        elseif ( strpos($civilStatusStr, 'separ') ):
            $civilStatus = 4;
            
        elseif ( strpos($civilStatusStr, 'wido') ):
            $civilStatus = 5;

        endif;

        return $civilStatus;
    }

    private function getBarangay($barangayStr, $provinceId)
    {
        $barangayId = 8166;

        $barangayModel = \App\Models\Barangay::where('name', 'LIKE', '%'.$barangayStr.'%')
                ->where('prov_code', $provinceId)
                ->first();

        if ($barangayModel):
            $barangayId = $barangayModel->id;
        endif;

        return $barangayId;
    }

    private function getCity($cityStr, $provinceId)
    {
        $cityId = '036916';

        $cityModel = \App\Models\City::where('name', 'LIKE', '%'.$cityStr.'%')
                ->where('prov_code', $provinceId)
                ->first();

        if ($cityModel):
            $cityId = $cityModel->city_code;
        endif;

        return $cityId;
    }

    private function getDateOfBirth($dateOfBirthStr)
    {
        $dateOfBirth = '1970-01-01';

        if ($dateOfBirthStr):

            $dateOfBirth = (new \Carbon\Carbon($dateOfBirthStr))->format('Y-m-d');

        endif;

        return $dateOfBirth;
    }

    private function getClassification($classificationStr, $company)
    {
        $checking = $company->classifications()->where('name', 'LIKE', '%'.$classificationStr.'%')->first();

        if (!$checking):
            $checking = $company->classifications()->save(
                $this->classificationRepository->new([
                    'name' => $classificationStr,
                    'description' => null,
                ], $company)
            );
        endif;

        return $checking->id;
    }
}
