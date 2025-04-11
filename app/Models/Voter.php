<?php

namespace App\Models;

use App\Models\Model;

use App\Observers\BeneficiaryObserver as Observer;

class Voter extends Model
{
    use Observer;

    public $searchFields = [
        'firstName' => ':first_name',
        'middleName' => ':middle_name',
        'lastName' => ':last_name',
    ];

    public $filterFields = [
        'companyCode' => 'slug:company_id',
        'provCode' => ':province_id',
        'cityCode' => ':city_id',
        'barangay' => ':barangay_id',
    ];

    public $sortFields = [
        'created' => ':created_at',
        'dateRegistered' => ':date_registered',
    ];

    public $genderOptions = [
        0 => 'NOT INDICATED',
        1 => 'MALE',
        2 => 'FEMALE'
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

    public function toArray()
    {
        $beneficiary = Beneficiary::where('first_name', $this->first_name)
            ->where('middle_name', $this->middle_name)
            ->where('last_name', $this->last_name)
            ->where('verify_voter', 2)
            ->first();

        $arr = [
            'slug' => $this->slug,
            'code' => $this->code,
            'full_name' => $this->fullName(),
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'photo' => null,
            'beneficiary' => null,
            'precint_no' => $this->precinct_no,
            'date_registered' => $this->date_registered,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at
                ? $this->updated_at->toDateTimeString()
                : $this->created_at->toDateTimeString()
        ];

        // // If a beneficiary is found, add their photo
        if (!empty($beneficiary)) {
            $arr['photo'] = $beneficiary->photo ? env('CDN_URL', '') . '/storage/' . $beneficiary->photo : null;
            $arr['beneficiary'] = [
                'slug' => $beneficiary->slug,
                'code' => $beneficiary->code,
            ];
        }


        if (request()->has('voters-related')):

            $arr['barangay'] = $this->barangay_id
                ? $this->barangay
                : null;
            $arr['city'] = $this->city_id
                ? $this->city
                : null;
            $arr['province'] = $this->province_id
                ? $this->province
                : null;
            $arr['date_of_birth'] = $this->date_of_birth;
            $arr['gender'] = [
                'id' => $this->gender,
                'name' => $this->genderOptions[$this->gender]
            ];
            $arr['precinct_no'] = $this->precinct_no;
            $arr['application_no'] = $this->application_no;
            $arr['application_date'] = $this->application_date;
            $arr['application_type'] = $this->application_type;
        endif;

        if (request()->has('beneficiary-checking-voters-related')):
            $arr['date_of_birth'] = $this->date_of_birth;
            $arr['gender'] = [
                'id' => $this->gender,
                'name' => $this->genderOptions[$this->gender]
            ];

            $arr['precinct_no'] = $this->precinct_no;
            $arr['application_no'] = $this->application_no;
            $arr['application_date'] = $this->application_date;
            $arr['application_type'] = $this->application_type;
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

            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'gender' => $this->gender ?: 1,
            'date_of_birth' => $this->date_of_birth,

            'precinct_no' => $this->precinct_no,
            'application_no' => $this->application_no,
            'application_date' => $this->application_date,
            'application_type' => $this->application_type,

            'remarks' => $this->remarks,
        ];
    }

    public function toArrayVotersRelated()
    {
        $beneficiary = Beneficiary::where('first_name', $this->first_name)
            ->where('middle_name', $this->middle_name)
            ->where('last_name', $this->last_name)
            ->where('verify_voter', 2)
            ->first();

        $arr = [
            'slug' => $this->slug,
            'code' => $this->code,
            'province' => $this->province_id
                ? $this->province
                : null,
            'city' => $this->city_id
                ? $this->city
                : null,
            'barangay' => $this->barangay_id
                ? $this->barangay
                : null,
            'house_no' => $this->house_no,
            'address' => $this->address,

            'full_name' => $this->fullName(),
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'photo' => null,
            'beneficiary' => null,
            'gender' => [
                'id' => $this->gender,
                'name' => $this->genderOptions[$this->gender]
            ],
            'date_of_birth' => $this->date_of_birth,

            'precinct_no' => $this->precinct_no,
            'application_no' => $this->application_no,
            'application_date' => $this->application_date,
            'application_type' => $this->application_type,

            'remarks' => $this->remarks,

            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at
                ? $this->updated_at->toDateTimeString()
                : $this->created_at->toDateTimeString(),
        ];

        // // If a beneficiary is found, add their photo
        if (!empty($beneficiary)) {
            $arr['photo'] = $beneficiary->photo ? env('CDN_URL', '') . '/storage/' . $beneficiary->photo : null;
            $arr['beneficiary'] = [
                'slug' => $beneficiary->slug,
                'code' => $beneficiary->code,
            ];
        }

        return $arr;
    }

    public function toArrayVoterCheckingRelated()
    {
        return [
            'slug' => $this->slug,
            'province' => $this->province_id
                ? $this->province
                : null,
            'city' => $this->city_id
                ? $this->city
                : null,
            'barangay' => $this->barangay_id
                ? $this->barangay_id
                : null,
            'house_no' => $this->house_no,
            'address' => $this->address,
            'full_name' => $this->fullName(),
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'date_of_birth' => $this->date_of_birth,
            'gender' => [
                'id' => $this->gender,
                'name' => $this->genderOptions[$this->gender]
            ],
        ];
    }
}
