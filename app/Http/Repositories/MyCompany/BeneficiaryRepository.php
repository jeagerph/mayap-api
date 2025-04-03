<?php

namespace App\Http\Repositories\MyCompany;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Slug;
use App\Models\Company;
use App\Models\Barangay;
use App\Models\Beneficiary;
use App\Models\BeneficiaryRelative;
use App\Models\BeneficiaryAssistance;
use App\Models\BeneficiaryPatient;
use App\Models\BeneficiaryIdentification;
use App\Models\BeneficiaryDocument;
use App\Models\BeneficiaryCall;
use App\Models\BeneficiaryFamily;

use App\Http\Repositories\Base\BeneficiaryRepository as BaseRepository;
use App\Http\Repositories\Base\SlugRepository;
use App\Http\Repositories\Base\PDFRepository;
use App\Http\Repositories\Base\CompanyRepository;
use App\Http\Repositories\Base\CompanyClassificationRepository;
use App\Http\Repositories\Base\CompanyCallTransactionRepository;
use App\Http\Repositories\Base\BeneficiaryRelativeRepository;
use App\Http\Repositories\Base\BeneficiaryNetworkRepository;
use App\Http\Repositories\Base\BeneficiaryIncentiveRepository;
use App\Http\Repositories\Base\BeneficiaryAssistanceRepository;
use App\Http\Repositories\Base\BeneficiaryPatientRepository;
use App\Http\Repositories\Base\BeneficiaryMessageRepository;
use App\Http\Repositories\Base\BeneficiaryCallRepository;
use App\Http\Repositories\Base\BeneficiaryIdentificationRepository;
use App\Http\Repositories\Base\BeneficiaryDocumentRepository;
use App\Http\Repositories\Base\BeneficiaryFamilyRepository;

use Illuminate\Support\Facades\DB;
use App\Http\Repositories\MyCompany\SmsRepository;
use App\Models\Voter;

class BeneficiaryRepository
{
    private $companyRepository;

    public function __construct()
    {
        $this->baseRepository = new BaseRepository;
        $this->pdfRepository = new PDFRepository;
        $this->slugRepository = new SlugRepository;
        $this->companyRepository = new CompanyRepository;
        $this->companyClassificationRepository = new CompanyClassificationRepository;
        $this->companyCallTransactionRepository = new CompanyCallTransactionRepository;
        $this->relativeRepository = new BeneficiaryRelativeRepository;
        $this->networkRepository = new BeneficiaryNetworkRepository;
        $this->incentiveRepository = new BeneficiaryIncentiveRepository;
        $this->assistanceRepository = new BeneficiaryAssistanceRepository;
        $this->patientRepository = new BeneficiaryPatientRepository;
        $this->messageRepository = new BeneficiaryMessageRepository;
        $this->callRepository = new BeneficiaryCallRepository;
        $this->identificationRepository = new BeneficiaryIdentificationRepository;
        $this->documentRepository = new BeneficiaryDocumentRepository;
        $this->smsRepository = new SmsRepository;
        $this->familyRepository = new BeneficiaryFamilyRepository;
    }

    public function downloadReport($request)
    {
        ini_set('max_execution_time', 300);

        $company = Auth::user()->company();

        $file = $company->report_file;

        $fileName = $this->pdfRepository->fileName(
            $request->get('from'),
            $request->get('to'),
            strtolower($company->name) . '-beneficaries-report',
            '.xlsx'
        );

        $namespace = '\App\Exports\Beneficiaries\\' . $file . '\\BeneficiaryReport';

        \Excel::store(
            new $namespace($request, $company),
            'storage/exports/' . $fileName,
            env('STORAGE_DISK', 'public')
        );

        return [
            'path' => env('CDN_URL') . '/storage/exports/' . $fileName
        ];
    }

    public function downloadPrintReport($request)
    {
        ini_set('max_execution_time', 300);

        $company = Auth::user()->company();

        $dates = [
            'from' => $request->get('from'),
            'to' => $request->get('to')
        ];

        $beneficiaries = $company->beneficiaries()
            ->select('beneficiaries.*')
            ->leftJoin('barangays', function ($join) {
                $join->on('beneficiaries.barangay_id', '=', 'barangays.id');
            })
            ->where(function ($q) use ($request) {
                if ($request->has('filter')):

                    if (isset($request->get('filter')['isHousehold'])):
                        $q->where('is_household', $request->get('filter')['isHousehold']);
                    endif;

                    if (isset($request->get('filter')['isPriority'])):
                        $q->where('is_priority', $request->get('filter')['isPriority']);
                    endif;

                    if (isset($request->get('filter')['isOfficer'])):
                        $q->where('is_officer', $request->get('filter')['isOfficer']);
                    endif;

                    if (isset($request->get('filter')['gender'])):
                        $q->where('gender', $request->get('filter')['gender']);
                    endif;

                    if (isset($request->get('filter')['provCode'])):
                        $q->where('province_id', $request->get('filter')['provCode']);
                    endif;

                    if (isset($request->get('filter')['cityCode'])):
                        $q->where('city_id', $request->get('filter')['cityCode']);
                    endif;

                    if (isset($request->get('filter')['barangay'])):
                        $q->where('barangay_id', $request->get('filter')['barangay']);
                    endif;

                    if (isset($request->get('filter')['purok'])):
                        $q->where('purok', 'LIKE', '%' . $request->get('filter')['purok'] . '%');
                    endif;

                    if (isset($request->get('filter')['street'])):
                        $q->where('street', 'LIKE', '%' . $request->get('filter')['street'] . '%');
                    endif;

                    if (isset($request->get('filter')['zone'])):
                        $q->where('zone', 'LIKE', '%' . $request->get('filter')['zone'] . '%');
                    endif;

                    if (isset($request->get('filter')['age'])):
                        $arrAgeRange = explode(',', $request->get('filter')['age']);

                        $minDate = \Carbon\Carbon::today()->subYears($arrAgeRange[0])->format('Y');
                        $maxDate = \Carbon\Carbon::today()->subYears($arrAgeRange[1])->format('Y');

                        $q->whereBetween(\DB::raw('YEAR(date_of_birth)'), [$maxDate, $minDate]);

                    endif;

                    if (isset($request->get('filter')['hasNetwork'])):

                        $q->has('parentingNetworks', '>=', 1);

                    endif;
                endif;
            })
            ->where(function ($q) use ($request) {
                $q->whereDate('date_registered', $request->get('from'))
                    ->orWhereDate('date_registered', $request->get('to'))
                    ->orWhereBetween('date_registered', [$request->get('from'), $request->get('to')]);
            })
            // ->orderBy('is_priority', 'desc')
            ->orderBy('barangays.name', 'asc')
            ->get();

        $file = $company->report_file;

        $fileName = $this->pdfRepository->fileName(
            $request->get('from'),
            $request->get('to'),
            strtolower($company->name) . '-beneficaries-report'
        );

        $pdf = \PDF::loadView(
            "beneficiaries.list.{$file}.index",
            [
                'company' => $company,
                'request' => $request,
                'beneficiaries' => $beneficiaries
            ]
        )
            ->setOption('margin-top', '1mm')
            ->setOption('margin-right', '0mm')
            ->setOption('margin-left', '0mm')
            ->setPaper('a4')
            ->setOrientation('landscape');

        return response(
            [
                'path' => $this->pdfRepository->export(
                    $pdf->output(),
                    $fileName,
                    'pdf/beneficiaries/report/list/'
                )
            ],
            200
        );
    }

    public function downloadByBarangayReport($request)
    {
        ini_set('max_execution_time', 300);

        $company = Auth::user()->company();

        $file = $company->report_file;

        $fileName = $this->pdfRepository->fileName(
            $request->get('from'),
            $request->get('to'),
            strtolower($company->name) . '-beneficaries-by-barangay-report',
            '.xlsx'
        );

        $namespace = '\App\Exports\Beneficiaries\\' . $file . '\\BeneficiaryByBarangayReport';

        \Excel::store(
            new $namespace($request, $company),
            'storage/exports/' . $fileName,
            env('STORAGE_DISK', 'public')
        );

        return [
            'path' => env('CDN_URL') . '/storage/exports/' . $fileName
        ];
    }

    public function downloadByPurokReport($request)
    {
        ini_set('max_execution_time', 300);

        $company = Auth::user()->company();

        $file = $company->report_file;

        $fileName = $this->pdfRepository->fileName(
            $request->get('from'),
            $request->get('to'),
            strtolower($company->name) . '-beneficaries-by-purok-report',
            '.xlsx'
        );

        $namespace = '\App\Exports\Beneficiaries\\' . $file . '\\BeneficiaryByPurokReport';

        \Excel::store(
            new $namespace($request, $company),
            'storage/exports/' . $fileName,
            env('STORAGE_DISK', 'public')
        );

        return [
            'path' => env('CDN_URL') . '/storage/exports/' . $fileName
        ];
    }

    public function downloadHouseholdByBarangayReport($request)
    {
        $company = Auth::user()->company();

        $file = $company->report_file;

        $fileName = $this->pdfRepository->fileName(
            $request->get('from'),
            $request->get('to'),
            strtolower($company->name) . '-beneficaries-household-by-barangay-report',
            '.xlsx'
        );

        $namespace = '\App\Exports\Beneficiaries\\' . $file . '\\HouseholdByBarangayReport';

        \Excel::store(
            new $namespace($request, $company),
            'storage/exports/' . $fileName,
            env('STORAGE_DISK', 'public')
        );

        return [
            'path' => env('CDN_URL') . '/storage/exports/' . $fileName
        ];
    }

    public function downloadHouseholdByPurokReport($request)
    {
        $company = Auth::user()->company();

        $file = $company->report_file;

        $fileName = $this->pdfRepository->fileName(
            $request->get('from'),
            $request->get('to'),
            strtolower($company->name) . '-beneficaries-household-by-purok-sitio-report',
            '.xlsx'
        );

        $namespace = '\App\Exports\Beneficiaries\\' . $file . '\\HouseholdByPurokReport';

        \Excel::store(
            new $namespace($request, $company),
            'storage/exports/' . $fileName,
            env('STORAGE_DISK', 'public')
        );

        return [
            'path' => env('CDN_URL') . '/storage/exports/' . $fileName
        ];
    }

    public function showBeneficiariesLocationsList($request)
    {
        $company = Auth::user()->company();

        $slugType = addslashes('App\Models\Beneficiary');

        $sql = "SELECT ";
        $sql .= "CONCAT(ben.last_name, ', ', ben.first_name, ' ', COALESCE(ben.middle_name, '')) AS full_name, ";
        $sql .= "ben.date_of_birth AS date_of_birth, ";
        $sql .= "CASE ";
        $sql .= "WHEN ben.gender = 1 THEN 'MALE' ";
        $sql .= "WHEN ben.gender = 2 THEN 'FEMALE' ";
        $sql .= "END AS gender_name, ";
        $sql .= "ben.latitude AS latitude, ";
        $sql .= "ben.longitude AS longitude, ";
        $sql .= "slug.code AS slug_code ";
        $sql .= "FROM beneficiaries ben ";
        $sql .= "LEFT JOIN slugs slug ON slug.slug_id = ben.id AND slug_type = '{$slugType}' ";
        $sql .= "WHERE ben.company_id = {$company->id} ";
        $sql .= "AND ben.latitude IS NOT NULL ";
        $sql .= "AND ben.longitude IS NOT NULL ";
        $sql .= "AND ben.deleted_at IS NULL ";

        $data = \DB::select($sql);

        return $data;
    }

    public function showBeneficiariesSummaryByBarangay($request)
    {
        $company = Auth::user()->company();

        $provinces = $company->barangay_report_provinces
            ? explode(',', $company->barangay_report_provinces)
            : [];

        return $this->companyRepository->barangaysSummaryReport($request, $company, $provinces[0]);


        // NOT IN USE
        // if (count($provinces)):

        //     $provinceCode = $provinces[0];

        //     $model = new Barangay;

        //     $barangays = Barangay::where('prov_code', $provinceCode)
        //                             ->where(function($q) use ($request, $provinceCode)
        //                             {
        //                                 // ROBES
        //                                 if ($provinceCode == '0314'):
        //                                     $q->where('city_code', '031420');
        //                                 endif;

        //                                 if($request->has('barangayName') && $request->get('barangayName')):
        //                                     $q->where('name', 'LIKE', '%'.$request->get('barangayName').'%');
        //                                 endif;
        //                             })
        //                             ->orderBy('name', 'asc')
        //                             ->paginate(10)
        //                             ->appends(request()->query());


        //     $itemsTransformed  = $barangays->getCollection()->transform(function ($value) use ($company)
        //     {
        //         $beneficiaries = $this->companyRepository->beneficiariesByBarangayReport($company, $value->id);

        //         $networks = $this->companyRepository->networksByBarangayReport($company, $value->id);

        //         $incentives = $this->companyRepository->incentivesByBarangayReport($company, $value->id);

        //         $patients = $this->companyRepository->patientsByBarangayReport($company, $value->id);

        //         $assistances = $this->companyRepository->assistancesByBarangayReport($company, $value->id);

        //         return [
        //             'barangay' => $value->toArrayBeneficiariesRelated(),
        //             'beneficiaries' => $beneficiaries['total'],
        //             'officers' => $beneficiaries['officers'],
        //             'household' => $beneficiaries['household'],
        //             'priorities' => $beneficiaries['priorities'],
        //             'networks' => $networks['total'],
        //             'incentives' => $incentives['total'],
        //             'patients' => $patients['total'],
        //             'requested' => $assistances['requested'],
        //             'assisted' => $assistances['assisted'],
        //         ];
        //     });

        //     $query = request()->query();
        //     $query['page'] = $barangays->currentPage();

        //     return new \Illuminate\Pagination\LengthAwarePaginator(
        //         $itemsTransformed,
        //         $barangays->total(),
        //         $barangays->perPage(),
        //         $barangays->currentPage(),
        //         [
        //             'path' => forceHttps(\Request::url()),
        //             'query' => $query
        //         ]
        //     );
        // endif;
    }

    public function showBeneficiariesVoterTypeList($request)
    {
        $company = Auth::user()->company();

        $provinces = $company->barangay_report_provinces
            ? explode(',', $company->barangay_report_provinces)
            : [];

        $slugType = addslashes('App\Models\Beneficiary');

        $sql = "SELECT ";
        $sql .= "CONCAT(ben.last_name, ', ', ben.first_name, ' ', COALESCE(ben.middle_name, '')) AS full_name, ";
        $sql .= "ben.date_of_birth AS date_of_birth, ";
        $sql .= "CASE ";
        $sql .= "WHEN ben.gender = 1 THEN 'MALE' ";
        $sql .= "WHEN ben.gender = 2 THEN 'FEMALE' ";
        $sql .= "END AS gender_name, ";
        $sql .= "ben.latitude AS latitude, ";
        $sql .= "ben.longitude AS longitude, ";
        $sql .= "ben.voter_type as voter_type, ";
        $sql .= "CASE ";
        $sql .= "WHEN ben.voter_type = 1 THEN 'OTHERS' ";
        $sql .= "WHEN ben.voter_type = 2 THEN 'COMMAND VOTES' ";
        $sql .= "WHEN ben.voter_type = 3 THEN 'SURE VOTES' ";
        $sql .= "WHEN ben.voter_type = 4 THEN 'SWING VOTES' ";
        $sql .= "WHEN ben.voter_type = 5 THEN 'BLOCK LISTED' ";
        $sql .= "END AS voter_type_name, ";
        $sql .= "CASE ";
        $sql .= "WHEN ben.voter_type = 1 THEN 'gray' ";
        $sql .= "WHEN ben.voter_type = 2 THEN 'green' ";
        $sql .= "WHEN ben.voter_type = 3 THEN 'orange' ";
        $sql .= "WHEN ben.voter_type = 4 THEN 'yellow' ";
        $sql .= "WHEN ben.voter_type = 5 THEN 'red' ";
        $sql .= "END AS voter_type_color, ";
        $sql .= "slug.code AS slug_code ";
        $sql .= "FROM beneficiaries ben ";
        $sql .= "LEFT JOIN slugs slug ON slug.slug_id = ben.id AND slug_type = '{$slugType}' ";
        $sql .= "WHERE ben.company_id = {$company->id} ";

        if ($provinces[0] == '0314'):
            $sql .= "AND ben.city_id = '031420' ";
        endif;

        $sql .= "AND ben.latitude IS NOT NULL ";
        $sql .= "AND ben.longitude IS NOT NULL ";
        $sql .= "AND ben.deleted_at IS NULL ";

        $data = \DB::select($sql);

        return $data;
    }

    public function store($request)
    {
        $company = Auth::user()->company();

        $newBeneficiary = $this->baseRepository->store($request, $company);

        $this->baseRepository->updateAddress($newBeneficiary);

        if ($request->input('photo')):
            $newBeneficiary->update(
                $this->baseRepository->uploadPhoto(
                    $newBeneficiary->photo,
                    $request
                )
            );
        endif;

        self::crossMatchBeneficiaryAndVoters($newBeneficiary->id);

        // $this->companyRepository->checkAndCreateClassification(
        //         $request->input('classification'),
        //         $company
        // );

        return $newBeneficiary;
    }

    public function crossMatchBeneficiaryAndVoters($id)
    {
        $beneficiary = Beneficiary::find($id);

        $voterData = Voter::where('first_name', $beneficiary->first_name)
            ->where('middle_name', $beneficiary->middle_name)
            ->where('last_name', $beneficiary->last_name)->first();

        if ($voterData) {
            $beneficiary->update([
                'verify_voter' => 1
            ]);
        }
    }

    public function import($request)
    {
        \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\BeneficiariesImport, $request->file('file'));

        return response([
            'message' => 'Data has been imported successfully.'
        ], 200);
    }

    public function check($request)
    {
        $company = Auth::user()->company();

        $model = new Beneficiary;

        $beneficiary = $model->where('company_id', $company->id)
            ->where('first_name', $request->input('first_name'))
            ->where('last_name', $request->input('last_name'))
            ->where('date_of_birth', $request->input('date_of_birth'))
            ->first();

        if ($beneficiary)
            return $beneficiary->toArrayBeneficiaryCheckingRelated();

        return $beneficiary;
    }

    public function show($request, $code)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $beneficiary = $model->slug;

        $this->companyRepository->isBeneficiaryRelated($company, $beneficiary->id);

        return $beneficiary->toArrayBeneficiariesRelated();
    }

    public function edit($request, $code)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $beneficiary = $model->slug;

        $this->companyRepository->isBeneficiaryRelated($company, $beneficiary->id);

        return $beneficiary->toArrayEdit();
    }

    public function update($request, $code)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $beneficiary = $model->slug;

        $this->companyRepository->isBeneficiaryRelated($company, $beneficiary->id);

        $beneficiary->update(
            $this->baseRepository->update($request)
        );

        if ($request->has('photo_for_upload') && $request->input('photo_for_upload')):
            $beneficiary->update(
                $this->baseRepository->uploadPhoto(
                    $beneficiary->photo,
                    [
                        'photo' => $request->input('photo_for_upload')
                    ]
                )
            );
        endif;

        $this->baseRepository->updateAddress($beneficiary);

        //     $this->companyRepository->checkAndCreateClassification(
        //         $request->input('classification'),
        //         $company
        //    );

        return (Beneficiary::find($beneficiary->id))->toArrayBeneficiariesRelated();
    }

    public function updatePhoto($request, $code)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $beneficiary = $model->slug;

        $this->companyRepository->isBeneficiaryRelated($company, $beneficiary->id);

        $beneficiary->update(
            $this->baseRepository->uploadPhoto(
                $beneficiary->photo,
                $request
            )
        );

        return (Beneficiary::find($beneficiary->id))->toArrayBeneficiariesRelated();
    }

    public function updateOfficer($request, $code)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $beneficiary = $model->slug;

        $this->companyRepository->isBeneficiaryRelated($company, $beneficiary->id);

        $beneficiary->update([
            'is_officer' => $request->input('is_officer'),
            'officer_classification' => $request->input('officer_classification')
                ? strtoupper($request->input('officer_classification'))
                : null,
            'updated_by' => Auth::id()
        ]);

        return (Beneficiary::find($beneficiary->id))->toArrayBeneficiariesRelated();
    }

    public function updateIsVoter($request, $code)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $beneficiary = $model->slug;

        $this->companyRepository->isBeneficiaryRelated($company, $beneficiary->id);

        $beneficiary->update([
            'is_voter' => $request->input('is_voter'),

            'updated_by' => Auth::id()
        ]);

        return (Beneficiary::find($beneficiary->id))->toArrayBeneficiariesRelated();
    }

    public function checkVoters($request, $code)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $beneficiary = $model->slug;

        $this->companyRepository->isBeneficiaryRelated($company, $beneficiary->id);

        return $company->voters()
            ->where(function ($q) use ($beneficiary) {
                $q->where('first_name', $beneficiary->first_name)
                    ->where('middle_name', $beneficiary->middle_name)
                    ->where('last_name', $beneficiary->last_name);
            })->get();
    }

    public function checkBulkVoters($request)
    {
        $company = Auth::user()->company();
        $slugCodes = Beneficiary::with('slug')
            ->whereHas('slug')
            ->pluck('slug.code', 'id');

        if ($slugCodes->isEmpty()) {
            return response()->json(['message' => 'No matching slugs found.'], 200);
        }


        DB::table('voters')
            ->join('beneficiaries', function ($join) use ($slugCodes) {
                $join->on('voters.first_name', '=', 'beneficiaries.first_name')
                    ->on('voters.last_name', '=', 'beneficiaries.last_name');
            })
            ->whereIn('beneficiaries.id', $slugCodes->keys())
            ->where('voters.company_id', $company->id)
            ->update(['voters.verify_voters' => 1]);

        return response()->json([
            'message' => 'Voter verification updated successfully!',
            'verified_voters' => DB::table('voters')->where('verify_voters', 1)->count()
        ]);
    }


    public function updateVoter($request, $code)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $beneficiary = $model->slug;

        $this->companyRepository->isBeneficiaryRelated($company, $beneficiary->id);

        $beneficiary->update([
            'is_voter' => $request->input('is_voter'),
            'verify_voter' => $request->input('verify_voter'),
            'voter_type' => $request->input('voter_type')
                ? $request->input('voter_type')
                : 1,
            'updated_by' => Auth::id()
        ]);

        // $voter = Voter::where('first_name', $beneficiary->first_name)
        //     ->where('last_name', $beneficiary->last_name)
        //     ->when(!empty($beneficiary->middle_name), function ($query) use ($beneficiary) {
        //         $middleName = $beneficiary->middle_name;
        //         if (preg_match('/^[A-Z]\.?$/', $middleName)) {
        //             $middleName = rtrim($middleName, '.');
        //             return $query->where('middle_name', 'LIKE', "$middleName%");
        //         } else {
        //             return $query->where('middle_name', 'LIKE', $middleName);
        //         }
        //     })
        //     ->first();

        $voter = Voter::where('first_name', $beneficiary->first_name)
            ->where('middle_name', $beneficiary->middle_name)
            ->where('last_name', $beneficiary->last_name)
            ->first();

        if ($voter) {
            $voter->update([
                'date_of_birth' => $beneficiary->date_of_birth,
                'gender' => $beneficiary->gender,
            ]);
        }


        if ($voter) {
            $voter->update([
                'date_of_birth' => $beneficiary->date_of_birth,
                'gender' => $beneficiary->gender,
            ]);
        }

        return (Beneficiary::find($beneficiary->id))->toArrayBeneficiariesRelated();
    }

    public function showProfile($request, $code)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $beneficiary = $model->slug;

        $this->companyRepository->isBeneficiaryRelated($company, $beneficiary->id);

        return $beneficiary->toArrayBeneficiariesRelated();
    }

    public function showMobileNo($request, $code)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $beneficiary = $model->slug;

        $this->companyRepository->isBeneficiaryRelated($company, $beneficiary->id);

        return [
            'beneficiary_name' => $beneficiary->fullName('F M L'),
            'beneficiary_mobile_number' => $beneficiary->mobile_no,
            'emergency_name' => $beneficiary->emergency_contact_name,
            'emergency_mobile_number' => $beneficiary->emergency_contact_no,
        ];
    }

    public function destroy($request, $code)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $beneficiary = $model->slug;

        $this->companyRepository->isBeneficiaryRelated($company, $beneficiary->id);

        $this->baseRepository->isAllowedToDestroy($beneficiary);

        $beneficiary->incentives()->delete();

        $beneficiary->delete();

        return response([
            'message' => 'Data deleted successfully.'
        ], 200);
    }

    public function arrangeRelatives($request, $code)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $beneficiary = $model->slug;

        $this->companyRepository->isBeneficiaryRelated($company, $beneficiary->id);

        $relatives = $request->input('relatives');

        foreach ($relatives as $row):

            $relative = BeneficiaryRelative::find($row['id']);

            $relative->update([
                'order_no' => $row['order_no'],
                'updated_by' => Auth::id()
            ]);

        endforeach;

        $this->relativeRepository->refreshOrderNo($beneficiary);

        return response([
            'message' => 'Family/relatives have been re-arranged.'
        ], 200);
    }

    public function storeRelative($request, $code)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $beneficiary = $model->slug;

        $this->companyRepository->isBeneficiaryRelated($company, $beneficiary->id);

        $relativeSlug = Slug::findCodeOrDie($request->input('relativeCode'));
        $relative = $relativeSlug->slug;

        $beneficiaryRelativeModel = new BeneficiaryRelative;

        $relationships = $beneficiaryRelativeModel->relationships;

        $this->baseRepository->isValidRelative($beneficiary, $relative->id);

        $this->baseRepository->isRelativeAlreadyExists($beneficiary, $relative->id);

        $newRelative = $beneficiary->relatives()->save(
            $this->relativeRepository->new(
                $beneficiary,
                $relative,
                $request->input('relationship')
            )
        );

        $relative->relatives()->save(
            $this->relativeRepository->new(
                $relative,
                $beneficiary,
                $relationships[$request->input('relationship')][$beneficiary->gender]
            )
        );

        return $newRelative;
    }

    public function destroyRelative($request, $code, $id)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $beneficiary = $model->slug;

        $this->companyRepository->isBeneficiaryRelated($company, $beneficiary->id);

        $record = $this->baseRepository->isRelativeRelated($beneficiary, $id);

        $relative = $record->relative;

        $viceVersaRelative = $relative->relatives('related_resident_id', $beneficiary->id)->first();

        if ($viceVersaRelative)
            $viceVersaRelative->delete();

        $record->delete();

        $this->relativeRepository->refreshOrderNo($beneficiary);

        $this->relativeRepository->refreshOrderNo($relative);

        return response([
            'message' => 'Data deleted successfully.'
        ], 200);
    }

    public function arrangeFamilies($request, $code)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $beneficiary = $model->slug;

        $this->companyRepository->isBeneficiaryRelated($company, $beneficiary->id);

        $families = $request->input('families');

        foreach ($families as $row):

            $family = BeneficiaryFamily::find($row['id']);

            $family->update([
                'order_no' => $row['order_no'],
                'updated_by' => Auth::id()
            ]);

        endforeach;

        $this->familyRepository->refreshOrderNo($beneficiary);

        return response([
            'message' => 'Family/relatives have been re-arranged.'
        ], 200);
    }

    public function storeFamily($request, $code)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $beneficiary = $model->slug;

        $this->companyRepository->isBeneficiaryRelated($company, $beneficiary->id);

        $newFamily = $beneficiary->families()->save(
            $this->familyRepository->new(
                $request,
                $beneficiary,
            )
        );

        return $newFamily;
    }

    public function updateFamily($request, $code, $id)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $beneficiary = $model->slug;

        $this->companyRepository->isBeneficiaryRelated($company, $beneficiary->id);

        $family = $this->baseRepository->isFamilyRelated($beneficiary, $id);

        $family->update(
            $this->familyRepository->update($request)
        );

        return (BeneficiaryFamily::find($family->id))->toArrayBeneficiaryFamiliesRelated();
    }

    public function destroyFamily($request, $code, $id)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $beneficiary = $model->slug;

        $this->companyRepository->isBeneficiaryRelated($company, $beneficiary->id);

        $family = $this->baseRepository->isFamilyRelated($beneficiary, $id);

        $family->delete();

        $this->familyRepository->refreshOrderNo($beneficiary);

        return response([
            'message' => 'Data deleted successfully.'
        ], 200);
    }

    public function showNetworkByList($request, $code)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $beneficiary = $model->slug;

        $this->companyRepository->isBeneficiaryRelated($company, $beneficiary->id);

        $parentingNetworks = $beneficiary->parentingNetworks()->orderBy('order_no', 'asc')->get();

        $arrParentingNetworks = [];

        foreach ($parentingNetworks as $parentingNetwork):

            $arrParentingNetworks[] = [
                'id' => $parentingNetwork->id,
                'beneficiary' => $parentingNetwork->beneficiary->toArrayBeneficiaryNetworksRelated(),
                // 'networks' => $parentingNetwork->beneficiary->parentingNetworks()->orderBy('order_no', 'asc')->get()
            ];
        endforeach;

        return $arrParentingNetworks;
    }
    public function downloadNetworkByList($request, $code)
    {
        ini_set('max_execution_time', 300);
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $beneficiary = $model->slug;

        $this->companyRepository->isBeneficiaryRelated($company, $beneficiary->id);

        $parentingNetworks = $beneficiary->parentingNetworks()->orderBy('order_no', 'asc')->get();

        $request->merge([
            'parentingNetworks' => $parentingNetworks,
            'officer' => $beneficiary,
        ]);
        $file = $company->report_file;

        $fileName = $this->pdfRepository->fileName(
            $request->get('from'),
            $request->get('to'),
            strtolower($company->name) . '-beneficaries-networ-report',
            '.xlsx'
        );

        $namespace = '\App\Exports\Beneficiaries\\' . $file . '\\BeneficiaryByNetworkReport';

        \Excel::store(
            new $namespace($request, $company),
            'storage/exports/' . $fileName,
            env('STORAGE_DISK', 'public')
        );

        return [
            'path' => env('CDN_URL') . '/storage/exports/' . $fileName
        ];

        return $arrParentingNetworks;
    }



    public function showNetworkByChart($request, $code)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $beneficiary = $model->slug;

        $this->companyRepository->isBeneficiaryRelated($company, $beneficiary->id);

        $parentingNetworks = $beneficiary->parentingNetworks()->orderBy('order_no', 'asc')->get();

        $arrParentingNetworks = [];

        foreach ($parentingNetworks as $parentingNetwork):

            $arrParentingNetworks[] = [
                'id' => $parentingNetwork->id,
                'beneficiary' => $parentingNetwork->beneficiary->toArrayBeneficiaryNetworksRelated(),
                'networks' => $parentingNetwork->beneficiary->parentingNetworks()->orderBy('order_no', 'asc')->get()
            ];
        endforeach;

        return $arrParentingNetworks;
    }

    public function storeNetwork($request, $code)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $beneficiary = $model->slug;

        $this->companyRepository->isBeneficiaryRelated($company, $beneficiary->id);

        $targetBeneficiarySlug = Slug::findCodeOrDie($request->input('beneficiaryCode'));

        $targetBeneficiary = $targetBeneficiarySlug->slug;

        $newNetwork = $this->networkRepository->addToNetwork($beneficiary, $targetBeneficiary, $company);

        $this->networkRepository->addIncentiveToParentNetworks($targetBeneficiary, $company);

        return [
            'id' => $newNetwork->id,
            'beneficiary' => $targetBeneficiary->toArrayBeneficiaryNetworksRelated(),
            'networks' => [],
        ];
    }

    public function destroyNetwork($request, $code, $id)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $beneficiary = $model->slug;

        $this->companyRepository->isBeneficiaryRelated($company, $beneficiary->id);

        $network = $this->baseRepository->isParentingNetworkRelated($beneficiary, $id);

        $this->networkRepository->deductIncentiveToParentNetworks(
            $network->beneficiary()->first(),
            $company
        );

        $network->delete();

        return response([
            'message' => 'Data deleted successfully.'
        ], 200);
    }

    public function storeIncentive($request, $code)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $beneficiary = $model->slug;

        $this->companyRepository->isBeneficiaryRelated($company, $beneficiary->id);

        $newIncentive = $beneficiary->incentives()->save(
            $this->incentiveRepository->new(
                $request,
                $company
            )
        );

        $this->baseRepository->refreshIncentives($beneficiary);

        return $newIncentive;
    }

    public function destroyIncentive($request, $code, $id)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $beneficiary = $model->slug;

        $this->companyRepository->isBeneficiaryRelated($company, $beneficiary->id);

        $incentive = $this->baseRepository->isIncentiveRelated($beneficiary, $id);

        $incentive->delete();

        $this->baseRepository->refreshIncentives($beneficiary);

        return response([
            'message' => 'Data deleted successfully.'
        ], 200);
    }

    public function storeAssistance($request, $code)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $beneficiary = $model->slug;

        $this->companyRepository->isBeneficiaryRelated($company, $beneficiary->id);

        $newAssistance = $this->assistanceRepository->storeBeneficiary(
            $request,
            $beneficiary
        );

        $this->baseRepository->updateAssistancesCount($beneficiary);

        return $newAssistance;
    }

    public function updateAssistance($request, $code, $id)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $beneficiary = $model->slug;

        $this->companyRepository->isBeneficiaryRelated($company, $beneficiary->id);

        $assistance = $this->baseRepository->isAssistanceRelated($beneficiary, $id);

        $assistance->update(
            $this->assistanceRepository->update($request)
        );

        return (BeneficiaryAssistance::find($assistance->id))->toArrayBeneficiaryAssistancesRelated();
    }

    public function destroyAssistance($request, $code, $id)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $beneficiary = $model->slug;

        $this->companyRepository->isBeneficiaryRelated($company, $beneficiary->id);

        $assistance = $this->baseRepository->isAssistanceRelated($beneficiary, $id);

        $assistance->delete();

        $this->baseRepository->updateAssistancesCount($beneficiary);

        return response([
            'message' => 'Data deleted successfully.'
        ], 200);
    }

    public function storePatient($request, $code)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $beneficiary = $model->slug;

        $this->companyRepository->isBeneficiaryRelated($company, $beneficiary->id);

        $newPatient = $beneficiary->patients()->save(
            $this->patientRepository->new(
                $request,
                $company
            )
        );

        return $newPatient;
    }

    public function updatePatient($request, $code, $id)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $beneficiary = $model->slug;

        $this->companyRepository->isBeneficiaryRelated($company, $beneficiary->id);

        $patient = $this->baseRepository->isPatientRelated($beneficiary, $id);

        $patient->update(
            $this->patientRepository->update($request)
        );

        return (BeneficiaryPatient::find($patient->id))->toArrayBeneficiaryPatientsRelated();
    }

    public function updatePatientStatus($request, $code, $id, $status)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $beneficiary = $model->slug;

        $this->companyRepository->isBeneficiaryRelated($company, $beneficiary->id);

        $patient = $this->baseRepository->isPatientRelated($beneficiary, $id);

        $patient->update([
            'status' => $status,
            'updated_by' => Auth::id()
        ]);

        return (BeneficiaryPatient::find($patient->id))->toArrayBeneficiaryPatientsRelated();
    }

    public function destroyPatient($request, $code, $id)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $beneficiary = $model->slug;

        $this->companyRepository->isBeneficiaryRelated($company, $beneficiary->id);

        $patient = $this->baseRepository->isPatientRelated($beneficiary, $id);

        $patient->delete();

        return response([
            'message' => 'Data deleted successfully.'
        ], 200);
    }

    public function storeMessage($request, $code)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $beneficiary = $model->slug;

        $this->companyRepository->isBeneficiaryRelated($company, $beneficiary->id);

        $this->smsRepository->sendSms($request);

        $newMessage = $beneficiary->messages()->save(
            $this->messageRepository->new(
                $request,
                $company
            )
        );

        $beneficiary->update([
            'last_message_date' => $newMessage->message_date,
            'updated_by' => Auth::id()
        ]);

        return $newMessage;
    }

    public function storeCall($request, $code)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $beneficiary = $model->slug;

        $this->companyRepository->isBeneficiaryRelated($company, $beneficiary->id);

        $newCall = $beneficiary->calls()->save(
            $this->callRepository->new(
                $request,
                $company
            )
        );

        $beneficiary->update([
            'last_call_date' => $newCall->call_date,
            'updated_by' => Auth::id()
        ]);

        return $newCall;
    }

    public function updateCall($request, $code, $id)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $beneficiary = $model->slug;

        $this->companyRepository->isBeneficiaryRelated($company, $beneficiary->id);

        $call = $this->baseRepository->isCallRelated($beneficiary, $id);

        $call->update([
            'call_minutes' => $request->input('call_minutes') ?: 1,
            'call_url' => $request->input('call_url') ?: null,
            'status' => 2,
        ]);

        $this->companyCallTransactionRepository->store([
            'transaction_date' => $call->call_date,
            'amount' => computeCallMinutes($request->input('call_minutes') ?: 1),
            'recording_url' => $request->input('call_url') ?: null,
            'mobile_number' => $call->mobile_number,
            'status' => 2,
            'source' => 'BENEFICIARY',
        ], $company);

        return (BeneficiaryCall::find($call->id))->toArrayBeneficiaryCallsRelated();
    }

    public function storeIdentification($request, $code)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $beneficiary = $model->slug;

        $this->companyRepository->isBeneficiaryRelated($company, $beneficiary->id);

        $newIdentification = $beneficiary->identifications()->save(
            $this->identificationRepository->new(
                $request,
                $company
            )
        );

        return $newIdentification;
    }

    public function downloadIdentification($request, $code, $id)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $beneficiary = $model->slug;

        $this->companyRepository->isBeneficiaryRelated($company, $beneficiary->id);

        $identification = $this->baseRepository->isIdentificationRelated($beneficiary, $id);

        return $this->identificationRepository->download($identification);
    }

    public function destroyIdentification($request, $code, $id)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $beneficiary = $model->slug;

        $this->companyRepository->isBeneficiaryRelated($company, $beneficiary->id);

        $identification = $this->baseRepository->isIdentificationRelated($beneficiary, $id);

        $identification->delete();

        return response([
            'message' => 'Data deleted successfully.'
        ], 200);
    }

    public function storeDocument($request, $code)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $beneficiary = $model->slug;

        $this->companyRepository->isBeneficiaryRelated($company, $beneficiary->id);

        $newDocument = $beneficiary->documents()->save(
            $this->documentRepository->new(
                $request,
                $company
            )
        );

        return $newDocument;
    }

    public function downloadDocument($request, $code, $id)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $beneficiary = $model->slug;

        $this->companyRepository->isBeneficiaryRelated($company, $beneficiary->id);

        $document = $this->baseRepository->isDocumentRelated($beneficiary, $id);

        return $this->documentRepository->download($document);
    }

    public function destroyDocument($request, $code, $id)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $beneficiary = $model->slug;

        $this->companyRepository->isBeneficiaryRelated($company, $beneficiary->id);

        $document = $this->baseRepository->isDocumentRelated($beneficiary, $id);

        $document->delete();

        return response([
            'message' => 'Data deleted successfully.'
        ], 200);
    }

    public function storeBeneficiaryOption($request)
    {
        $company = Auth::user()->company();

        $newBeneficiary = $this->baseRepository->store($request, $company);

        $this->baseRepository->updateAddress($newBeneficiary);

        return $newBeneficiary;
    }
}
?>