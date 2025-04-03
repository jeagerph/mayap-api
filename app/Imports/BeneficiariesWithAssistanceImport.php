<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

use App\Http\Repositories\Base\BeneficiaryRepository;
use App\Http\Repositories\Base\CompanyClassificationRepository;
use App\Http\Repositories\Base\BeneficiaryAssistanceRepository;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class BeneficiariesWithAssistanceImport implements ToCollection, WithHeadingRow
{
    public function __construct()
    {
        $this->beneficiaryRepository = new BeneficiaryRepository;
        $this->classificationRepository = new CompanyClassificationRepository;
        $this->assistanceRepository = new BeneficiaryAssistanceRepository;
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

            

            $date_of_birth = self::getDateOfBirth($row['date_of_birth'], $row);

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
                ? ('0' . $row['contact_no'])
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
                : null;

            $religion = $row['religion']
                ? $row['religion']
                : null;

            $typeOfAssistance = $row['type_of_assistance']
                ? $row['type_of_assistance']
                : null;
            
            $assistanceRemarks = $row['assistance_remarks']
                ? $row['assistance_remarks']
                : null;
            
            $remarks = $row['assistance_remarks']
                ? $row['assistance_remarks']
                : null;
            
            if ($date_of_birth):
                if ($last_name && $first_name && $date_of_birth):

                    $checking = $company->beneficiaries()->where(function($q) use ($first_name, $last_name, $date_of_birth)
                                                {
                                                    $q->where('first_name', $first_name)
                                                        ->where('last_name', $last_name)
                                                        ->where('date_of_birth', $date_of_birth);
                                                })->first();
    
                    if (!$checking):
    
                        
    
                        $newBeneficiary = $this->beneficiaryRepository->store([
                            'date_registered' => $date_registered,
    
                            'province_id' => $province_id,
                            'city_id' => $city_id,
                            'barangay_id' => $barangay_id,
                            'house_no' => $house_no,
    
                            'first_name' => $first_name,
                            'middle_name' => $middle_name,
                            'last_name' => $last_name,
                            'gender' => $gender,
                            'mobile_no' => $contact_no,
                            'email' => null,
    
                            'place_of_birth' => $place_of_birth,
                            'date_of_birth' => $date_of_birth,
                            'civil_status' => $civil_status,
                            'citizenship' => null,
                            'religion' => $religion,
    
                            'educational_attainment' => $educational_attainment,
                            'occupation' => $occupation,
                            'monthly_income' => $monthly_income,
                            'source_of_income' => null,
                            'classification' => $classification,
    
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
                        
                            'is_household' => 0,
                            'household_count' => 0,
                            'is_priority' => 0,
    
                            'health_issues' => null,
                            'problem_presented' => null,
                            'findings' => null,
                            'assessment_recommendation' => null,
                            'needs' => null,
                            'remarks' => $remarks,
    
                            'questionnaires' => [],
                            
                            'emergency_contact_name' => null,
                            'emergency_contact_address' => null,
                            'emergency_contact_no' => null,

                            'latitude' => null,
                            'longitude' => null,
    
                        ], $company);
    
                        $this->beneficiaryRepository->updateAddress($newBeneficiary);
    
                        if (
                            $typeOfAssistance &&
                            $typeOfAssistance != 'NA' &&
                            $typeOfAssistance != 'N/A' &&
                            $typeOfAssistance != 'N A'
                        ):
    
                            $newBeneficiary->assistances()->save(
                                $this->assistanceRepository->new([
                                    'assistance_date' => $date_registered,
                                    'assistance_type' => $typeOfAssistance,
                                    'remarks' => $assistanceRemarks,
                                    'assisted_date' => null,
                                    'is_assisted' => 0,
                                    'assisted_by' => null,
                                ], $company)
                            );
    
                        endif;
    
                    else:
    
                        if (
                            $typeOfAssistance &&
                            $typeOfAssistance != 'NA' &&
                            $typeOfAssistance != 'N/A' &&
                            $typeOfAssistance != 'N A'
                        ):
    
                            $checkingAssistance = $checking->assistances()->where('assistance_type', $typeOfAssistance)->where('remarks', $assistanceRemarks)->first();
    
                            if (!$checkingAssistance):
                                $checking->assistances()->save(
                                    $this->assistanceRepository->new([
                                        'assistance_date' => $date_registered,
                                        'assistance_type' => $typeOfAssistance,
                                        'remarks' => $assistanceRemarks,
                                        'assisted_date' => null,
                                        'is_assisted' => 0,
                                        'assisted_by' => null,
                                    ], $company)
                                );
                            endif;
    
                            
    
                        endif;
    
                    endif;
    
                endif;
            endif;

        endforeach;

        
    }

    private function getDateRegistered($dateRegisteredStr)
    {
        $dateRegistered = now()->format('Y-m-d');

        if ($dateRegisteredStr):

            if (gettype($dateRegisteredStr) == 'integer'):
                $dateRegistered = (\Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateRegisteredStr)))->format('Y-m-d');
            elseif (gettype($dateRegisteredStr) == 'string'):
                $dateRegistered = (new \Carbon\Carbon($dateRegisteredStr))->format('Y-m-d');
            endif;

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
        $civilStatus = 'SINGLE';

        if ( strpos($civilStatusStr, 'single') ):
            $civilStatus = 'SINGLE';
            
        elseif ( strpos($civilStatusStr, 'marr') ):
            $civilStatus = 'MARRIED';
            
        elseif ( strpos($civilStatusStr, 'divor') ):
            $civilStatus = 'DIVORCED';
            
        elseif ( strpos($civilStatusStr, 'separ') ):
            $civilStatus = 'SEPARATED';
            
        elseif ( strpos($civilStatusStr, 'wido') ):
            $civilStatus = 'WIDOWED';

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

    private function getDateOfBirth($dateOfBirthStr, $row)
    {
        $dateOfBirth = '1970-01-01';

        if ($dateOfBirthStr):

            try {
                if (gettype($dateOfBirthStr) == 'integer'):
                    $dateOfBirth = (\Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateOfBirthStr)))->format('Y-m-d');
                elseif (gettype($dateOfBirthStr) == 'string'):
                    $dateOfBirth = (new \Carbon\Carbon($dateOfBirthStr))->format('Y-m-d');
                endif;

                return $dateOfBirth;
            }
            catch (\Carbon\Exceptions\InvalidFormatException $e)
            {
                $log = new Logger('mayap_error_import');
                $log->pushHandler(new StreamHandler(storage_path('logs/mayap_error_import.log')), Logger::INFO);
                $log->info('mayap_error_import', [
                    'first_name' => $row['first_name'],
                    'middle_name' => $row['middle_name'],
                    'last_name' => $row['last_name'],
                    'assistance' => $row['type_of_assistance'],
                    'date_of_birth' => $dateOfBirthStr,
                ]);

                return false;
            }
        endif;

        
    }

    private function getClassification($classificationStr, $company)
    {
        // $checking = $company->classifications()->where('name', 'LIKE', '%'.$classificationStr.'%')->first();

        // if (!$checking):
        //     $checking = $company->classifications()->save(
        //         $this->classificationRepository->new([
        //             'name' => $classificationStr,
        //             'description' => null,
        //         ], $company)
        //     );
        // endif;

        return $classificationStr;
    }

    private function getDateOfAssistance($dateRegisteredStr)
    {
        $dateRegistered = now()->format('Y-m-d');

        if ($dateRegisteredStr):
        
            $dateRegistered = (\Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateRegisteredStr)))->format('Y-m-d');

        endif;

        return $dateRegistered;
    }
}
