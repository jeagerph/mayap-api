<?php

namespace App\Imports;

use App\Models\Barangay;
use App\Models\City;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


use App\Http\Repositories\Base\CompanyClassificationRepository;
use App\Http\Repositories\Base\BeneficiaryAssistanceRepository;
use App\Http\Repositories\Base\VoterRepository;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Illuminate\Support\Facades\Cache;

class VotersImport implements ToCollection, WithHeadingRow
{
    private $votersRepository;
    private $classificationRepository;
    private $assistanceRepository;

    public function __construct()
    {
        $this->votersRepository = new VoterRepository;
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

        $newBeneficiaries = [];

        foreach ($collection as $row) {
            $full_name = $row['full_name'] ?? null;

            if (!$full_name) {
                continue;
            }


            [$last_name, $first_name, $middle_name] = $this->splitFullName($full_name);

            // $date_of_birth = self::getDateOfBirth($row['date_of_birth'], $row);
            $date_registered = self::getDateRegistered($row['date_registered']);
            $gender = 0;
            $house_no = $row['house_no'] ?? null;
            $province_id = '0369';
            $city_id = self::getCity($row['city'], $province_id);
            $barangay_id = self::getBarangay($row['barangay'], $province_id);
            $precint_no = $row['precint_no'] ?? null;
            $application_no = $row['application_no'] ?? null;
            $application_date = $row['application_date'] ?? null;
            $application_type = $row['application_type'] ?? null;
            $remarks = $row['remarks'] ?? null;

            // if ($date_of_birth) { 
            $newBeneficiaries[] = [
                'date_registered' => $date_registered,
                'province_id' => $province_id,
                'city_id' => $city_id,
                'barangay_id' => $barangay_id,
                'house_no' => $house_no,
                'first_name' => $first_name,
                'middle_name' => $middle_name,
                'last_name' => $last_name,
                'gender' => $gender ?? 0,
                'precint_no' => $precint_no,
                'email' => null,
                'application_no' => $application_no,
                'application_date' => $application_date,
                'application_type' => $application_type,
                'date_of_birth' => null,
                'remarks' => $remarks,
            ];
            // }
        }

        $this->votersRepository->bulkInsert($newBeneficiaries, $company);
    }


    private function splitFullName($full_name)
    {
        $parts = explode(', ', $full_name);
        $last_name = $parts[0] ?? null;

        $name_parts = explode(' ', $parts[1] ?? '');
        $first_name = $name_parts[0] ?? null;
        $middle_name = count($name_parts) > 1 ? implode(' ', array_slice($name_parts, 1)) : null;

        return [$last_name, $first_name, $middle_name];
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

        if (!$genderStr)
            return $gender;

        if ($genderStr == 'FEMALE' || $genderStr == 'F')
            $gender = 2;

        return $gender;
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
            } catch (\Carbon\Exceptions\InvalidFormatException $e) {
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
        if (!$contactNoStr)
            return '09';

        $contactNo = $contactNoStr;

        if (gettype($contactNoStr) == 'string'):
            if ($contactNoStr[0] == '9'):
                $contactNo = ('0' . $contactNo);
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
