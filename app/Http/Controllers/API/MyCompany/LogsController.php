<?php

namespace App\Http\Controllers\API\MyCompany;

use App\Models\City;
use App\Models\Voter;
use App\Models\Barangay;
use App\Models\Province;
use App\Models\Beneficiary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class LogsController extends Controller
{
    public function getProvinces()
    {
        $provinces = Province::all();

        return response()->json(
            $provinces
        );
    }

    public function getCities($province_code)
    {
        $cities = City::where('prov_code', $province_code)->get();

        return response()->json(
            $cities
        );
    }

    public function getBarangays($city_code)
    {
        $barangays = Barangay::where('city_code', $city_code)->get();

        return response()->json(
            $barangays
        );
    }

    public function getBeneficiaries($barangay_id)
    {
        $beneficiaries = DB::table('beneficiaries as b')
            ->join('voters as v', function ($join) {
                $join->on('b.first_name', '=', 'v.first_name')
                    ->on('b.middle_name', '=', 'v.middle_name')
                    ->on('b.last_name', '=', 'v.last_name');
            })
            ->where('b.barangay_id', $barangay_id)
            ->select(
                'b.id',
                'b.first_name',
                'b.middle_name',
                'b.last_name',
                'b.code',
                'b.mobile_no',
                'v.precinct_no',
                'b.address',
                'b.barangay_id'
            )
            ->get();

        return response()->json($beneficiaries);
    }
}
