<?php

namespace App\Http\Controllers\API\MyCompany;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\BeneficiaryPatient;
use App\Models\Beneficiary;
use App\Models\Province;
use App\Models\City;
use App\Models\Barangay;

use App\Http\Repositories\MyCompany\PatientRepository as Repository;

use App\Http\Requests\MyCompany\Patient\StoreRequest;
use App\Http\Requests\MyCompany\Patient\UpdateRequest;
use App\Http\Requests\MyCompany\Patient\StoreBeneficiaryOptionRequest;

class PatientController extends Controller
{
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
            'patientDate' => 'desc',
            'created' => 'desc',
        ];

        $request->merge([
            'patients-related' => true,
            'filter' => $this->handleQueries('filter', $request, $filters),
            'sort' => $this->handleQueries('sort', $request, $sorts),
        ]);

        $model = new BeneficiaryPatient;

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

            if($request->has('filter')):
                
                if(isset($request->get('filter')['status'])):
                    $q->where('status', $request->get('filter')['status']);
                endif;
            endif;
        })
        ->where(function($q) use ($request)
        {
            if($request->has('range') && $request->get('range')['patientDate']):
                $dates = explode(',', $request->get('range')['patientDate']);

                $q->whereDate('patient_date', $dates[0])
                    ->orWhereDate('patient_date', $dates[1])
                    ->orWhereBetween('patient_date', [$dates[0], $dates[1]]);
            endif;
        })
        ->orderBy('patient_date', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(10);
    }

    public function downloadReport(Request $request)
    {
        $request->merge([
            'patients-related' => true,
        ]);

        return $this->repository->downloadReport($request);
    }

    public function downloadByBarangayReport(Request $request)
    {
        $request->merge([
            'patients-related' => true,
        ]);

        return $this->repository->downloadByBarangayReport($request);
    }

    public function downloadByPurokReport(Request $request)
    {
        $request->merge([
            'patients-related' => true,
        ]);

        return $this->repository->downloadByPurokReport($request);
    }

    public function store(Request $request, StoreRequest $formRequest)
    {
        $request->merge([
            'patients-related' => true
        ]);

        return $this->repository->store($formRequest);
    }

    public function update(Request $request, UpdateRequest $formRequest, $id)
    {
        $request->merge([
            'patients-related' => true
        ]);

        return $this->repository->update($formRequest, $id);
    }

    public function approve(Request $request, $id)
    {
        $request->merge([
            'patients-related' => true,
        ]);

        return $this->repository->updateStatus($request, $id, 1);
    }

    public function inProgress(Request $request, $id)
    {
        $request->merge([
            'patients-related' => true,
        ]);

        return $this->repository->updateStatus($request, $id, 2);
    }

    public function complete(Request $request, $id)
    {
        $request->merge([
            'patients-related' => true,
        ]);

        return $this->repository->updateStatus($request, $id, 3);
    }

    public function cancel(Request $request, $id)
    {
        $request->merge([
            'patients-related' => true,
        ]);

        return $this->repository->updateStatus($request, $id, 4);
    }

    public function destroy(Request $request, $id)
    {
        $request->merge([
            'patient-deletion' => true
        ]);

        return $this->repository->destroy($request, $id);
    }

    public function beneficiaryOptions(Request $request)
    {
        $request->merge([
            'patient-options' => true,
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
            'patient-options' => true,
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
            'patient-options' => true,
            'sort' => $this->handleQueries('sort', $request, $sorts),
            'all' => true
        ]);

        $model = new Barangay;

        return $model->build();
    }

    public function storeBeneficiaryOption(Request $request, StoreBeneficiaryOptionRequest $formRequest)
    {
        $request->merge([
            'patient-options' => true,
        ]);

        return $this->repository->storeBeneficiaryOption($formRequest);
    }
}
