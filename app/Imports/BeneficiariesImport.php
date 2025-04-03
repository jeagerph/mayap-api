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
use Illuminate\Support\Facades\Cache;

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
        $barangayOptions = [
            'BAGUINDOC (BAGUINLOC)' => 7770,
            'BANTOG' => 7771,
            'CAMPOS' => 7772,
            'CARMEN' => 7773,
            'CASILI' => 7774,
            'DON RAMON' => 7775,
            'HERNANDO' => 7776,
            'POBLACION' => 7777,
            'RIZAL' => 7778,
            'SAN FRANCISCO EAST' => 7779,
            'SAN FRANCISCO WEST' => 7780,
            'SAN JOSE NORTH' => 7781,
            'SAN JOSE SOUTH' => 7782,
            'SAN JUAN' => 7783,
            'SAN ROQUE' => 7784,
            'SANTO DOMINGO' => 7785,
            'SINENSE' => 7786,
            'SUAVERDEZ' => 7787,
            'ANUPUL' => 7788,
            'BANABA' => 7789,
            'BANGCU' => 7790,
            'CULUBASA' => 7791,
            'DELA CRUZ' => 7792,
            'LA PAZ' => 7793,
            'LOURDES' => 7794,
            'MALONZO' => 7795,
            'VIRGEN DE LOS REMEDIOS (PACALCAL)' => 7796,
            'SAN NICOLAS (POB.)' => 7797,
            'SAN PEDRO' => 7798,
            'SAN RAFAEL' => 7799,
            'SAN ROQUE' => 7800,
            'SAN VICENTE' => 7801,
            'SANTO NIñO' => 7802,
           
            'ANOLING 1ST' => 7803,
            'ANOLING 2ND' => 7804,
            'ANOLING 3RD' => 7805,
            'BACABAC' => 7806,
            'BACSAY' => 7807,
            'BANCAY 1ST' => 7808,
            'BANCAY 2ND' => 7809,
            'BILAD' => 7810,
            'BIRBIRA' => 7811,
            'BOBON CAAROSIPAN' => 7812,
            'BOBON 1ST' => 7813,

            'BOBON 2ND' => 7814,
            'CABANABAAN' => 7815,
            'CACAMILINGAN NORTE' => 7816,
            'CACAMILINGAN SUR' => 7817,
            'CANIAG' => 7818,
            'CARAEL' => 7819,
            'CAYAOAN' => 7820,
            'CAYASAN' => 7821,
            'FLORIDA' => 7822,
            'LASONG' => 7823,
            'LIBUEG' => 7824,
            'MALACAMPA' => 7825,
            'MANAKEM' => 7826,
            'MANUPEG' => 7827,
            'MARAWI' => 7828,
            'MATUBOG' => 7829,
            'NAGRAMBACAN' => 7830,
            'NAGSERIALAN' => 7831,
            'PALIMBO PROPER' => 7832,
            'PALIMBO-CAAROSIPAN' => 7833,
            'PAO 1ST' => 7834,
            'PAO 2ND' => 7835,
            'PAO 3RD' => 7836,
            'PAPAAC' => 7837,
            'PINDANGAN 1ST' => 7838,
            'PINDANGAN 2ND' => 7839,
            'POBLACION A' => 7840,
            'POBLACION B' => 7841,
            'POBLACION C' => 7842,
            'POBLACION D' => 7843,
            'POBLACION E' => 7844,
            'POBLACION F' => 7845,
            'POBLACION G' => 7846,
            'POBLACION H' => 7847,
            'POBLACION I' => 7848,
            'POBLACION J' => 7849,
            'SANTA MARIA' => 7850,
            'SAWAT' => 7851,
            'SINILIAN 1ST' => 7852,
            'SINILIAN 2ND' => 7853,
            'SINILIAN 3RD' => 7854,
            'SINILIAN CACALIBOSOAN' => 7855,
            'SINULATAN 1ST' => 7856,
            'SINULATAN 2ND' => 7857,
            'SURGUI 1ST' => 7858,
            'SURGUI 2ND' => 7859,
            'SURGUI 3RD' => 7860,
            'TAMBUGAN' => 7861,
            'TELBANG' => 7862,
            'TUEC' => 7863,

            'ARANGUREN' => 7864,
            'BUENO' => 7865,
           
            'CUBCUB (POB.)' => 7866,
            'CUTCUT 1ST' => 7867,
            'CUTCUT 2ND' => 7868,
            'DOLORES' => 7869,
            'ESTRADA (CALINGCUAN)' => 7870,
            'LAWY' => 7871,
            'MANGA' => 7872,
            'MANLAPIG' => 7873,
            'O DONNELL' => 7874,
            'SANTA LUCIA' => 7875,
            'SANTA RITA' => 7876,
            'SANTO DOMINGO 1ST' => 7877,
            'SANTO DOMINGO 2ND' => 7878,
            'SANTO ROSARIO' => 7879,
            'TALAGA' => 7880,
            'MARUGLU' => 7881,
            'SANTA JULIANA' => 7882,
            'CRISTO REY' => 7883,

            'ALFONSO' => 7884,
            'BALUTU' => 7885,
            'CAFE' => 7886,
            'CALIUS GUECO' => 7887,
            'CALULUAN' => 7888,
            'CASTILLO' => 7889,
            'CORAZON DE JESUS' => 7890,
            'CULATINGAN' => 7891,
            'DUNGAN' => 7892,
            'DUTUNG-A-MATAS' => 7893,
            'LILIBANGAN' => 7894,
            'MABILOG' => 7895,
            'MAGAO' => 7896,
            'MALUPA' => 7897,
            'MINANE' => 7898,
            'PANALICSIAN (PANALICSICAN)' => 7899,
            'PANDO' => 7900,
            'PARANG' => 7901,
            'PARULUNG' => 7902,
            'PITABUNAN' => 7903,
            'SAN AGUSTIN (MURCIA)' => 7904,
            'SAN ANTONIO' => 7905,
            'SAN BARTOLOME' => 7906,
            'SAN FRANCISCO' => 7907,
            'SAN ISIDRO (ALMENDRAS)' => 7908,
            'SAN JOSE (POB.)' => 7909,
            'SAN JUAN (CASTRO)' => 7910,
            'SAN MARTIN' => 7911,
            'SAN NICOLAS (POB.)' => 7912,
            'SAN NICOLAS BALAS' => 7913,
            'SANTO NIñO' => 7914,
            'SANTA CRUZ' => 7915,
            'SANTA MARIA' => 7916,
            'SANTA MONICA' => 7917,
            'SANTA RITA' => 7918,
            'SANTA ROSA' => 7919,
            'SANTIAGO' => 7920,
            'SANTO CRISTO' => 7921,
            'SANTO ROSARIO (MAGUNTING)' => 7922,
           'TALIMUNDUC MARIMLA' => 7923,
            'TALIMUNDUC SAN MIGUEL' => 7924,
            'TELABANCA' => 7925,
            'TINANG' => 7926,
            'SAN VICENTE (CALUIS/COBRA)' => 7927,
            'GREEN VILLAGE' => 7928,
            'ABAGON' => 7929,
            'AMACALAN' => 7930,
            'APSAYAN' => 7931,
            'AYSON' => 7932,
            'BAWA' => 7933,
            'BUENLAG' => 7934,
            'BULARIT' => 7935,
            'CALAYAAN' => 7936,
            'CARBONEL' => 7937,
            'CARDONA' => 7938,
            'CATURAY' => 7939,
            'DANZO' => 7940,

            'DICOLOR' => 7941,
    'DON BASILIO' => 7942,
    'LUNA' => 7943,
    'MABINI' => 7944,
    'MAGASPAC' => 7945,
    'MALAYEP' => 7946,
    'MATAPITAP' => 7947,
    'MATAYUMCAB' => 7948,
    'NEW SALEM' => 7949,
    'OLOYBUAYA' => 7950,
    'PADAPADA' => 7951,
    'PARSOLINGAN' => 7952,
    'PINASLING (PINASUNG)' => 7953,
    'PLASTADO' => 7954,
    'POBLACION 1' => 7955,
    'POBLACION 2' => 7956,
    'POBLACION 3' => 7957,
    'QUEZON' => 7958,
    'RIZAL' => 7959,
    'SALAPUNGAN' => 7960,
    'SAN AGUSTIN' => 7961,
    'SAN ANTONIO' => 7962,
    'SAN BARTOLOME' => 7963,
    'SAN JOSE' => 7964,
    'SANTA LUCIA' => 7965,
    'SANTIAGO' => 7966,
    'SEMBRANO' => 7967,
    'SINGAT' => 7968,
    'SULIPA' => 7969,
    'TAGUMBAO' => 7970,
    'TANGCARAN' => 7971,
    'VILLA PAZ' => 7972,
    'BALANOY' => 7973,
    'BANTOG-CARICUTAN' => 7974,
    'CARAMUTAN' => 7975,
    'CAUT' => 7976,
    'COMILLAS' => 7977,
    'DUMARAIS' => 7978,
    'GUEVARRA' => 7979,
    'KAPANIKIAN' => 7980,
    'LA PURISIMA' => 7981,
    'LARA' => 7982,
    'LAUNGCUPANG' => 7983,
    'LOMBOY' => 7984,
    'MACALONG' => 7985,
    'MATAYUMTAYUM' => 7986,
    'MAYANG' => 7987,
    'MOTRICO' => 7988,
    'PALUDPUD' => 7989,
    'RIZAL' => 7990,
    'SAN ISIDRO (POB.)' => 7991,
    'SAN ROQUE (POB.)' => 7992,
    'SIERRA' => 7993,
    'AMBALINGIT' => 7994,
    'BAYBAYAOAS' => 7995,
    'BIGBIGA' => 7996,
    'BINBINACA' => 7997,
    'CALABTANGAN' => 7998,
    'CAOCAOAYAN' => 7999,
    'CARABAOAN' => 8000,
    'CUBCUB' => 8001,
    'GAYONGGAYONG' => 8002,
    'GOSSOOD' => 8003,
    'LABNEY' => 8004,
    'MAMONIT' => 8005,
    'MANINIOG' => 8006,
    'MAPANDAN' => 8007,
    'NAMBALAN' => 8008,
    'PEDRO L. QUINES' => 8009,
    'PITOMBAYOG' => 8010,
    'POBLACION NORTE' => 8011,
    'POBLACION SUR' => 8012,
    'ROTROTTOOC' => 8013,
    'SAN BARTOLOME' => 8014,
    'SAN JOSE' => 8015,
    'TALDIAPAN' => 8016,
    'TANGCARANG' => 8017,
    'ABLANG-SAPANG' => 8018,
    'ARINGIN' => 8019,
    'ATENCIO' => 8020,
    'BANAOANG EAST' => 8021,
    'BANAOANG WEST' => 8022,
    'BAQUERO NORTE' => 8023,
    'BAQUERO SUR' => 8024,
    'BURGOS' => 8025,
    'CALAMAY' => 8026,
    'CALAPAN' => 8027,
    'CAMANGAAN EAST' => 8028,
    'CAMANGAAN WEST' => 8029,
    'CAMPOSANTO 1 - NORTE' => 8030,
    'CAMPOSANTO 1 - SUR' => 8031,
    'CAMPOSANTO 2' => 8032,
    'CAPAOAYAN' => 8033,
    'LAPSING' => 8034,
    'MABINI' => 8035,
    'MALUAC' => 8036,
    'POBLACION 1' => 8037,
    'POBLACION 2' => 8038,
    'POBLACION 3' => 8039,
    'POBLACION 4' => 8040,
    'RIZAL' => 8041,
    'SAN JUAN' => 8042,
    'SAN JULIAN' => 8043,
    'SAN LEON' => 8044,
    'SAN PEDRO' => 8045,
    'SAN ROQUE' => 8046,
    'SANTA LUCIA EAST' => 8047,
    'SANTA LUCIA WEST' => 8048,
    'SANTA MARIA' => 8049,
    'SANTA MONICA' => 8050,
    'TUBECTUBANG' => 8051,
    'TOLEGA NORTE' => 8052,
    'TOLEGA SUR' => 8053,
    'VILLA' => 8054,
    'ABOGADO' => 8055,
'ACOCOLAO' => 8056,
'ADUAS' => 8057,
'APULID' => 8058,
'BALAOANG' => 8059,
'BARANG (BORANG)' => 8060,
'BRILLANTE' => 8061,
'BURGOS' => 8062,
'CABAYAOASAN' => 8063,
'CANAN' => 8064,
'CARINO' => 8065,
'CAYANGA' => 8066,
'COLIBANGBANG' => 8067,
'CORAL' => 8068,
'DAPDAP' => 8069,
'ESTACION' => 8070,
'MABILANG' => 8071,
'MANAOIS' => 8072,
'MATALAPITAP' => 8073,
'NAGMISAAN' => 8074,
'NANCAMARINAN' => 8075,
'NIPACO' => 8076,
'PATALAN' => 8077,
'POBLACION NORTE' => 8078,
'POBLACION SUR' => 8079,
'RANG-AYAN' => 8080,
'SALUMAGUE' => 8081,
'SAMPUT' => 8082,
'SAN CARLOS' => 8083,
'SAN ISIDRO' => 8084,
'SAN JUAN DE MILLA' => 8085,
'SANTA INES' => 8086,
'SINIGPIT' => 8087,
'TABLANG' => 8088,
'VENTENILLA' => 8089,
'BALITE' => 8090,
'BUENAVISTA' => 8091,
'CADANGLAAN' => 8092,
'ESTIPONA' => 8093,
'LINAO' => 8094,
'MAASIN' => 8095,
'MATINDEG' => 8096,
'MAUNGIB' => 8097,
'NAYA' => 8098,
'NILASIN 1ST' => 8099,
'NILASIN 2ND' => 8100,
'POBLACION 1' => 8101,
'POBLACION 2' => 8102,
'POBLACION 3' => 8103,
'POROC' => 8104,
'SINGAT' => 8105,
'CORAL-ILOCO' => 8106,
'GUITEB' => 8107,
'PANCE' => 8108,
'POBLACION CENTER' => 8109,
'POBLACION NORTH' => 8110,
'POBLACION SOUTH' => 8111,
'SAN JUAN' => 8112,
'SAN RAYMUNDO' => 8113,
'TOLEDO' => 8114,
'BALLOC' => 8115,
'BAMBAN' => 8116,
'CASIPO' => 8117,
'CATAGUDINGAN' => 8118,
'DALDALAYAP' => 8119,
'DOCLONG 1' => 8120,
'DOCLONG 2' => 8121,
'MAASIN' => 8122,
'NAGSABARAN' => 8123,
'PIT-AO' => 8124,
'POBLACION NORTE' => 8125,
'POBLACION SUR' => 8126,
'COLUBOT' => 8127,
'LANAT' => 8128,
'LEGASPI' => 8129,
'MANGANDINGAY' => 8130,
'MATARANNOC' => 8131,
'PACPACO' => 8132,
'POBLACION' => 8133,
'SALCEDO' => 8134,
'SAN AGUSTIN' => 8135,
'SAN FELIPE' => 8136,
'SAN JACINTO' => 8137,
'SAN MIGUEL' => 8138,
'SAN NARCISO' => 8139,
'SAN VICENTE' => 8140,
'SANTA MARIA' => 8141,
'BALDIOS' => 8142,
'BOTBOTONES' => 8143,
'CAANAMONGAN' => 8144,
'CABARUAN' => 8145,
'CABUGBUGAN' => 8146,
'CADULDULAOAN' => 8147,
'CALIPAYAN' => 8148,
'MACAGUING' => 8149,
'NAMBALAN' => 8150,
'PADAPADA' => 8151,
'PILPILA' => 8152,
'PINPINAS' => 8153,
'POBLACION EAST' => 8154,
'POBLACION WEST' => 8155,
'PUGO-CECILIO' => 8156,
'SAN FRANCISCO' => 8157,
'SAN SOTERO' => 8158,
'SAN VICENTE' => 8159,
'SANTA INES CENTRO' => 8160,
'SANTA INES EAST' => 8161,
'SANTA INES WEST' => 8162,
'TAGUIPORO' => 8163,
'TIMMAGUAB' => 8164,
'VARGAS' => 8165,
'AGUSO' => 8166,
'ALVINDIA SEGUNDO' => 8167,
'AMUCAO' => 8168,
'ARMENIA' => 8169,
'ASTURIAS' => 8170,
'ATIOC' => 8171,
'BALANTI' => 8172,
'BALETE' => 8173,
'BALIBAGO I' => 8174,
'BALIBAGO II' => 8175,
'BALINGCANAWAY' => 8176,
'BANABA' => 8177,
'BANTOG' => 8178,
'BARAS-BARAS' => 8179,
'BATANG-BATANG' => 8180,
'BINAUGANAN' => 8181,
'BORA' => 8182,
'BUENAVISTA' => 8183,
'BUHILIT (BUBULIT)' => 8184,
'BUROT' => 8185,
'CALINGCUAN' => 8186,
'CAPEHAN' => 8187,
'CARANGIAN' => 8188,
'CENTRAL' => 8189,
'CULIPAT' => 8190,
'CUT-CUT I' => 8191,
'CUT-CUT II' => 8192,
'DALAYAP' => 8193,
'DELA PAZ' => 8194,
'DOLORES' => 8195,
'LAOANG' => 8196,
'LIGTASAN' => 8197,
'LOURDES' => 8198,
'MABINI' => 8199,
'MALIGAYA' => 8200,
'MALIWALO' => 8201,
'MAPALACSIAO' => 8202,
'MAPALAD' => 8203,
'MATATALAIB' => 8204,
'PARAISO' => 8205,
'POBLACION' => 8206,
'SAN CARLOS' => 8207,
'SAN FRANCISCO' => 8208,
'SAN ISIDRO' => 8209,
'SAN JOSE' => 8210,
'SAN JOSE DE URQUICO' => 8211,
'SAN JUAN DE MATA' => 8212,
'SAN LUIS' => 8213,
'SAN MANUEL' => 8214,
'SAN MIGUEL' => 8215,
'SAN NICOLAS' => 8216,
'SAN PABLO' => 8217,
'SAN PASCUAL' => 8218,
'SAN RAFAEL' => 8219,
'SAN ROQUE' => 8220,
'SAN SEBASTIAN' => 8221,
'SAN VICENTE' => 8222,
'SANTA CRUZ (ALVINDIA PRIMERO)' => 8223,
'SANTA MARIA' => 8224,
'SANTO CRISTO' => 8225,
'SANTO DOMINGO' => 8226,
'SANTO NIÑO' => 8227,
'SAPANG MARAGUL' => 8228,
'SAPANG TAGALOG' => 8229,
'SEPUNG CALZADA' => 8230,
'SINAIT' => 8231,
'SUIZO' => 8232,
'TARIJI' => 8233,
'TIBAG' => 8234,
'TIBAGAN' => 8235,
'TRINIDAD (TRINIDAD PRIMERO)' => 8236,
'UNGOT' => 8237,
'MATADERO' => 8238,
'SALAPUNGAN' => 8239,
'VILLA BACOLOR' => 8240,
'CARE' => 8241,
'BACULONG' => 8242,
'BALAYANG' => 8243,
'BALBALOTO' => 8244,
'BANGAR' => 8245,
'BANTOG' => 8246,
'BATANGBATANG' => 8247,
'BULO' => 8248,
'CABULUAN' => 8249,
'CALIBUNGAN' => 8250,
'CANAREM' => 8251,
'CRUZ' => 8252,
'LALAPAC' => 8253,
'MALUID' => 8254,
'MANGOLAGO' => 8255,
'MASALASA' => 8256,
'PALACPALAC' => 8257,
'SAN AGUSTIN' => 8258,
'SAN ANDRES' => 8259,
'SAN FERNANDO (POB.)' => 8260,
'SAN FRANCISCO' => 8261,
'SAN GAVINO (POB.)' => 8262,
'SAN JACINTO' => 8263,
'SAN NICOLAS (POB.)' => 8264,
'SAN VICENTE' => 8265,
'SANTA BARBARA' => 8266,
'SANTA LUCIA (POB.)' => 8267,
'BURGOS' => 8268,
'DAVID' => 8269,
'IBA' => 8270,
'LABNEY' => 8271,
'LAWACAMULAG' => 8272,
'LUBIGAN' => 8273,
'MAAMOT' => 8274,
'MABABANABA' => 8275,
'MORIONES' => 8276,
'PAO' => 8277,
'SAN JUAN DE VALDEZ' => 8278,
'SULA' => 8279,
'VILLA AGLIPAY' => 8280,
        ];


        
        $barangayId = 8280;

        if (!$barangayStr) return $barangayId;

        // $barangayModel = \App\Models\Barangay::where('name', 'LIKE', '%'.$barangayStr.'%')
        //         ->where('prov_code', $provinceId)
        //         ->first();

        // if ($barangayModel):
        //     $barangayId = $barangayModel->id;
        // endif;

        $arrKeys = array_keys($barangayOptions);

        foreach ($arrKeys as $key => $value):

            if (str_contains(strtolower($value), strtolower($barangayStr))):

                $barangayId = $barangayOptions[$value];

                break;
            endif;

        endforeach;

        return $barangayId;
    }

    private function getCity($cityStr, $provinceId)
    {
        $cityOptions = [
            'ANAO' => '036901',
            'BAMBAN' => '036902',
            'CAMILING' => '036903',
            'CAPAS' => '036904',
            'CONCEPCION' => '036905',
            'GERONA' => '036906',
            'LA PAZ' => '036907',
            'MAYANTOC' => '036908',
            'MONCADA' => '036909',
            'PANIQUI' => '036910',
            'PURA' => '036911',
            'RAMOS' => '036912',
            'SAN CLEMENTE' => '036913',
            'SAN MANUEL' => '036914',
            'SANTA IGNACIA' => '036915',
            'TARLAC CITY' => '036916',
            'VICTORIA' => '036917',
            'SAN JOSE' => '036918',
           
        ];

        $cityId = '036916';

        if (!$cityStr) return $cityId;

        // $cityModel = \App\Models\City::where('name', 'LIKE', '%'.$cityStr.'%')
        //         ->where('prov_code', $provinceId)
        //         ->first();

        // if ($cityModel):
        //     $cityId = $cityModel->city_code;
        // endif;

        $arrKeys = array_keys($cityOptions);

        foreach ($arrKeys as $key => $value):

            if (str_contains(strtolower($value), strtolower($cityStr))):

                $cityId = $cityOptions[$value];

                break;
            endif;

        endforeach;
        
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
