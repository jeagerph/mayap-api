<?php

namespace App\Http\Controllers\API\MyCompany;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Repositories\MyCompany\MonitoringRepository as Repository;

class MonitoringController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth.permission');

        $this->repository = new Repository;
    }

    public function showBarangays(Request $request)
    {
        $request->merge([
            'monitoring-barangays-related' => true,
        ]);

        return $this->repository->showBarangays($request);
    }

    public function showBarangayResidents(Request $request)
    {
        $request->merge([
            'monitoring-barangays-related' => true,
        ]);

        return $this->repository->showBarangayResidents($request);
    }

    public function showBarangayResident(Request $request)
    {
        $request->merge([
            'monitoring-barangays-related' => true,
        ]);

        return $this->repository->showBarangayResident($request);
    }
}
