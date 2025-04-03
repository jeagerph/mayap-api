<?php

namespace App\Models;

use App\Models\Model;

use App\Observers\MemberObserver as Observer;

class Member extends Model
{
    use Observer;

    public $searchFields = [
        'firstName' => ':first_name',
        'middleName' => ':middle_name',
        'lastName' => ':last_name',
    ];

    public $filterFields = [
        'companyId' => ':company_id',
        'provinceId' => ':province_id',
        'cityId' => ':city_id',
        'brgyId' => ':barangay_id',
        'gender' => ':gender',
        'isHousehold' => ':is_household',
        'civilStatus' => ':civil_status',
    ];

    public $sortFields = [
        'firstName' => ':first_name',
        'lastName' => ':last_name',
        'created' => ':created_at',
        'dateRegistered' => ':date_registered'
    ];

    public $rangeFields = [
        'dateRegistered' => ':date_registered'
    ];

    public $genders = [
        1 => 'MALE',
        2 => 'FEMALE'
    ];

    public $civilStatuses = [
        1 => 'SINGLE',
        2 => 'MARRIED',
        3 => 'DIVORCED',
        4 => 'SEPARATED',
        5 => 'WIDOWED',
        6 => 'OTHERS'
    ];

    public $reportFieldOptions = [
        'date_of_birth' => [
            'name' => 'DATE OF BIRTH',
            'field' => 'date_of_birth'
        ],
        'gender' => [
            'name' => 'GENDER',
            'field' => 'gender'
        ],
        'address' => [
            'name' => 'ADDRESS',
            'field' => 'address'
        ],
        'barangay_purok_id' => [
            'name' => 'PUROK',
            'field' => 'barangay_purok_id'
        ],
        'barangay_street_id' => [
            'name' => 'STREET',
            'field' => 'barangay_street_id'
        ],
        'civil_status' => [
            'name' => 'CIVIL STATUS',
            'field' => 'civil_status'
        ],
        'is_household' => [
            'name' => 'HEAD OF HOUSEHOLD',
            'field' => 'is_household'
        ],
        'place_of_birth' => [
            'name' => 'PLACE OF BIRTH',
            'field' => 'place_of_birth'
        ],
        'resident_type' => [
            'name' => 'TYPE OF RESIDENT',
            'field' => 'resident_type'
        ],
        'precinct_no' => [
            'name' => 'PRECINCT NO.',
            'field' => 'precinct_no'
        ],
        'citizenship' => [
            'name' => 'CITIZENSHIP',
            'field' => 'citizenship'
        ],
        'blood_type' => [
            'name' => 'BLOOD TYPE',
            'field' => 'blood_type'
        ],
        'date_of_birth' => [
            'name' => 'DATE OF BIRTH',
            'field' => 'date_of_birth'
        ],
        'contact_no' => [
            'name' => 'CONTACT NO.',
            'field' => 'contact_no'
        ],
        'work_status' => [
            'name' => 'EMPLOYMENT STATUS',
            'field' => 'work_status'
        ],
        'primary_education' => [
            'name' => 'PRIMARY EDUCATION',
            'field' => 'primary_education'
        ],
        'secondary_education' => [
            'name' => 'SECONDARY EDUCATION',
            'field' => 'secondary_education'
        ],
        'tertiary_education' => [
            'name' => 'TERTIARY EDUCATION',
            'field' => 'tertiary_education'
        ],
        'other_education' => [
            'name' => 'OTHER EDUCATION',
            'field' => 'other_education'
        ],
        'age' => [
            'name' => 'AGE',
            'field' => 'date_of_birth'
        ],
        'email' => [
            'name' => 'EMAIL',
            'field' => 'email'
        ],
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

    public function classification()
    {
        return $this->belongsTo('App\Models\CompanyClassification', 'company_classification_id');
    }

    // public function documents()
    // {
    //     return $this->morphMany('App\Models\BarangayDocument', 'constituent');
    // }

    // public function identifications()
    // {
    //     return $this->morphMany('App\Models\BarangayIdentification', 'constituent');
    // }

    // public function relatives()
    // {
    //     return $this->hasMany('App\Models\ResidentRelative');
    // }

    // public function relatedRelatives()
    // {
    //     return $this->hasMany('App\Models\ResidentRelative', 'related_resident_id');
    // }
    
    // public function attachments()
    // {
    //     return $this->morphMany('App\Models\BarangayAttachment', 'module');
    // }

    public function activities()
    {
        return $this->morphMany('App\Models\Activity', 'module');
    }

    public function fullName($format = 'L, F M')
    {
        switch($format):

            case 'L, F M':
                return $this->last_name . ', ' . $this->first_name . ($this->middle_name ? ' ' . $this->middle_name : '');
                break;

            case 'F M L':
                return $this->first_name . ' ' . ($this->middle_name ? $this->middle_name .' ' : '') . $this->last_name;
                break;

            case 'L, F MI':
                return $this->last_name . ', ' . $this->first_name . ($this->middle_name ? (' ' . $this->middle_name[0] .'.') : '');
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

    public function toArray()
    {
        $arr = [
            'slug' => $this->slug,
            'code' => $this->code,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'date_registered' => $this->date_registered,
            'photo' => $this->photo
                ? env('CDN_URL', '') . '/storage/' . $this->photo
                : '',
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at
                ? $this->updated_at->toDateTimeString()
                : $this->created_at->toDateTimeString()
        ];

        if(request()->has('members-related')):

            $arr['company'] = $this->company->toArrayMembersRelated();
            $arr['barangay'] = $this->barangay;
            $arr['city'] = $this->city;
            $arr['province'] = $this->province;
            $arr['house_no'] = $this->house_no;
            $arr['date_of_birth'] = $this->date_of_birth;
            $arr['gender'] = [
                'id' => $this->gender,
                'name' => $this->genders[$this->gender]
            ];
            $arr['contact_no'] = $this->contact_no;
            $arr['is_household'] = $this->is_household;

        endif;

        return $arr;
    }

    public function toArrayEdit()
    {
        return [
            'company_classification_id' => $this->company_classification_id,
            'province_id' => $this->province_id,
            'city_id' => $this->city_id,
            'barangay_id' => $this->barangay_id,
            'house_no' => $this->house_no,

            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender ?: '',
            'contact_no' => $this->contact_no,
            'address' => $this->address,
            'email' => $this->email,
            'place_of_birth' => $this->place_of_birth,
            'civil_status' => $this->civil_status ?: '',
            'emergency_contact_name' => $this->emergency_contact_name,
            'emergency_contact_no' => $this->emergency_contact_no,
            'emergency_contact_address' => $this->emergency_contact_address,

            'resident_type' => $this->resident_type,
            'precinct_no' => $this->precinct_no,
            'citizenship' => $this->citizenship,
            'religion' => $this->religion,
            'eligibility' => $this->eligibility,
            'blood_type' => $this->blood_type,
            'health_history' => $this->health_history,
            'skills' => $this->skills,
            'pending' => $this->pending,
            'is_household' => $this->is_household,
            'date_registered' => $this->date_registered,
            'photo' => $this->photo
                ? env('CDN_URL', '') . '/storage/' . $this->photo
                : '',
            'gsis_sss_no' => $this->gsis_sss_no,
            'philhealth_no' => $this->philhealth_no,
            'pagibig_no' => $this->pagibig_no,
            'tin_no' => $this->tin_no,
            'voters_no' => $this->voters_no,
            'organ_donor' => $this->organ_donor,
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
            'work_status' => $this->work_status,
            'work_experiences' => $this->work_experiences
                ? json_decode($this->work_experiences)
                : [],
            'monthly_income_start' => $this->monthly_income_start,
            'monthly_income_end' => $this->monthly_income_end,
        ];
    }

    public function toArrayMembersRelated()
    {
        return [
            'slug' => $this->slug,
            'code' => $this->code,
            'province' => $this->province,
            'city' => $this->city,
            'barangay' => $this->barangay,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'date_of_birth' => $this->date_of_birth,
            'photo' => $this->photo
                ? env('CDN_URL', '') . '/storage/' . $this->photo
                : '',
            'left_thumbmark' => $this->left_thumbmark
                ? env('CDN_URL', '') . '/storage/' . $this->left_thumbmark
                : '',
            'right_thumbmark' => $this->right_thumbmark
                ? env('CDN_URL', '') . '/storage/' . $this->right_thumbmark
                : '',
            'signature' => $this->signature
                ? env('CDN_URL', '') . '/storage/' . $this->signature
                : '',
            'gender' => [
                    'id' => $this->gender,
                    'name' => $this->genders[$this->gender]
            ],
            'resident_type' => $this->resident_type,
            'is_household' => $this->is_household,
            'contact_no' => $this->contact_no,
            'address' => $this->address,
            'email' => $this->email,
            'place_of_birth' => $this->place_of_birth,
            'civil_status' => $this->civil_status
                ? [
                    'id' => $this->id,
                    'name' => $this->civilStatuses[$this->civil_status]
                ]
                : null,
            'emergency_contact_name' => $this->emergency_contact_name,
            'emergency_contact_no' => $this->emergency_contact_no,
            'emergency_contact_address' => $this->emergency_contact_address,
            'precinct_no' => $this->precinct_no,
            'citizenship' => $this->citizenship,
            'religion' => $this->religion,
            'eligibility' => $this->eligibility,
            'blood_type' => $this->blood_type,
            'health_history' => $this->health_history,
            'skills' => $this->skills,
            'pending' => $this->pending,
            'date_registered' => $this->date_registered,
            'gsis_sss_no' => $this->gsis_sss_no,
            'philhealth_no' => $this->philhealth_no,
            'pagibig_no' => $this->pagibig_no,
            'tin_no' => $this->tin_no,
            'voters_no' => $this->voters_no,
            'organ_donor' => $this->organ_donor,
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
            'work_status' => $this->work_status,
            'work_experiences' => $this->work_experiences
                ? json_decode($this->work_experiences)
                : [],
            'monthly_income_start' => $this->monthly_income_start,
            'monthly_income_end' => $this->monthly_income_end,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at
                ? $this->updated_at->toDateTimeString()
                : $this->created_at->toDateTimeString(),
        ];
    }

    public function toArrayMemberProfileRelated()
    {
        return [
            'slug' => $this->slug,
            'code' => $this->code,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'date_of_birth' => $this->date_of_birth,
            'photo' => $this->photo
                ? env('CDN_URL', '') . '/storage/' . $this->photo
                : '',
            'left_thumbmark' => $this->left_thumbmark
                ? env('CDN_URL', '') . '/storage/' . $this->left_thumbmark
                : '',
            'right_thumbmark' => $this->right_thumbmark
                ? env('CDN_URL', '') . '/storage/' . $this->right_thumbmark
                : '',
            'signature' => $this->signature
                ? env('CDN_URL', '') . '/storage/' . $this->signature
                : '',
            'gender' => [
                    'id' => $this->gender,
                    'name' => $this->genders[$this->gender]
            ],
            'resident_type' => $this->resident_type,
            'is_household' => $this->is_household,
            'contact_no' => $this->contact_no,
            'address' => $this->address,
            'email' => $this->email,
            'place_of_birth' => $this->place_of_birth,
            'civil_status' => $this->civil_status
                ? [
                    'id' => $this->id,
                    'name' => $this->civilStatuses[$this->civil_status]
                ]
                : null,
            'emergency_contact_name' => $this->emergency_contact_name,
            'emergency_contact_no' => $this->emergency_contact_no,
            'emergency_contact_address' => $this->emergency_contact_address,
            'precinct_no' => $this->precinct_no,
            'citizenship' => $this->citizenship,
            'religion' => $this->religion,
            'eligibility' => $this->eligibility,
            'blood_type' => $this->blood_type,
            'health_history' => $this->health_history,
            'skills' => $this->skills,
            'pending' => $this->pending,
            'work_status' => $this->work_status,
            'monthly_income_start' => $this->monthly_income_start,
            'monthly_income_end' => $this->monthly_income_end,
            'date_registered' => $this->date_registered,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at
                ? $this->updated_at->toDateTimeString()
                : $this->created_at->toDateTimeString()
        ];
    }

    public function toArrayDocumentOptions()
    {
        return [
            'slug' => $this->slug,
            'code' => $this->code,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'photo' => $this->photo
                ? env('CDN_URL', '') . '/storage/' . $this->photo
                : '',
            'date_of_birth' => $this->date_of_birth,
            'address' => $this->address,
            'place_of_birth' => $this->place_of_birth,
            'photo' => $this->photo
                ? env('CDN_URL', '') . '/storage/' . $this->photo
                : '',
            'gender' => [
                    'id' => $this->gender,
                    'name' => $this->genders[$this->gender]
            ],
            'civil_status' => $this->civil_status
                ? [
                    'id' => $this->id,
                    'name' => $this->civilStatuses[$this->civil_status]
                ]
                : null,
            'barangay' => $this->barangay,
            'city' => $this->city,
            'province' => $this->province,
            'left_thumbmark' => $this->left_thumbmark
                ? env('CDN_URL', '') . '/storage/' . $this->left_thumbmark
                : '',
            'right_thumbmark' => $this->right_thumbmark
                ? env('CDN_URL', '') . '/storage/' . $this->right_thumbmark
                : '',
        ];
    }

    public function toArrayDocumentsRelated()
    {
        return [
            'slug' => $this->slug,
            'code' => $this->code,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'photo' => $this->photo
                ? env('CDN_URL', '') . '/storage/' . $this->photo
                : '',
        ];
    }

    public function toArrayDocumentViewRelated()
    {
        return [
            'slug' => $this->slug,
            'code' => $this->code,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'photo' => $this->photo
                ? env('CDN_URL', '') . '/storage/' . $this->photo
                : '',
            'date_of_birth' => $this->date_of_birth,
            'address' => $this->address,
            'place_of_birth' => $this->place_of_birth,
            'gender' => [
                    'id' => $this->gender,
                    'name' => $this->genders[$this->gender]
            ],
            'civil_status' => $this->civil_status
                ? [
                    'id' => $this->id,
                    'name' => $this->civilStatuses[$this->civil_status]
                ]
                : null,
            'barangay' => $this->barangay,
            'city' => $this->city,
            'province' => $this->province,
            'left_thumbmark' => $this->left_thumbmark
                ? env('CDN_URL', '') . '/storage/' . $this->left_thumbmark
                : '',
            'right_thumbmark' => $this->right_thumbmark
                ? env('CDN_URL', '') . '/storage/' . $this->right_thumbmark
                : '',
        ];
    }

    public function toArrayPublicDocumentRelated()
    {
        return [
            'full_name' => $this->fullName(),
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
        ];
    }

    public function toArrayMemberRelativesRelated()
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
                    'name' => $this->genders[$this->gender]
            ],
        ];
    }

    public function toArrayMemberCheckingRelated()
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
                    'name' => $this->genders[$this->gender]
            ],
            'company' => $this->company->toArrayMemberCheckingRelated(),
        ];
    }
}
