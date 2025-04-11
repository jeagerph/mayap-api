<?php

namespace App\Imports;

use Monolog\Logger;
use App\Models\City;
use App\Models\Barangay;

use Illuminate\Support\Collection;
use Monolog\Handler\StreamHandler;
use Illuminate\Support\Facades\Cache;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Http\Repositories\Base\BeneficiaryRepository;
use App\Http\Repositories\Base\BeneficiaryAssistanceRepository;
use App\Http\Repositories\Base\CompanyClassificationRepository;

class BeneficiariesImport implements ToCollection, WithHeadingRow
{
    private $beneficiaryRepository;
    private $classificationRepository;
    private $assistanceRepository;

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

        $firstNames = [];
        $middleNames = [];
        $lastNames = [];

        $newBeneficiaries = [];

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

            $contact_no = self::getContactNo($row['contact_no']);

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

            $latitude = $row['latitude']
                ? $row['latitude']
                : 0;

            $longitude = $row['longitude']
                ? $row['longitude']
                : 0;

            $head_of_household = $row['head_of_household']
                ? ($row['head_of_household'] == 'YES' ? 1 : 0)
                : 0;

            $typeOfAssistance = $row['type_of_assistance']
                ? $row['type_of_assistance']
                : null;

            $assistanceDate = $row['date_assisted']
                ? $row['date_assisted']
                : null;

            $assistedDate = $row['date_assisted']
                ? $row['date_assisted']
                : null;    

            $assistedBy = $row['assisted_by']
                ? $row['assisted_by']
                : null;
            
            $assistanceRemarks = $row['assistance_remarks']
                ? $row['assistance_remarks']
                : null;
            
            $remarks = $row['assistance_remarks']
                ? $row['assistance_remarks']
                : null;
            
            if ($date_of_birth):
                if ($last_name && $first_name && $date_of_birth):

                    $firstNames[] = $first_name;
                    $lastNames[] = $last_name;
                    $middleNames[] = $middle_name;

                    $newBeneficiaries[] = [
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
                    
                        'is_household' => $head_of_household,
                        'household_count' => 0,
                        'household_voters_count' => 0,
                        'household_families_count' => 0,
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
        
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                    ];
                    
    
                endif;
            endif;

        endforeach;
   
        $exists = Cache::remember(
            'beneficiaries_check_' . md5(json_encode($firstNames)),
            600,
            fn () => $company->beneficiaries()
                ->whereIn('first_name', $firstNames)
                ->whereIn('middle_name', $middleNames)
                ->whereIn('last_name', $lastNames)
                ->exists()
        );

        if(!$exists):
            $newBeneficiary = $this->beneficiaryRepository->bulkInsert($newBeneficiaries, $company);
    
            $this->beneficiaryRepository->bulkUpdateAddress($newBeneficiary);

            if (
                $typeOfAssistance &&
                $typeOfAssistance != 'NA' &&
                $typeOfAssistance != 'N/A' &&
                $typeOfAssistance != 'N A'
            ):

                $isAssisted = 0;

                if ($assistanceDate):
                    $isAssisted = 1;
                    $assistanceDate = self::getAssistedDate($assistanceDate);
                endif;
                $assistances = [];
                foreach ($newBeneficiary as $beneficiary) {
                  
                      $assistance = $this->assistanceRepository->new([
                            'assistance_date' =>  $assistanceDate  ?: $date_registered,
                            'assistance_type' => $typeOfAssistance,
                            'remarks' => $assistanceRemarks,
                            'assisted_date' =>  \Carbon\Carbon::parse($assistedDate)->format('Y-m-d'),
                            'is_assisted' => $isAssisted,
                            'assisted_by' => $assistedBy,
                        ], $company);

                        $assistances[] = $assistance->attributesToArray();
                   
                }
                if (!empty($assistances)) {
                    $beneficiary->assistances()->insert($assistances);
                }
             
            endif;

        else:
            $updatedBeneficiaryIds = collect();
            if ($exists) {
                $updatedBeneficiaries = collect();
                $company->beneficiaries()
                ->whereIn('first_name', $firstNames)
                ->whereIn('middle_name', $middleNames)
                ->whereIn('last_name', $lastNames)->chunk(100)->each(function ($batch) use ( $updatedBeneficiaryIds, $company,$province_id, $city_id, $barangay_id, $house_no, $gender, $contact_no, $place_of_birth, $date_of_birth, $civil_status, $religion, $educational_attainment, $occupation, $monthly_income, $head_of_household, $latitude, $longitude) {
                    $beneficiaryIds = $batch->pluck('id')->toArray();
                   $company->beneficiaries()->whereIn('id', $beneficiaryIds)
                        ->update([
                            'province_id' => $province_id,
                            'city_id' => $city_id,
                            'barangay_id' => $barangay_id,
                            'house_no' => $house_no,
                            'gender' => $gender,
                            'mobile_no' => $contact_no,
                            'email' => null,
                            'place_of_birth' => $place_of_birth,
                            'date_of_birth' => $date_of_birth,
                            'civil_status' => $civil_status,
                            'religion' => $religion,
                            'educational_attainment' => $educational_attainment,
                            'occupation' => $occupation,
                            'monthly_income' => $monthly_income,
                            'is_household' => $head_of_household,
                            'latitude' => $latitude,
                            'longitude' => $longitude,
                            'updated_at' => now(),
                        ]);

                        $updatedBeneficiaryIds = $updatedBeneficiaryIds->merge($beneficiaryIds);
                });
              
                if ($updatedBeneficiaryIds->isNotEmpty()) {
                    $updatedBeneficiaries = $company->beneficiaries()->whereIn('id', $updatedBeneficiaryIds)->get();
                    $this->beneficiaryRepository->bulkUpdateAddress($updatedBeneficiaries);
                }
               
            }
           

        endif;

        
        
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

        if (!$genderStr) return $gender;

        if ($genderStr == 'FEMALE' || $genderStr == 'F') $gender = 2;

        return $gender;
    }

    private function getCivilStatus($civilStatusStr)
    {
        $civilStatus = 'SINGLE';

        if (!$civilStatusStr) return $civilStatus;

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
        $barangay = Barangay::where('name', $barangayStr)
            ->where('prov_code', $provinceId)
            ->first();

        return $barangay->id ?? 8280;
    }

    private function getCity($cityStr, $provinceId)
    {
        $city = City::where('name', $cityStr)
            ->where('prov_code', $provinceId)
            ->first();

        return $city->city_code ?? '036918';
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

        return $dateOfBirth;
    }

    private function getClassification($classificationStr, $company)
    {


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

    private function getContactNo($contactNoStr)
    {
        if (!$contactNoStr) return '09';

        $contactNo = $contactNoStr;

        if (gettype($contactNoStr) == 'string'):
            if ($contactNoStr[0] == '9'):
                $contactNo = ('0'.$contactNo);
            endif;
        endif;

        return $contactNo;
    }

    private function getAssistedDate($assistedDateStr)
    {
        $assistanceDate = now()->format('Y-m-d');

        if ($assistanceDate):
        
            $assistanceDate = (new \Carbon\Carbon($assistedDateStr))->format('Y-m-d');

        endif;

        return $assistanceDate;
    }
}
