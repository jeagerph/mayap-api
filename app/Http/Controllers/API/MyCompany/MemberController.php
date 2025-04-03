<?php

namespace App\Http\Controllers\API\MyCompany;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Slug;
use App\Models\Member;
use App\Models\Company;
use App\Models\Province;
use App\Models\City;
use App\Models\Barangay;
use App\Models\CompanyClassification;
use App\Models\Activity;

use App\Http\Repositories\MyCompany\MemberRepository as Repository;

use App\Http\Requests\MyCompany\Member\CheckRequest;
use App\Http\Requests\MyCompany\Member\StoreRequest;
use App\Http\Requests\MyCompany\Member\UpdateRequest;
use App\Http\Requests\MyCompany\Member\UpdatePhotoRequest;
use App\Http\Requests\MyCompany\Member\UpdateThumbmarkRequest;
use App\Http\Requests\MyCompany\Member\ArrangeRelativesRequest;
use App\Http\Requests\MyCompany\Member\StoreRelativeRequest;
use App\Http\Requests\MyCompany\Member\StoreAttachmentRequest;
use App\Http\Requests\MyCompany\Member\UpdateAttachmentRequest;

class MemberController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth.permission');

        $this->repository = new Repository;
    }

    public function index(Request $request)
    {
        $company = Auth::user()->company();
        
        $request->merge([
            'members-related' => true
        ]);

        $model = new Member;

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
                            
                            if(isset($request->get('filter')['isHousehold'])):
                                $q->where('is_household', $request->get('filter')['isHousehold']);
                            endif;
                            
                            if(isset($request->get('filter')['gender'])):
                                $q->where('gender', $request->get('filter')['gender']);
                            endif;
                        endif;
                    })
                    ->where(function($q) use ($request)
                    {
                        if($request->has('range') && $request->get('range')['dateRegistered']):
                            $dates = explode(',', $request->get('range')['dateRegistered']);

                            $q->whereDate('date_registered', $dates[0])
                                ->orWhereDate('date_registered', $dates[1])
                                ->orWhereBetween('date_registered', [$dates[0], $dates[1]]);
                        endif;
                    })
                    ->orderBy('date_registered', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);
    }

    public function check(Request $request, CheckRequest $formRequest)
    {
        $request->merge([
            'member-checking-related' => true
        ]);

        return $this->repository->check($formRequest);
    }

    public function store(Request $request, StoreRequest $formRequest)
    {
        $request->merge([
            'members-related' => true
        ]);

        return $this->repository->store($formRequest);
    }

    public function import(Request $request)
    {
        $request->merge([
            'members-related' => true
        ]);

        return $this->repository->import($request);
    }

    public function show(Request $request, $code)
    {
        $request->merge([
            'members-related' => true
        ]);

        return $this->repository->show($request, $code);
    }

    public function edit(Request $request, $code)
    {
        $request->merge([
            'members-related' => true
        ]);

        return $this->repository->edit($request, $code);
    }

    public function update(Request $request, UpdateRequest $formRequest, $code)
    {
        $request->merge([
            'members-related' => true
        ]);

        return $this->repository->update($request, $code);
    }

    public function updatePhoto(Request $request, UpdatePhotoRequest $formRequest, $code)
    {
        $request->merge([
            'members-related' => true
        ]);

        return $this->repository->updatePhoto($request, $code);
    }

    public function updateLeftThumbmark(Request $request, UpdateThumbmarkRequest $formRequest, $code)
    {
        $request->merge([
            'members-related' => true
        ]);

        return $this->repository->updateLeftThumbmark($request, $code);
    }

    public function updateRightThumbmark(Request $request, UpdateThumbmarkRequest $formRequest, $code)
    {
        $request->merge([
            'members-related' => true
        ]);

        return $this->repository->updateRightThumbmark($request, $code);
    }

    public function showProfile(Request $request, $code)
    {
        $request->merge([
            'members-profile-related' => true
        ]);

        return $this->repository->showProfile($request, $code);
    }

    public function showContact(Request $request, $code)
    {
        $request->merge([
            'members-sms-related' => true
        ]);

        return $this->repository->showContact($request, $code);
    }

    public function download(Request $request)
    {
        $company = Auth::user()->company();

        $filters = [
            'companyId' => $company->id,
        ];
        
        $sorts = [
            'dateRegistered' => 'asc',
            'created' => 'desc'
        ];

        $request->merge([
            'members-related' => true,
            'filter' => $this->handleQueries('filter', $request, $filters),
            'sort' => $this->handleQueries('sort', $request, $sorts),
            'all' => true
        ]);

        $model = new Member;

        $members = $model->build()['data'];

        return $this->repository->download($request, $members);
    }

    public function destroy(Request $request, $code)
    {
        $request->merge([
            'member-deletion' => true
        ]);

        return $this->repository->destroy($request, $code);
    }

    public function storeIdentification(Request $request, $code)
    {
        $request->merge([
            'members-related' => true,
        ]);

        return $this->repository->storeIdentification($request, $code);
    }

    public function downloadIdentification(Request $request, $code, $idCode)
    {
        $request->merge([
            'members-related' => true,
        ]);

        return $this->repository->downloadIdentification($request, $code, $idCode);
    }

    

    public function customDownloadIdentification(Request $request, $code)
    {
        $request->merge([
            'members-related' => true,
        ]);

        return $this->repository->customDownloadIdentification($request, $code);
    }
    
    public function showActivities(Request $request, $code)
    {
        $module = Slug::findCodeOrDie($code);

        $filters = [
            'activityType' => 'App\\Models\\Member',
            'activityId' => $module->slug_id
        ];

        $sorts = [
            'created' => 'desc'
        ];

        $request->merge([
            'members-related' => true,
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
            'member-options' => true,
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
            'member-options' => true,
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
            'member-options' => true,
            'sort' => $this->handleQueries('sort', $request, $sorts),
            'all' => true
        ]);

        $model = new Barangay;

        return $model->build();
    }

    public function classificationOptions(Request $request)
    {
        $sorts = [
            'name' => 'asc'
        ];

        $request->merge([
            'member-options' => true,
            'sort' => $this->handleQueries('sort', $request, $sorts),
            'all' => true
        ]);

        $model = new CompanyClassification;

        return $model->build();
    }

    public function reportFieldOptions(Request $request)
    {
        $model = new Member;

        return [
            'data' => $model->reportFieldOptions
        ];
    }

    // public function templatesOptions(Request $request, $code)
    // {
    //     $residentSlug = Slug::findCodeOrDie($code);

    //     $resident = $residentSlug->slug;

    //     $barangay = BarangayProfile::where('barangay_id', $resident->barangay_id)->first();

    //     if(!$barangay) return response([
    //         'data' => []
    //     ], 200);

    //     $filters = [
    //         'brgyCode' => $barangay->slug->code,
    //         'constituentTypeId' => 2
    //     ];
        
    //     $sorts = [
    //         'name' => 'asc'
    //     ];

    //     $request->merge([
    //         'resident-options' => true,
    //         'filter' => $this->handleQueries('filter', $request, $filters),
    //         'sort' => $this->handleQueries('sort', $request, $sorts),
    //         'all' => true
    //     ]);

    //     $model = new BarangayTemplate;

    //     return $model->build();
    // }

    // public function idTemplatesOptions(Request $request, $code)
    // {
    //     $residentSlug = Slug::findCodeOrDie($code);

    //     $resident = $residentSlug->slug;

    //     $barangay = BarangayProfile::where('barangay_id', $resident->barangay_id)->first();

    //     if(!$barangay) return response([
    //         'data' => []
    //     ], 200);

    //     $filters = [
    //         'brgyCode' => $barangay->slug->code
    //     ];
        
    //     $sorts = [
    //         'created' => 'desc'
    //     ];

    //     $request->merge([
    //         'resident-options' => true,
    //         'filter' => $this->handleQueries('filter', $request, $filters),
    //         'sort' => $this->handleQueries('sort', $request, $sorts),
    //         'all' => true
    //     ]);

    //     $model = new BarangayIdentificationTemplate;

    //     return $model->build();
    // }

    // public function residentsOptions(Request $request, $code)
    // {
    //     $request->merge([
    //         'resident-relative-options' => true,
    //     ]);

    //     $residentSlug = Slug::findCodeOrDie($code);
    //     $resident = $residentSlug->slug;

    //     $barangay = BarangayProfile::where('barangay_id', $resident->barangay_id)->first();

    //     if(!$barangay) return response([
    //         'data' => []
    //     ], 200);

    //     $model = new Resident;

    //     return $model->where('barangay_id', $barangay->barangay_id)
    //                 ->where(function($q) use ($request)
    //                 {
    //                     if($request->has('firstName') && $request->get('firstName')):
    //                         $q->where('first_name', 'LIKE', '%'.$request->get('firstName').'%');
    //                     endif;

    //                     if($request->has('middleName') && $request->get('middleName')):
    //                         $q->where('middle_name', 'LIKE', '%'.$request->get('middleName').'%');
    //                     endif;

    //                     if($request->has('lastName') && $request->get('lastName')):
    //                         $q->where('last_name', 'LIKE', '%'.$request->get('lastName').'%');
    //                     endif;
    //                 })
    //                 ->orderBy('first_name', 'asc')
    //                 ->get();
    // }

    // public function relationshipsOptions(Request $request)
    // {
    //     $model = new ResidentRelative;

    //     return $model->relationships;
    // }

    // public function barangayProfilesFilters(Request $request)
    // {
    //     $sorts = [
    //         'barangayName' => 'asc'
    //     ];

    //     $request->merge([
    //         'resident-barangays-filters' => true,
    //         'sort' => $this->handleQueries('sort', $request, $sorts),
    //         'all' => true
    //     ]);

    //     $model = new BarangayProfile;

    //     return $model->build();
    // }
}
