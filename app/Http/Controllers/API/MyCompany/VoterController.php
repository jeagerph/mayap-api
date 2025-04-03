<?php

namespace App\Http\Controllers\API\MyCompany;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Slug;
use App\Models\Voter;
use App\Models\Company;
use App\Models\Province;
use App\Models\City;
use App\Models\Barangay;
use App\Models\Activity;

use App\Http\Repositories\MyCompany\VoterRepository as Repository;

use App\Http\Requests\MyCompany\Voter\CheckRequest;
use App\Http\Requests\MyCompany\Voter\StoreRequest;
use App\Http\Requests\MyCompany\Voter\UpdateRequest;

class VoterController extends Controller
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

        $request->merge([
            'voters-related' => true
        ]);

        $model = new Voter;

        return $model->where('company_id', $company->id)
            ->where(function ($q) use ($request) {
                if ($request->has('firstName') && $request->get('firstName')):
                    $q->where('first_name', 'LIKE', '%' . $request->get('firstName') . '%');
                endif;

                if ($request->has('middleName') && $request->get('middleName')):
                    $q->where('middle_name', 'LIKE', '%' . $request->get('middleName') . '%');
                endif;

                if ($request->has('lastName') && $request->get('lastName')):
                    $q->where('last_name', 'LIKE', '%' . $request->get('lastName') . '%');
                endif;

                if ($request->has('filter')):

                    if (isset($request->get('filter')['gender'])):
                        $q->where('gender', $request->get('filter')['gender']);
                    endif;

                    if (isset($request->get('filter')['provCode'])):
                        $q->where('province_id', $request->get('filter')['provCode']);
                    endif;

                    if (isset($request->get('filter')['cityCode'])):
                        $q->where('city_id', $request->get('filter')['cityCode']);
                    endif;

                    if (isset($request->get('filter')['barangay'])):
                        $q->where('barangay_id', $request->get('filter')['barangay']);
                    endif;

                    if (isset($request->get('filter')['age'])):
                        $arrAgeRange = explode(',', $request->get('filter')['age']);

                        $minDate = \Carbon\Carbon::today()->subYears($arrAgeRange[0])->format('Y');
                        $maxDate = \Carbon\Carbon::today()->subYears($arrAgeRange[1])->format('Y');

                        $q->whereBetween(\DB::raw('YEAR(date_of_birth)'), [$maxDate, $minDate]);

                    endif;
                endif;
            })
            ->where(function ($q) use ($request) {
                if ($request->has('range') && $request->get('range')['dateRegistered']):
                    $dates = explode(',', $request->get('range')['dateRegistered']);

                    $q->whereDate('date_registered', $dates[0])
                        ->orWhereDate('date_registered', $dates[1])
                        ->orWhereBetween('date_registered', [$dates[0], $dates[1]]);
                endif;
            })
            // ->orderBy('is_priority', 'desc')
            ->orderBy('last_name', 'asc')
            ->orderBy('first_name', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function check(Request $request, CheckRequest $formRequest)
    {
        $request->merge([
            'voter-checking-related' => true
        ]);

        return $this->repository->check($formRequest);
    }

    public function store(Request $request, StoreRequest $formRequest)
    {
        $request->merge([
            'voters-related' => true
        ]);

        return $this->repository->store($formRequest);
    }

    public function show(Request $request, $code)
    {
        $request->merge([
            'voters-related' => true
        ]);

        return $this->repository->show($request, $code);
    }

    public function edit(Request $request, $code)
    {
        $request->merge([
            'voters-related' => true
        ]);

        return $this->repository->edit($request, $code);
    }

    public function showProfile(Request $request, $code)
    {
        $request->merge([
            'voter-profile-related' => true
        ]);

        return $this->repository->showProfile($request, $code);
    }

    public function update(Request $request, UpdateRequest $formRequest, $code)
    {
        $request->merge([
            'voters-related' => true
        ]);

        return $this->repository->update($request, $code);
    }

    public function destroy(Request $request, $code)
    {
        $request->merge([
            'beneficiary-deletion' => true
        ]);

        return $this->repository->destroy($request, $code);
    }

    public function showActivities(Request $request, $code)
    {
        $module = Slug::findCodeOrDie($code);

        $filters = [
            'activityType' => 'App\\Models\\Voter',
            'activityId' => $module->slug_id
        ];

        $sorts = [
            'created' => 'desc'
        ];

        $request->merge([
            'voters-related' => true,
            'filter' => $this->handleQueries('filter', $request, $filters),
            'sort' => $this->handleQueries('sort', $request, $sorts),
        ]);

        $model = new Activity;

        return $model->build();
    }

    public function provincesOptions(Request $request)
    {
        $sorts = [
            'name' => 'asc'
        ];

        $request->merge([
            'voter-options' => true,
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
            'voter-options' => true,
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
            'voter-options' => true,
            'sort' => $this->handleQueries('sort', $request, $sorts),
            'all' => true
        ]);

        $model = new Barangay;

        return $model->build();
    }

    public function import(Request $request)
    {
        $request->merge([
            'voters-related' => true
        ]);

        return $this->repository->import($request);
    }

    public function updatePhoto(Request $request, $code)
    {
        $request->merge([
            'voters-related' => true
        ]);
        return $this->repository->updateConnectedBeneficiaryPhoto($request, $code);
    }
}
