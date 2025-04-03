<?php

namespace App\Http\Repositories\Base;

use Illuminate\Support\Facades\Auth;

use App\Models\Slug;
use App\Models\Beneficiary;
use App\Models\BeneficiaryNetwork;

use App\Http\Repositories\Base\BeneficiaryIncentiveRepository;
use App\Http\Repositories\Base\BeneficiaryRepository;

class BeneficiaryNetworkRepository
{
    public function __construct()
    {
        $this->incentiveRepository = new BeneficiaryIncentiveRepository;
        $this->beneficiaryRepository = new BeneficiaryRepository;
    }
    
    public function addToNetwork($currentBeneficiary, $targetBeneficiary, $company)
    {
        // Checking of target beneficiary if already under network
        $checkingTargetNetwork = $targetBeneficiary->network;

        if ($checkingTargetNetwork) return abort(403, 'Beneficiary is already part of a network.');

        // Get count of current beneficiary's parenting networks
        $orderNo = 1;
        $lastUnderNetwork = $currentBeneficiary->parentingNetworks()->orderBy('order_no', 'desc')->first();

        if ($lastUnderNetwork):
            $orderNo = $lastUnderNetwork->order_no + 1;
        endif;

        return $currentBeneficiary->parentingNetworks()->save(
            new BeneficiaryNetwork([
                'company_id' => $company->id,
                'order_no' => $orderNo,
                'beneficiary_id' => $targetBeneficiary->id,
                'created_by' => Auth::id() ?: 1
            ])
        );
    }

    public function addIncentiveToParentNetworks($currentTargetBeneficiary, $company)
    {
        $networkSetting = $company->networkSetting;

        $targetBeneficiary = Beneficiary::find($currentTargetBeneficiary->id);

        // Get parent first level parent
        $targetNetwork = $targetBeneficiary->network;

        if ($targetNetwork):

            $masterDegreeParent = $targetNetwork->parentBeneficiary;

            if ($masterDegreeParent):

                if ($networkSetting->master_degree_enabled):

                    // ADD INCENTIVES TO MASTER DEGREE PARENT
                    $masterDegreeParent->incentives()->save(
                        $this->incentiveRepository->new([
                            'incentive_date' => now()->format('Y-m-d'),
                            'points' => $networkSetting->master_degree_points,
                            'mode' => 1,
                            'remarks' => 'ADDED BENEFICIARY: ' . $targetBeneficiary->fullName() . ' ( MASTER DEGREE )',
                        ], $company)
                    );

                    $this->beneficiaryRepository->refreshIncentives($masterDegreeParent);

                endif;

                // 1ST DEGREE NETWORK

                $masterDegreeNetwork = $masterDegreeParent->network;

                if ($masterDegreeNetwork):

                    $firstDegreeParent = $masterDegreeNetwork->parentBeneficiary;

                    if ($firstDegreeParent):

                        if ($networkSetting->first_degree_enabled):

                            // ADD INCENTIVES TO FIRST DEGREE PARENT
                            $firstDegreeParent->incentives()->save(
                                $this->incentiveRepository->new([
                                    'incentive_date' => now()->format('Y-m-d'),
                                    'points' => $networkSetting->first_degree_points,
                                    'mode' => 1,
                                    'remarks' => 'ADDED BENEFICIARY: ' . $targetBeneficiary->fullName() . '( 1ST DEGREE )',
                                ], $company)
                            );

                            $this->beneficiaryRepository->refreshIncentives($firstDegreeParent);
        
                        endif;

                        // 2ND DEGREE NETWORK

                        $secondDegreeNetwork = $firstDegreeParent->network;

                        if ($secondDegreeNetwork):

                            $secondDegreeParent = $secondDegreeNetwork->parentBeneficiary;

                            if ($secondDegreeParent):

                                if ($networkSetting->second_degree_enabled):

                                    // ADD INCENTIVES TO SECOND DEGREE PARENT
                                    $secondDegreeParent->incentives()->save(
                                        $this->incentiveRepository->new([
                                            'incentive_date' => now()->format('Y-m-d'),
                                            'points' => $networkSetting->second_degree_points,
                                            'mode' => 1,
                                            'remarks' => 'ADDED BENEFICIARY: ' . $targetBeneficiary->fullName() . '( 2ND DEGREE )',
                                        ], $company)
                                    );

                                    $this->beneficiaryRepository->refreshIncentives($secondDegreeParent);
                
                                endif;

                                // 3RD DEGREE NETWORK

                                $thirdDegreeNetwork = $secondDegreeParent->network;

                                if ($thirdDegreeNetwork):

                                    $thirdDegreeParent = $thirdDegreeNetwork->parentBeneficiary;

                                    if ($thirdDegreeParent):

                                        if ($networkSetting->third_degree_enabled):

                                            // ADD INCENTIVES TO THIRD DEGREE PARENT
                                            $thirdDegreeParent->incentives()->save(
                                                $this->incentiveRepository->new([
                                                    'incentive_date' => now()->format('Y-m-d'),
                                                    'points' => $networkSetting->third_degree_points,
                                                    'mode' => 1,
                                                    'remarks' => 'ADDED BENEFICIARY: ' . $targetBeneficiary->fullName() . '( 3RD DEGREE )',
                                                ], $company)
                                            );

                                            $this->beneficiaryRepository->refreshIncentives($thirdDegreeParent);
                        
                                        endif;

                                        // 4TH DEGREE NETWORK
                                        
                                        $fourthDegreeNetwork = $thirdDegreeParent->network;
        
                                        if ($fourthDegreeNetwork):
    
                                            $fourthDegreeParent = $fourthDegreeNetwork->parentBeneficiary;
    
                                            if ($fourthDegreeParent):
    
                                                if ($networkSetting->fourth_degree_enabled):

                                                    // ADD INCENTIVES TO FOURTH DEGREE PARENT
                                                    $fourthDegreeParent->incentives()->save(
                                                        $this->incentiveRepository->new([
                                                            'incentive_date' => now()->format('Y-m-d'),
                                                            'points' => $networkSetting->fourth_degree_points,
                                                            'mode' => 1,
                                                            'remarks' => 'ADDED BENEFICIARY: ' . $targetBeneficiary->fullName() . '( 4TH DEGREE )',
                                                        ], $company)
                                                    );

                                                    $this->beneficiaryRepository->refreshIncentives($fourthDegreeParent);
                                
                                                endif;

                                                // 5TH DEGREE NETWORK
    
                                                $fifthDegreeNetwork = $fourthDegreeParent->network;
                
                                                if ($fifthDegreeNetwork):
            
                                                    $fifthDegreeParent = $fifthDegreeNetwork->parentBeneficiary;
            
                                                    if ($fifthDegreeParent):
            
                                                        if ($networkSetting->fifth_degree_enabled):

                                                            // ADD INCENTIVES TO FIFTH DEGREE PARENT
                                                            $fifthDegreeParent->incentives()->save(
                                                                $this->incentiveRepository->new([
                                                                    'incentive_date' => now()->format('Y-m-d'),
                                                                    'points' => $networkSetting->fifth_degree_points,
                                                                    'mode' => 1,
                                                                    'remarks' => 'ADDED BENEFICIARY: ' . $targetBeneficiary->fullName() . '( 5TH DEGREE )',
                                                                ], $company)
                                                            );

                                                            $this->beneficiaryRepository->refreshIncentives($fifthDegreeParent);
                                        
                                                        endif;
                                                    endif;
                                                endif;
                                            endif;
                                        endif;
                                    endif;
                                endif;
                            endif;
                        endif;
                    endif;
                endif;
            endif;
        endif;
    }

    public function deductIncentiveToParentNetworks($currentTargetBeneficiary, $company)
    {
        $networkSetting = $company->networkSetting;

        $targetBeneficiary = Beneficiary::find($currentTargetBeneficiary->id);

        // Get parent first level parent
        $targetNetwork = $targetBeneficiary->network;

        if ($targetNetwork):

            $masterDegreeParent = $targetNetwork->parentBeneficiary;

            if ($masterDegreeParent):

                if ($networkSetting->master_degree_enabled):

                    // DEDUCT INCENTIVES TO MASTER DEGREE PARENT
                    $masterDegreeParent->incentives()->save(
                        $this->incentiveRepository->new([
                            'incentive_date' => now()->format('Y-m-d'),
                            'points' => $networkSetting->master_degree_points,
                            'mode' => 2,
                            'remarks' => 'REMOVED BENEFICIARY: ' . $targetBeneficiary->fullName() . ' ( MASTER DEGREE )',
                        ], $company)
                    );

                    $this->beneficiaryRepository->refreshIncentives($masterDegreeParent);

                endif;

                // 1ST DEGREE NETWORK

                $masterDegreeNetwork = $masterDegreeParent->network;

                if ($masterDegreeNetwork):

                    $firstDegreeParent = $masterDegreeNetwork->parentBeneficiary;

                    if ($firstDegreeParent):

                        if ($networkSetting->first_degree_enabled):

                            // ADD INCENTIVES TO FIRST DEGREE PARENT
                            $firstDegreeParent->incentives()->save(
                                $this->incentiveRepository->new([
                                    'incentive_date' => now()->format('Y-m-d'),
                                    'points' => $networkSetting->first_degree_points,
                                    'mode' => 2,
                                    'remarks' => 'REMOVED BENEFICIARY: ' . $targetBeneficiary->fullName() . '( 1ST DEGREE )',
                                ], $company)
                            );

                            $this->beneficiaryRepository->refreshIncentives($firstDegreeParent);
        
                        endif;

                        // 2ND DEGREE NETWORK

                        $secondDegreeNetwork = $firstDegreeParent->network;

                        if ($secondDegreeNetwork):

                            $secondDegreeParent = $secondDegreeNetwork->parentBeneficiary;

                            if ($secondDegreeParent):

                                if ($networkSetting->second_degree_enabled):

                                    // DEDUCT INCENTIVES TO SECOND DEGREE PARENT
                                    $secondDegreeParent->incentives()->save(
                                        $this->incentiveRepository->new([
                                            'incentive_date' => now()->format('Y-m-d'),
                                            'points' => $networkSetting->second_degree_points,
                                            'mode' => 2,
                                            'remarks' => 'REMOVED BENEFICIARY: ' . $targetBeneficiary->fullName() . '( 2ND DEGREE )',
                                        ], $company)
                                    );

                                    $this->beneficiaryRepository->refreshIncentives($secondDegreeParent);
                
                                endif;

                                // 3RD DEGREE NETWORK

                                $thirdDegreeNetwork = $secondDegreeParent->network;

                                if ($thirdDegreeNetwork):

                                    $thirdDegreeParent = $thirdDegreeNetwork->parentBeneficiary;

                                    if ($thirdDegreeParent):

                                        if ($networkSetting->third_degree_enabled):

                                            // ADD INCENTIVES TO THIRD DEGREE PARENT
                                            $thirdDegreeParent->incentives()->save(
                                                $this->incentiveRepository->new([
                                                    'incentive_date' => now()->format('Y-m-d'),
                                                    'points' => $networkSetting->third_degree_points,
                                                    'mode' => 2,
                                                    'remarks' => 'REMOVED BENEFICIARY: ' . $targetBeneficiary->fullName() . '( 3RD DEGREE )',
                                                ], $company)
                                            );

                                            $this->beneficiaryRepository->refreshIncentives($thirdDegreeParent);
                        
                                        endif;

                                        // 4TH DEGREE NETWORK
                                        
                                        $fourthDegreeNetwork = $thirdDegreeParent->network;
        
                                        if ($fourthDegreeNetwork):
    
                                            $fourthDegreeParent = $fourthDegreeNetwork->parentBeneficiary;
    
                                            if ($fourthDegreeParent):
    
                                                if ($networkSetting->fourth_degree_enabled):

                                                    // ADD INCENTIVES TO FOURTH DEGREE PARENT
                                                    $fourthDegreeParent->incentives()->save(
                                                        $this->incentiveRepository->new([
                                                            'incentive_date' => now()->format('Y-m-d'),
                                                            'points' => $networkSetting->fourth_degree_points,
                                                            'mode' => 2,
                                                            'remarks' => 'REMOVED BENEFICIARY: ' . $targetBeneficiary->fullName() . '( 4TH DEGREE )',
                                                        ], $company)
                                                    );

                                                    $this->beneficiaryRepository->refreshIncentives($fourthDegreeParent);
                                
                                                endif;

                                                // 5TH DEGREE NETWORK
    
                                                $fifthDegreeNetwork = $fourthDegreeParent->network;
                
                                                if ($fifthDegreeNetwork):
            
                                                    $fifthDegreeParent = $fifthDegreeNetwork->parentBeneficiary;
            
                                                    if ($fifthDegreeParent):
            
                                                        if ($networkSetting->fifth_degree_enabled):

                                                            // DEDUCT INCENTIVES TO FIFTH DEGREE PARENT
                                                            $fifthDegreeParent->incentives()->save(
                                                                $this->incentiveRepository->new([
                                                                    'incentive_date' => now()->format('Y-m-d'),
                                                                    'points' => $networkSetting->fifth_degree_points,
                                                                    'mode' => 2,
                                                                    'remarks' => 'REMOVED BENEFICIARY: ' . $targetBeneficiary->fullName() . '( 5TH DEGREE )',
                                                                ], $company)
                                                            );

                                                            $this->beneficiaryRepository->refreshIncentives($fifthDegreeParent);
                                        
                                                        endif;
                                                    endif;
                                                endif;
                                            endif;
                                        endif;
                                    endif;
                                endif;
                            endif;
                        endif;
                    endif;
                endif;
            endif;
        endif;
    }
}
?>