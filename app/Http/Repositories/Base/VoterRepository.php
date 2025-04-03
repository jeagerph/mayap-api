<?php

namespace App\Http\Repositories\Base;

use Illuminate\Support\Facades\Auth;

use App\Models\Voter;

use App\Models\Slug;

use App\Http\Repositories\Base\SlugRepository;
use App\Http\Repositories\Base\CompanyRepository;
use Illuminate\Support\Facades\DB;

class VoterRepository
{   
    private $slugRepository;
    private $companyRepository;

    public function __construct()
    {
        $this->slugRepository = new SlugRepository;
        $this->companyRepository = new CompanyRepository;
    }

    public function store($data, $company)
    {   
        $model = new Voter;

        $model->code = self::generateCode($company);

        $model->company_id = $company->id;

        $model->date_registered = $data['date_registered'];

        $model->province_id = $data['province_id'];
        $model->city_id = $data['city_id'];
        $model->barangay_id = $data['barangay_id'];
        $model->house_no = $data['house_no'];

        $model->first_name = strtoupper($data['first_name']);
        $model->middle_name = $data['middle_name']
            ? strtoupper($data['middle_name'])
            : null;
        $model->last_name = strtoupper($data['last_name']);
        $model->gender = isset($data['gender'])
            ? $data['gender']
            : 1;
        $model->date_of_birth = isset($data['date_of_birth'])
            ? $data['date_of_birth']
            : null;

        $model->precinct_no = isset($data['precinct_no'])
            ? $data['precinct_no']
            : null;
        $model->application_no = isset($data['application_no'])
            ? $data['application_no']
            : null;
        $model->application_date = isset($data['application_date'])
            ? $data['application_date']
            : null;
        $model->application_type = isset($data['application_type'])
            ? $data['application_type']
            : null;

        $model->remarks = isset($data['remarks'])
            ? $data['remarks']
            : null;

        $model->created_by = Auth::id() ?: 1;
        $model->save();

        $model->slug()->save(
            $this->slugRepository->new(
                $model->first_name . ' ' . $model->last_name . ' Voter'
            )
        );

        return $model;
    }

    public function bulkInsert(array $dataList, $company)
    {
   
    
        DB::transaction(function() use ($dataList, $company){

            $records = [];
            $slugs = [];
            $timestamp = now();
            $createdBy = Auth::id() ?? 1;
            $lastVoter = Voter::where('company_id', $company->id)
            ->orderBy('id', 'desc')
            ->first();
            $nextNumber = $lastVoter ? ((int) str_replace('VOTER-' . $company->id . '-', '', $lastVoter->code) + 1) : 1;
        
            foreach ($dataList as $data) {
                $records[] = [
                    'code' => self::bulkGenerateCode($company,$nextNumber++),
                    'company_id' => $company->id,
                    'date_registered' => $data['date_registered'] ?? null,
                    'province_id' => $data['province_id'],
                    'city_id' => $data['city_id'],
                    'barangay_id' => $data['barangay_id'],
                    'house_no' => $data['house_no'] ?? null,
                    'first_name' => strtoupper($data['first_name']),
                    'middle_name' => isset($data['middle_name']) ? strtoupper($data['middle_name']) : null,
                    'last_name' => strtoupper($data['last_name']),
                    'gender' => $data['gender'] ?? 1,
                    'date_of_birth' => $data['date_of_birth'] ?? null,
                    'precinct_no' => $data['precint_no'] ?? null,
                    'application_no' => $data['application_no'] ?? null,
                    'application_date' => $data['application_date'] ?? null,
                    'application_type' => $data['application_type'] ?? null,
                    'remarks' => $data['remarks'] ?? null,
                    'created_by' => $createdBy,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ];
            }

            Voter::insert($records);
    
            // Retrieve inserted voter IDs
            $insertedVoters = Voter::latest()->limit(count($dataList))->get();
        
            foreach ($insertedVoters as $voter) {
                $slug = $this->slugRepository->new($voter->first_name . ' ' . $voter->last_name . ' Voter');
        
                // Add `slug_id` (linked voter ID) and `slug_type` (model name)
                $slugs[] = [
                    'full' => $slug->full,
                    'code' => $slug->code,
                    'name' => $slug->name,
                    'slug_id' => $voter->id,
                    'slug_type' => Voter::class,
                    'created_by' => $createdBy,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ];
            }
        
          
            Slug::insert($slugs);
            return $insertedVoters;
        });
    
     
    }
    

    public function update($data)
    {
        return [
            'province_id' => $data['province_id'],
            'city_id' => $data['city_id'],
            'barangay_id' => $data['barangay_id'],
            'house_no' => $data['house_no'],

            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'],
            'last_name' => $data['last_name'],
            'gender' => $data['gender'],
            'date_of_birth' => $data['date_of_birth'],

            'precinct_no' => $data['precinct_no'],
            'application_no' => $data['application_no'],
            'application_date' => $data['application_date'],
            'application_type' => $data['application_type'],
            'remarks' => $data['remarks'],

            'updated_by' => Auth::id()
        ];
    }

    public function updateAddress($voter)
    {
        $address = $voter->house_no ? $voter->house_no . ', ' : '';
        $address .= $voter->barangay ? $voter->barangay->name . ', ' : '';

        // Customization of address: Determined if Olongapo City beneficiary, not include province
        if($voter->city_id == '037107'):
            $address .= $voter->city_id ? $voter->city->name : '';
        else:
            $address .= $voter->city_id ? $voter->city->name . ', ' : '';
            $address .= $voter->province_id ? $voter->province->name : '';
        endif;

        $voter->update([
            'address' => strtoupper($address),
            'updated_by' => Auth::id() ?: 1
        ]);
    }

    public function generateCode($company)
    {
        $count = Voter::where('company_id', $company->id)->count();

        $code = 'VOTER-';
        $code .= $company->id. '-';
        $code .= leadingZeros($count+1);

        return $code;
    }
    public function bulkGenerateCode($company, $startNumber)
    {
        return 'VOTER-' . $company->id . '-' . str_pad($startNumber, 6, '0', STR_PAD_LEFT);
    }


    public function isAllowedToUpdate($voter)
    {
        $company = Auth::user()->company();

        if($voter->company_id != $company->id):

            return abort(403, 'Forbidden: Voter is not under your account.');

        endif;
    }

    public function isAllowedToDestroy($voter)
    {
        
    }
}
?>