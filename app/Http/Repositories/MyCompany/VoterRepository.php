<?php

namespace App\Http\Repositories\MyCompany;

use App\Models\Slug;
use App\Models\Voter;

use App\Models\Company;
use App\Models\Barangay;
use App\Models\Beneficiary;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\Auth;
use App\Http\Repositories\Base\SlugRepository;
use App\Http\Repositories\Base\CompanyRepository;
use App\Http\Repositories\Base\VoterRepository as BaseRepository;
use App\Http\Repositories\Base\BeneficiaryRepository as BeneficiaryBaseRepository;

class VoterRepository
{
    private $baseRepository;
    private $slugRepository;
    private $companyRepository;

    private $beneficiaryBaseRepository;

    public function __construct()
    {
        $this->baseRepository = new BaseRepository;
        $this->slugRepository = new SlugRepository;
        $this->companyRepository = new CompanyRepository;
        $this->beneficiaryBaseRepository = new BeneficiaryBaseRepository;
    }
    public function store($request)
    {
        $company = Auth::user()->company();

        $newVoter = $this->baseRepository->store($request, $company);

        $this->baseRepository->updateAddress($newVoter);

        return $newVoter;
    }

    public function check($request)
    {
        $company = Auth::user()->company();

        $model = new Voter;

        $voter = $model->where('company_id', $company->id)
            ->where('first_name', $request->input('first_name'))
            ->where('last_name', $request->input('last_name'))
            // ->where('date_of_birth', $request->input('date_of_birth'))
            ->first();

        if ($voter)
            return $voter->toArrayVoterCheckingRelated();

        return $voter;
    }

    public function show($request, $code)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $voter = $model->slug;

        $this->companyRepository->isVoterRelated($company, $voter->id);

        return $voter->toArrayVotersRelated();
    }

    public function edit($request, $code)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $voter = $model->slug;

        $this->companyRepository->isVoterRelated($company, $voter->id);

        return $voter->toArrayEdit();
    }

    public function update($request, $code)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $voter = $model->slug;

        $this->companyRepository->isVoterRelated($company, $voter->id);

        $voter->update(
            $this->baseRepository->update($request)
        );

        $this->baseRepository->updateAddress($voter);

        return (Voter::find($voter->id))->toArrayVotersRelated();
    }

    public function showProfile($request, $code)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $voter = $model->slug;

        $this->companyRepository->isVoterRelated($company, $voter->id);

        return $voter->toArrayVotersRelated();
    }

    public function destroy($request, $code)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $voter = $model->slug;

        $this->companyRepository->isVoterRelated($company, $voter->id);

        $this->baseRepository->isAllowedToDestroy($voter);

        $voter->delete();

        return response([
            'message' => 'Data deleted successfully.'
        ], 200);
    }


    public function import($request)
    {
        \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\VotersImport, $request->file('file'));

        return response([
            'message' => 'Data has been imported successfully.'
        ], 200);
    }

    public function updateConnectedBeneficiaryPhoto($request, $code)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $beneficiary = $model->slug;

        $this->companyRepository->isBeneficiaryRelated($company, $beneficiary->id);

        if ($request->has('photo') && $request->input('photo')):
            $beneficiary->update(
                $this->beneficiaryBaseRepository->uploadPhoto(
                    $beneficiary->photo,
                    [
                        'photo' => $request->input('photo')
                    ]
                )
            );
        endif;

        //     $this->companyRepository->checkAndCreateClassification(
        //         $request->input('classification'),
        //         $company
        //    );

        return (Beneficiary::find($beneficiary->id))->toArrayBeneficiariesRelated();
    }
}
?>