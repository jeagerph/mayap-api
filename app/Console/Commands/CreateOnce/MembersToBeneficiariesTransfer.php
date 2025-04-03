<?php

namespace App\Console\Commands\CreateOnce;

use Illuminate\Console\Command;

class MembersToBeneficiariesTransfer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create-once:members-to-beneficiaries-transfer {companyId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $companyId = $this->argument('companyId');

        $count = 0;

        $company = \App\Models\Company::where('id', $companyId)->first();

        if (!$company) return $this->info('Company does not exists.');

        $beneficiaryRepository = new \App\Http\Repositories\Base\BeneficiaryRepository;

        $members = \App\Models\Member::orderBy('date_registered', 'asc')->orderBy('created_at', 'asc')->get();

        foreach($members as $member):

            $beneficiary = \App\Models\Beneficiary::where('barangay_id', $member->barangay_id)->where('first_name', $member->first_name)->where('last_name', $member->last_name)->where('date_of_birth', $member->date_of_birth)->first();

            if (!$beneficiary):
                $newBeneficiary = $beneficiaryRepository->store([
                    'date_registered' => $member->date_registered,
    
                    'province_id' => $member->province_id,
                    'city_id' => $member->city_id,
                    'barangay_id' => $member->barangay_id,
                    'house_no' => $member->house_no,
    
                    'first_name' => $member->first_name,
                    'middle_name' => $member->middle_name,
                    'last_name' => $member->last_name,
                    'gender' => $member->gender,
                    'mobile_no' => $member->contact_no,
                    'email' => $member->email,
    
                    'place_of_birth' => $member->place_of_birth,
                    'date_of_birth' => $member->date_of_birth,
                    'civil_status' => $member->civil_status
                        ? $member->civilStatuses[$member->civil_status]
                        : null,
                    'citizenship' => $member->citizenship,
                    'religion' => $member->religion,
    
                    'educational_attainment' => $member->educational_attainment,
                    'occupation' => $member->occupation,
                    'monthly_income' => $member->monthly_income,
                    'source_of_income' => null,
                    'classification' => $member->classification->name,
    
                    'is_household' => $member->is_household,
                    'household_count' => 0,
                    'is_priority' => 0,

                    'health_issues' => $member->health_history,
                    'problem_presented' => null,
                    'findings' => null,
                    'assessment_recommendation' => $member->pending,
                    'needs' => null,
                    'remarks' => $member->remarks,

                    'questionnaires' => [],
    
                    'emergency_contact_name' => $member->emergency_contact_name,
                    'emergency_contact_address' => $member->emergency_contact_address,
                    'emergency_contact_no' => $member->emergency_contact_no,
    
                    
                ], $company);
    
                if ($member->photo):
                    $newBeneficiary->update([
                        'photo' => $member->photo,
                        'updated_by' => 1
                    ]);
                endif;
                
                $beneficiaryRepository->updateAddress($newBeneficiary);
    
                $this->info($newBeneficiary->fullName() . ' has been transferred.');
    
                $count++;

            else:

                $beneficiary->update([
                    'health_issues' => $member->health_history,
                    'assessment_recommendation' => $member->pending,
                    'updated_by' => 1
                ]);
            endif;

        endforeach;

        $this->info($count . ' members have been transferred.');
    }
}
