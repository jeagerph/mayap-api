<?php

namespace App\Http\Repositories\MyCompany;

use Illuminate\Support\Facades\Auth;

use App\Models\BeneficiaryIncentive;

use App\Http\Repositories\Base\BeneficiaryIncentiveRepository as BaseRepository;
use App\Http\Repositories\Base\CompanyRepository;
use App\Http\Repositories\Base\BeneficiaryRepository;

class IncentiveRepository
{
    public function __construct()
    {
        $this->baseRepository = new BaseRepository;
        $this->companyRepository = new CompanyRepository;
        $this->beneficiaryRepository = new BeneficiaryRepository;
    }

    public function destroy($request, $id)
    {
        $company = Auth::user()->company();

        $incentive = $this->companyRepository->isIncentiveRelated($company, $id);

        $incentive->delete();

        $this->beneficiaryRepository->refreshIncentives($incentive->beneficiary);

        return response([
            'message' => 'Data deleted successfully.'
        ], 200);
    }
}
?>