<?php

namespace App\Http\Controllers\API\MyCompany;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\BeneficiaryAssistance;
use App\Models\Beneficiary;
use App\Models\Province;
use App\Models\City;
use App\Models\Barangay;

use App\Http\Repositories\MyCompany\AssistanceRepository as Repository;

use App\Http\Requests\MyCompany\Assistance\StoreRequest;
use App\Http\Requests\MyCompany\Assistance\UpdateRequest;
use App\Http\Requests\MyCompany\Assistance\StoreBeneficiaryOptionRequest;

class AssistanceController extends Controller
{
    private $repository;
    public function __construct()
    {
        // $this->middleware('auth.permission');

        $this->repository = new Repository;
    }

    public function index(Request $request)
    {
        $company = Auth::user()->company();

        $filters = [
            'companyCode' => $company->slug->code
        ];

        $sorts = [
            'assistanceDate' => 'desc',
            'created' => 'desc',
        ];

        $request->merge([
            'assistances-related' => true,
            'filter' => $this->handleQueries('filter', $request, $filters),
            'sort' => $this->handleQueries('sort', $request, $sorts),
        ]);

        $model = new BeneficiaryAssistance;

        return $model->build();
    }


    public function liveSearch(Request $request)
    {
        $company = Auth::user()->company();

        $filters = [
            'companyCode' => $company->slug->code
        ];

        $sorts = [
            'assistanceDate' => 'desc',
            'created' => 'desc',
        ];

        $request->merge([
            'assistances-related' => true,
            'filter' => $this->handleQueries('filter', $request, $filters),
            'sort' => $this->handleQueries('sort', $request, $sorts),
        ]);

        $model = new BeneficiaryAssistance;

        return $model->build();
    }

    public function downloadReport(Request $request)
    {
        $request->merge([
            'assistances-related' => true,
        ]);

        return $this->repository->downloadReport($request);
    }

    public function downloadByBarangayReport(Request $request)
    {
        $request->merge([
            'assistances-related' => true,
        ]);

        return $this->repository->downloadByBarangayReport($request);
    }

    public function downloadByPurokReport(Request $request)
    {
        $request->merge([
            'assistances-related' => true,
        ]);

        return $this->repository->downloadByPurokReport($request);
    }

    public function downloadByFromReport(Request $request)
    {
        $request->merge([
            'assistances-related' => true,
        ]);

        return $this->repository->downloadByFromReport($request);
    }

    public function store(Request $request, StoreRequest $formRequest)
    {
        $request->merge([
            'assistances-related' => true
        ]);

        return $this->repository->store($formRequest);
    }

    public function update(Request $request, UpdateRequest $formRequest, $id)
    {
        $request->merge([
            'assistances-related' => true
        ]);

        return $this->repository->update($formRequest, $id);
    }

    public function destroy(Request $request, $id)
    {
        $request->merge([
            'assistance-deletion' => true
        ]);

        return $this->repository->destroy($request, $id);
    }
    

    public function showOtherAssistances(Request $request, $id)
    {
        $request->merge([
            'assistances-related' => true,
        ]);

        return $this->repository->showOtherAssistances($request, $id);
    }

    public function showAssistancesLocationsList(Request $request)
    {   
        $request->merge([
            'assistances-related' => true
        ]);

        return $this->repository->showAssistancesLocationsList($request);
    }

    public function showAssistancesByBarangayList(Request $request)
    {   
        $request->merge([
            'assistances-related' => true
        ]);

        return $this->repository->showAssistancesByBarangayList($request);
    }

    public function beneficiaryOptions(Request $request)
    {
        $request->merge([
            'assistance-options' => true,
        ]);

        $company = Auth::user()->company();

        $model = new Beneficiary;

        return $model->where('company_id', $company->id)
                    ->where(function($q) use ($request)
                    {
                        if($request->has('firstName') && $request->get('firstName')):
                            $q->where('first_name', 'LIKE', '%'.$request->get('firstName').'%');
                        endif;

                        if($request->has('middleName') && $request->get('middleName')):
                            $q->where('middle_name', 'LIKE', '%'.$request->get('middleName').'%');
                        endif;

                        if($request->has('lastName') && $request->get('lastName')):
                            $q->where('last_name', 'LIKE', '%'.$request->get('lastName').'%');
                        endif;
                    })
                    ->orderBy('last_name', 'asc')
                    ->get();
    }

    public function provincesOptions(Request $request)
    {
        $sorts = [
            'name' => 'asc'
        ];

        $request->merge([
            'patient-options' => true,
            'sort' => $this->handleQueries('sort', $request, $sorts),
            'all' => true
        ]);

        $model = new Province;

        return $model->build();
    }

    public function citiesOptions(Request $request)
    {
        $sorts = [
            'name' => 'asc'
        ];

        $request->merge([
            'assistance-options' => true,
            'sort' => $this->handleQueries('sort', $request, $sorts),
            'all' => true
        ]);

        $model = new City;

        return $model->build();
    }

    public function barangaysOptions(Request $request)
    {
        $sorts = [
            'name' => 'asc'
        ];

        $request->merge([
            'assistance-options' => true,
            'sort' => $this->handleQueries('sort', $request, $sorts),
            'all' => true
        ]);

        $model = new Barangay;

        return $model->build();
    }

    public function storeBeneficiaryOption(Request $request, StoreBeneficiaryOptionRequest $formRequest)
    {
        $request->merge([
            'assistance-options' => true,
        ]);

        return $this->repository->storeBeneficiaryOption($formRequest);
    }
}
