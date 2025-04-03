<?php

namespace App\Http\Controllers\API\MyCompany;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\CompanyNetworkSetting;

use App\Http\Repositories\MyCompany\SettingRepository as Repository;

use App\Http\Requests\MyCompany\Setting\UpdateNetworkSettingRequest;

class SettingController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth.permission');

        $this->repository = new Repository;
    }

    public function showNetworkSetting(Request $request)
    {
        $request->merge([
            'my-company-related' => true
        ]);

        return $this->repository->showNetworkSetting($request);
    }

    public function updateNetworkSetting(Request $request, UpdateNetworkSettingRequest $formRequest)
    {
        $request->merge([
            'my-company-related' => true
        ]);

        return $this->repository->updateNetworkSetting($formRequest);
    }
}
