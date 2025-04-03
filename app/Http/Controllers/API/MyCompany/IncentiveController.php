<?php

namespace App\Http\Controllers\API\MyCompany;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\BeneficiaryIncentive;

use App\Http\Repositories\MyCompany\IncentiveRepository as Repository;

use App\Http\Requests\MyCompany\Incentive\UpdateRequest;

class IncentiveController extends Controller
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
            'incentiveDate' => 'desc',
            'created' => 'desc',
        ];

        $request->merge([
            'my-company-incentives-related' => true,
            'filter' => $this->handleQueries('filter', $request, $filters),
            'sort' => $this->handleQueries('sort', $request, $sorts),
        ]);

        $model = new BeneficiaryIncentive;

        return $model->where('company_id', $company->id)
                    ->where(function($q) use ($request)
                    {
                        if ($request->has('firstName') || $request->has('middleName') || $request->has('lastName')):

                            $q->whereHas('beneficiary', function($q) use ($request)
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
                            });
                            
                        endif;
                    })
                    ->where(function($q) use ($request)
                    {
                        if($request->has('range') && $request->get('range')['incentiveDate']):
                            $dates = explode(',', $request->get('range')['incentiveDate']);

                            $q->whereDate('incentive_date', $dates[0])
                                ->orWhereDate('incentive_date', $dates[1])
                                ->orWhereBetween('incentive_date', [$dates[0], $dates[1]]);
                        endif;
                    })
                    ->orderBy('incentive_date', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);

        return $model->build();
    }

    public function destroy(Request $request, $id)
    {
        $request->merge([
            'my-company-assistance-deletion' => true
        ]);

        return $this->repository->destroy($request, $id);
    }
}
