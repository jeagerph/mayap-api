<?php

namespace App\Models;

use Carbon\Carbon;

use App\Models\Model;
use Illuminate\Support\Facades\DB;
use App\Observers\BeneficiaryObserver as Observer;

class Beneficiary extends Model
{
    use Observer;

    public $searchFields = [
        'firstName' => ':first_name',
        'middleName' => ':middle_name',
        'lastName' => ':last_name',
    ];

    public $filterFields = [
        'companyCode' => 'slug:company_id',
        'isPriority' => ':is_priority',
        'isHousehold' => ':is_household',
        'isOfficer' => ':is_officer',
        'provCode' => ':province_id',
        'cityCode' => ':city_id',
        'barangay' => ':barangay_id',
        'voterType' => ':voter_type',
        'isVoter' => ':is_voter',
    ];

    public $sortFields = [
        'created' => ':created_at',
        'dateRegistered' => ':date_registered',
        'isPriority' => ':is_priority',
        'rating' => ':rating',
    ];

    public $rangeFields = [
        'dateRegistered' => ':date_registered'
    ];

    public $genderOptions = [
        1 => 'MALE',
        2 => 'FEMALE'
    ];

    public $civilStatusOptions = [
        1 => 'SINGLE',
        2 => 'MARRIED',
        3 => 'DIVORCED',
        4 => 'SEPARATED',
        5 => 'WIDOWED',
        6 => 'OTHERS'
    ];

    public $voterTypeOptions = [
        1 => [
            'name' => 'OTHERS',
            'short' => 'OTH',
            'color' => '#6c757d',
        ],
        2 => [
            'name' => 'COMMAND VOTES',
            'short' => 'CV',
            'color' => '#17a00e',
        ],
        3 => [
            'name' => 'SURE VOTES',
            'short' => 'SV',
            'color' => '#ff8f07',
        ],
        4 => [
            'name' => 'SWING VOTES',
            'short' => 'SW',
            'color' => '#fffb22df',
        ],
        5 => [
            'name' => 'BLOCK LISTED',
            'short' => 'BL',
            'color' => '#f41127'
        ]
    ];

    public function slug()
    {
        return $this->morphOne('App\Models\Slug', 'slug');
    }

    public function company()
    {
        return $this->belongsTo('App\Models\Company');
    }

    public function province()
    {
        return $this->belongsTo('App\Models\Province', 'province_id', 'prov_code');
    }



    public function city()
    {
        return $this->belongsTo('App\Models\City', 'city_id', 'city_code');
    }

    public function barangay()
    {
        return $this->belongsTo('App\Models\Barangay', 'barangay_id');
    }

    public function activities()
    {
        return $this->morphMany('App\Models\Activity', 'module');
    }

    public function relatives()
    {
        return $this->hasMany('App\Models\BeneficiaryRelative');
    }

    public function relatedRelatives()
    {
        return $this->hasMany('App\Models\BeneficiaryRelative', 'related_beneficiary_id');
    }

    public function network()
    {
        return $this->hasOne('App\Models\BeneficiaryNetwork', 'beneficiary_id');
    }

    public function parentingNetworks()
    {
        return $this->hasMany('App\Models\BeneficiaryNetwork', 'parent_beneficiary_id');
    }

    public function incentives()
    {
        return $this->hasMany('App\Models\BeneficiaryIncentive');
    }

    public function assistances()
    {
        return $this->hasMany('App\Models\BeneficiaryAssistance');
    }

    public function patients()
    {
        return $this->hasMany('App\Models\BeneficiaryPatient');
    }

    public function messages()
    {
        return $this->hasMany('App\Models\BeneficiaryMessage');
    }

    public function calls()
    {
        return $this->hasMany('App\Models\BeneficiaryCall');
    }

    public function identifications()
    {
        return $this->hasMany('App\Models\BeneficiaryIdentification');
    }

    public function documents()
    {
        return $this->hasMany('App\Models\BeneficiaryDocument');
    }

    public function families()
    {
        return $this->hasMany('App\Models\BeneficiaryFamily');
    }

    public function fullName($format = 'L, F M')
    {
        switch ($format):

            case 'L, F M':
                return $this->last_name . ', ' . $this->first_name . ($this->middle_name ? ' ' . $this->middle_name : '');
                break;

            case 'F M L':
                return $this->first_name . ' ' . ($this->middle_name ? $this->middle_name . ' ' : '') . $this->last_name;
                break;

            case 'L, F MI':
                return $this->last_name . ', ' . $this->first_name . ($this->middle_name ? (' ' . $this->middle_name[0] . '.') : '');
                break;

            default:
                return $this->last_name . ', ' . $this->first_name . ($this->middle_name ? ' ' . $this->middle_name : '');
                break;
        endswitch;
    }

    public function age()
    {
        return (new \Carbon\Carbon($this->date_of_birth))->age;
    }

    public function createdBy()
    {
        return $this->belongsTo('App\Models\User', 'created_by');
    }

    public function creator()
    {
        $createdBy = $this->createdBy;

        return [
            'full_name' => $createdBy && $createdBy->account
                ? $createdBy->account->full_name
                : 'DELETED USER'
        ];
    }

    public function issuedIds()
    {
        $issuedIds = BeneficiaryIdentification::where('beneficiary_id', $this->id)
            ->where('is_printed', 1)
            ->where('name', 'LIKE', 'beneficiary id')
            ->count();

        return $issuedIds ?? 0;
    }

    public function toArray()
    {
        $arr = [
            'slug' => $this->slug,
            'code' => $this->code,
            'id' => $this->id,
            'full_name' => $this->fullName(),
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'verify_voter' => $this->verify_voter,
            'voter_details' => null,
            'issued_ids' => $this->issuedIds(),
            'last_name' => $this->last_name,
            'date_registered' => $this->date_registered,
            'photo' => $this->photo
                ? env('CDN_URL') . '/storage/' . $this->photo
                : '',
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at
                ? $this->updated_at->toDateTimeString()
                : $this->created_at->toDateTimeString()
        ];

        if ($this->verify_voter != null) {

            $voter_details = Voter::where('first_name', $this->first_name)
                ->where('middle_name', $this->middle_name)
                ->where('last_name', $this->last_name)
                ->first();

            if ($voter_details) {
                $arr['voter_details'] = [
                    'precinct_no' => $voter_details->precinct_no,
                ];
            }
        }

        if (request()->has('beneficiaries-related')):

            $arr['barangay'] = $this->barangay;
            $arr['city'] = $this->city;
            $arr['province'] = $this->province;
            $arr['date_of_birth'] = $this->date_of_birth;
            $arr['gender'] = [
                'id' => $this->gender,
                'name' => $this->genderOptions[$this->gender]
            ];
            $arr['mobile_no'] = $this->mobile_no;
            $arr['rating'] = $this->rating;
            $arr['incentive'] = $this->incentive;
            $arr['is_priority'] = $this->is_priority;
            $arr['is_officer'] = $this->is_officer;
            $arr['officer_classification'] = $this->officer_classification;
            $arr['remarks'] = $this->remarks;
            $arr['networks_count'] = $this->parentingNetworks()->count();
            $arr['assistances_count'] = $this->assistances_count;
            $arr['last_message_date'] = $this->last_message_date;
            $arr['last_call_date'] = $this->last_call_date;
            $arr['last_document_date'] = null;
            $arr['last_id_date'] = null;
            $arr['latitude'] = $this->latitude;
            $arr['longitude'] = $this->longitude;
            $arr['is_voter'] = $this->is_voter;
            $arr['voter_type'] = $this->voterTypeOptions[$this->voter_type];
            $arr['voter_type']['id'] = $this->voter_type;

        endif;

        if (
            request()->has('beneficiary-options') ||
            request()->has('patient-options') ||
            request()->has('assistance-options')
        ):
            $arr['barangay'] = $this->barangay;
            $arr['city'] = $this->city;
            $arr['province'] = $this->province;
            $arr['date_of_birth'] = $this->date_of_birth;
            $arr['gender'] = [
                'id' => $this->gender,
                'name' => $this->genderOptions[$this->gender]
            ];
        endif;

        if (request()->has('beneficiary-network-options')):
            $arr['barangay'] = $this->barangay;
            $arr['city'] = $this->city;
            $arr['province'] = $this->province;
            $arr['date_of_birth'] = $this->date_of_birth;
            $arr['gender'] = [
                'id' => $this->gender,
                'name' => $this->genderOptions[$this->gender]
            ];
            $arr['network'] = $this->network
                ? $this->network->toArrayBeneficiaryNetworkOptions()
                : null;
        endif;

        return $arr;
    }

    public function toArrayEdit()
    {
        return [
            'province_id' => $this->province_id,
            'city_id' => $this->city_id,
            'barangay_id' => $this->barangay_id,
            'house_no' => $this->house_no,
            'zone' => $this->zone,
            'purok' => $this->purok,
            'street' => $this->street,
            'landmark' => $this->landmark,
            'house_ownership' => $this->house_ownership,
            'house_ownership_remarks' => $this->house_ownership_remarks,

            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'gender' => $this->gender ?: '',
            'mobile_no' => $this->mobile_no,
            'email' => $this->email,

            'place_of_birth' => $this->place_of_birth,
            'date_of_birth' => $this->date_of_birth,
            'civil_status' => $this->civil_status,
            'religion' => $this->religion,
            'citizenship' => $this->citizenship,

            'educational_attainment' => $this->educational_attainment,
            'occupation' => $this->occupation,
            'monthly_income' => $this->monthly_income,
            'source_of_income' => $this->source_of_income,
            'classification' => $this->classification,

            'is_household' => $this->is_household,
            'household_count' => $this->household_count,
            'household_voters_count' => $this->household_voters_count,
            'household_families_count' => $this->household_families_count,
            'is_priority' => $this->is_priority,
            'is_officer' => $this->is_officer,
            'officer_classification' => $this->officer_classification,

            'primary_school' => $this->primary_school,
            'primary_year_graduated' => $this->primary_year_graduated,
            'secondary_school' => $this->secondary_school,
            'secondary_course' => $this->secondary_course,
            'secondary_year_graduated' => $this->secondary_year_graduated,
            'tertiary_school' => $this->tertiary_school,
            'tertiary_course' => $this->tertiary_course,
            'tertiary_year_graduated' => $this->tertiary_year_graduated,
            'other_school' => $this->other_school,
            'other_course' => $this->other_course,
            'other_year_graduated' => $this->other_year_graduated,

            'emergency_contact_name' => $this->emergency_contact_name,
            'emergency_contact_no' => $this->emergency_contact_no,
            'emergency_contact_address' => $this->emergency_contact_address,

            'health_issues' => $this->health_issues,
            'problem_presented' => $this->problem_presented,
            'findings' => $this->findings,
            'assessment_recommendation' => $this->assessment_recommendation,
            'needs' => $this->needs,
            'remarks' => $this->remarks,

            'questionnaires' => $this->questionnaires
                ? json_decode($this->questionnaires)
                : [],

            'photo' => $this->photo
                ? env('CDN_URL', '') . '/storage/' . $this->photo
                : '',

            'latitude' => $this->latitude,
            'longitude' => $this->longitude,

            'is_voter' => $this->is_voter,
            'voter_type' => $this->voter_type,
        ];
    }

    public function toArrayBeneficiariesRelated()
    {
        $arr = [
            'slug' => $this->slug,
            'code' => $this->code,
            'province' => $this->province,
            'city' => $this->city,
            'barangay' => $this->barangay,
            'house_no' => $this->house_no,
            'issued_ids' => $this->issuedIds(),
            'zone' => $this->zone,
            'verify_voter' => $this->verify_voter,
            'voter_details' => null,
            'purok' => $this->purok,
            'street' => $this->street,
            'landmark' => $this->landmark,
            'address' => $this->address,
            'house_ownership' => $this->house_ownership,
            'house_ownership_remarks' => $this->house_ownership_remarks,

            'full_name' => $this->fullName(),
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'gender' => [
                'id' => $this->gender,
                'name' => $this->genderOptions[$this->gender]
            ],
            'mobile_no' => $this->mobile_no,
            'email' => $this->email,

            'place_of_birth' => $this->place_of_birth,
            'date_of_birth' => $this->date_of_birth,
            'civil_status' => $this->civil_status,
            'citizenship' => $this->citizenship,
            'religion' => $this->religion,

            'educational_attainment' => $this->educational_attainment,
            'occupation' => $this->occupation,
            'monthly_income' => $this->monthly_income,
            'source_of_income' => $this->source_of_income,
            'classification' => $this->classification,

            'is_household' => $this->is_household,
            'household_count' => $this->household_count,
            'household_voters_count' => $this->household_voters_count,
            'household_families_count' => $this->household_families_count,
            'is_priority' => $this->is_priority,
            'is_officer' => $this->is_officer,
            'officer_classification' => $this->officer_classification,

            'primary_school' => $this->primary_school,
            'primary_year_graduated' => $this->primary_year_graduated,
            'secondary_school' => $this->secondary_school,
            'secondary_course' => $this->secondary_course,
            'secondary_year_graduated' => $this->secondary_year_graduated,
            'tertiary_school' => $this->tertiary_school,
            'tertiary_course' => $this->tertiary_course,
            'tertiary_year_graduated' => $this->tertiary_year_graduated,
            'other_school' => $this->other_school,
            'other_course' => $this->other_course,
            'other_year_graduated' => $this->other_year_graduated,

            'photo' => $this->photo
                ? env('CDN_URL', '') . '/storage/' . $this->photo
                : '',

            'emergency_contact_name' => $this->emergency_contact_name,
            'emergency_contact_no' => $this->emergency_contact_no,
            'emergency_contact_address' => $this->emergency_contact_address,

            'health_issues' => $this->health_issues,
            'problem_presented' => $this->problem_presented,
            'findings' => $this->findings,
            'assessment_recommendation' => $this->assessment_recommendation,
            'needs' => $this->needs,
            'remarks' => $this->remarks,

            'questionnaires' => $this->questionnaires
                ? json_decode($this->questionnaires)
                : [],

            'incentive' => $this->incentive,
            'rating' => $this->rating,

            'networks_count' => $this->parentingNetworks()->count(),
            'assistances_count' => $this->assistances_count,

            'latitude' => $this->latitude,
            'longitude' => $this->longitude,

            'is_voter' => $this->is_voter,
            'voter_type' => [
                'id' => $this->voter_type,
                'name' => $this->voterTypeOptions[$this->voter_type]['name'],
                'short' => $this->voterTypeOptions[$this->voter_type]['short'],
                'color' => $this->voterTypeOptions[$this->voter_type]['color'],
            ],

            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at
                ? $this->updated_at->toDateTimeString()
                : $this->created_at->toDateTimeString(),
        ];

        if ($this->verify_voter != null) {

            $voter_details = Voter::where('first_name', $this->first_name)
                ->where('middle_name', $this->middle_name)
                ->where('last_name', $this->last_name)
                ->first();

            if ($voter_details) {
                $arr['voter_details'] = [
                    'precinct_no' => $voter_details->precinct_no,
                ];
            }
        }

        return $arr;
    }

    public function toArrayBeneficiaryCheckingRelated()
    {
        return [
            'slug' => $this->slug,
            'code' => $this->code,
            'full_name' => $this->fullName(),
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'date_of_birth' => $this->date_of_birth,
            'barangay' => $this->barangay,
            'city' => $this->city,
            'province' => $this->province,
            'photo' => $this->photo
                ? env('CDN_URL', '') . '/storage/' . $this->photo
                : '',
            'gender' => [
                'id' => $this->gender,
                'name' => $this->genderOptions[$this->gender]
            ],
        ];
    }

    public function toArrayBeneficiaryRelativesRelated()
    {
        return [
            'slug' => $this->slug,
            'code' => $this->code,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'date_of_birth' => $this->date_of_birth,
            'barangay' => $this->barangay,
            'city' => $this->city,
            'province' => $this->province,
            'photo' => $this->photo
                ? env('CDN_URL', '') . '/storage/' . $this->photo
                : '',
            'gender' => [
                'id' => $this->gender,
                'name' => $this->genderOptions[$this->gender]
            ],
        ];
    }

    public function toArrayBeneficiaryNetworksRelated()
    {
        return [
            'slug' => $this->slug,
            'code' => $this->code,
            'full_name' => $this->fullName(),
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'date_of_birth' => $this->date_of_birth,
            'barangay' => $this->barangay,
            'city' => $this->city,
            'province' => $this->province,
            'photo' => $this->photo
                ? env('CDN_URL', '') . '/storage/' . $this->photo
                : '',
            'gender' => [
                'id' => $this->gender,
                'name' => $this->genderOptions[$this->gender]
            ],
            'networks_count' => $this->parentingNetworks()->count()
        ];
    }

    public function toArrayBeneficiaryNetworkOptions()
    {
        return [
            'slug' => $this->slug,
            'code' => $this->code,
            'full_name' => $this->fullName(),
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'date_of_birth' => $this->date_of_birth,
            'barangay' => $this->barangay,
            'city' => $this->city,
            'province' => $this->province,
            'photo' => $this->photo
                ? env('CDN_URL', '') . '/storage/' . $this->photo
                : '',
            'gender' => [
                'id' => $this->gender,
                'name' => $this->genderOptions[$this->gender]
            ],
        ];
    }

    public function toArrayDashboardPatientsRelated()
    {
        return [
            'slug' => $this->slug,
            'code' => $this->code,
            'full_name' => $this->fullName(),
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'date_of_birth' => $this->date_of_birth,
            'barangay' => $this->barangay,
            'city' => $this->city,
            'province' => $this->province,
            'photo' => $this->photo
                ? env('CDN_URL', '') . '/storage/' . $this->photo
                : '',
            'gender' => [
                'id' => $this->gender,
                'name' => $this->genderOptions[$this->gender]
            ],
        ];
    }

    public function toArrayDashboardAssistancesRelated()
    {
        return [
            'slug' => $this->slug,
            'code' => $this->code,
            'full_name' => $this->fullName(),
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'date_of_birth' => $this->date_of_birth,
            'barangay' => $this->barangay,
            'city' => $this->city,
            'province' => $this->province,
            'photo' => $this->photo
                ? env('CDN_URL', '') . '/storage/' . $this->photo
                : '',
            'gender' => [
                'id' => $this->gender,
                'name' => $this->genderOptions[$this->gender]
            ],
        ];
    }

    public function toArrayBeneficiariesLocationsRelated()
    {
        return [
            'slug' => $this->slug,
            'code' => $this->code,
            'full_name' => $this->fullName(),
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'date_of_birth' => $this->date_of_birth,
            'barangay' => $this->barangay,
            'city' => $this->city,
            'province' => $this->province,
            'photo' => $this->photo
                ? env('CDN_URL', '') . '/storage/' . $this->photo
                : '',
            'gender' => [
                'id' => $this->gender,
                'name' => $this->genderOptions[$this->gender]
            ],
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];
    }

    public function toArrayPatientsRelated()
    {
        return [
            'slug' => $this->slug,
            'code' => $this->code,
            'full_name' => $this->fullName(),
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'date_of_birth' => $this->date_of_birth,
            'barangay' => $this->barangay,
            'city' => $this->city,
            'province' => $this->province,
            'photo' => $this->photo
                ? env('CDN_URL', '') . '/storage/' . $this->photo
                : '',
            'gender' => [
                'id' => $this->gender,
                'name' => $this->genderOptions[$this->gender]
            ],
        ];
    }

    public function toArrayAssistancesRelated()
    {
        return [
            'slug' => $this->slug,
            'code' => $this->code,
            'full_name' => $this->fullName(),
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'date_of_birth' => $this->date_of_birth,
            'barangay' => $this->barangay,
            'city' => $this->city,
            'province' => $this->province,
            'photo' => $this->photo
                ? env('CDN_URL', '') . '/storage/' . $this->photo
                : '',
            'gender' => [
                'id' => $this->gender,
                'name' => $this->genderOptions[$this->gender]
            ],
            'assistances_count' => $this->assistances_count,
            'networks_count' => $this->parentingNetworks()->count()
        ];
    }

    public function toArrayMyCompanyIncentivesRelated()
    {
        return [
            'slug' => $this->slug,
            'full_name' => $this->fullName(),
        ];
    }

    public function toArrayBenePublicDocumentRelated()
    {
        return [
            'slug' => $this->slug,
            'code' => $this->code,
            'barangay' => $this->barangay,
            'city' => $this->city,
            'province' => $this->province,
        ];
    }

    public function toArrayConstituentDocumentRelated()
    {
        return [
            'constituent_type.id' => 1,
            'full_name' => $this->fullName(),

        ];
    }

    public function toArrayPublicPointsDocumentRelated()
    {
        return [
            'point_received' => '0',
            'point_used' => '0',
            'point_rem' => '0'
        ];

    }

    public function toArrayPublicDocumentRelated()
    {
        return [
            'slug' => $this->slug,
            'code' => $this->code,
            'barangay' => $this->barangay,
            'full_name' => $this->fullName(),
            'city' => $this->city,
            'province' => $this->province,
            'photo' => $this->photo
                ? env('CDN_URL', '') . '/storage/' . $this->photo
                : '',
            'date_of_birth' => Carbon::parse($this->date_of_birth)->format('F d, Y'),

            'date_registered' => Carbon::parse($this->date_registered)->format('F d, Y')

        ];
    }



public function filtered($query,$request)
{
    $company = auth()->user()->company();

    return $query->where('company_id', $company->id)
        ->where(function ($q) use ($request) {
            if ($request->filled('firstName')) {
                $q->where('first_name', 'LIKE', '%' . $request->get('firstName') . '%');
            }

            if ($request->filled('middleName')) {
                $q->where('middle_name', 'LIKE', '%' . $request->get('middleName') . '%');
            }

            if ($request->filled('lastName')) {
                $q->where('last_name', 'LIKE', '%' . $request->get('lastName') . '%');
            }

            if ($request->filled('relativeName')) {
                $q->whereHas('families', function ($q) use ($request) {
                    $q->where('full_name', 'LIKE', '%' . $request->get('relativeName') . '%');
                });
            }

            if ($request->has('filter')) {
                $filter = $request->get('filter');

                if (isset($filter['isHousehold'])) {
                    $q->where('is_household', $filter['isHousehold']);
                }

                if (isset($filter['isPriority'])) {
                    $q->where('is_priority', $filter['isPriority']);
                }

                if (isset($filter['isOfficer'])) {
                    $q->where('is_officer', $filter['isOfficer']);
                }

                if (isset($filter['voterType'])) {
                    $q->where('voter_type', $filter['voterType']);
                }

                if (isset($filter['gender'])) {
                    $q->where('gender', $filter['gender']);
                }

                if (isset($filter['provCode'])) {
                    $q->where('province_id', $filter['provCode']);
                }

                if (isset($filter['cityCode'])) {
                    $q->where('city_id', $filter['cityCode']);
                }

                if (isset($filter['barangay'])) {
                    $q->where('barangay_id', $filter['barangay']);
                }

                if (isset($filter['isGreen']) && isset($filter['isOrange'])) {
                    $q->where(function ($query) {
                        $query->where('verify_voter', 2)
                            ->orWhere('verify_voter', 1);
                    });
                } elseif (isset($filter['isGreen'])) {
                    $q->where('verify_voter', 2);
                } elseif (isset($filter['isOrange'])) {
                    $q->where('verify_voter', 1);
                }

                if (isset($filter['age'])) {
                    $arrAgeRange = explode(',', $filter['age']);
                    $minDate = Carbon::today()->subYears($arrAgeRange[0])->format('Y');
                    $maxDate = Carbon::today()->subYears($arrAgeRange[1])->format('Y');
                    $q->whereBetween(DB::raw('YEAR(date_of_birth)'), [$maxDate, $minDate]);
                }

                if (isset($filter['hasNetwork'])) {
                    $q->has('parentingNetworks', '>=', 1);
                }
            }
        });

    if ($request->has('range') && isset($request->get('range')['dateRegistered'])) {
        $dates = explode(',', $request->get('range')['dateRegistered']);
        $query->where(function ($q) use ($dates) {
            $q->whereDate('date_registered', $dates[0])
                ->orWhereDate('date_registered', $dates[1])
                ->orWhereBetween('date_registered', [$dates[0], $dates[1]]);
        });
    }


}
}
