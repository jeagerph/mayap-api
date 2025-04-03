<?php

namespace App\Http\Repositories\MyCompany;

use Illuminate\Support\Facades\Auth;

use App\Models\Slug;
use App\Models\Company;
use App\Models\Barangay;
use App\Models\SystemSetting;

use App\Http\Repositories\Base\SlugRepository;
use App\Http\Repositories\Base\CompanyRepository;
use App\Http\Repositories\Base\SystemSettingRepository;

class MonitoringRepository
{
    public function __construct()
    {
        $this->slugRepository = new SlugRepository;
        $this->companyRepository = new CompanyRepository;
        $this->systemSettingRepository = new SystemSettingRepository;
    }

    public function showBarangays($request)
    {
        $company = Auth::user()->company();

        $barangays = $company->barangays()->orderBy('barangay_name', 'asc')->get();

        return [
            'data' => $barangays
        ];
    }

    public function showBarangayResidents($request)
    {
        $company = Auth::user()->company();

        $setting = SystemSetting::where('is_default', 1)->first();

        $barangay = $this->companyRepository->isBarangayRelated($company, $request->query('barangayId'));

        $http = new \GuzzleHttp\Client;

        $url = $setting->kapitan_api_url;
        $url .= '/membership/residents?ctx';
        $url .= '&filter[brgyId]=' . $barangay->barangay_id;

        if ($request->has('firstName') && $request->input('firstName')):
            $url .= '&firstName='. $request->input('firstName');
        endif;

        if ($request->has('middleName') && $request->input('middleName')):
            $url .= '&middleName='. $request->input('middleName');
        endif;

        if ($request->has('lastName') && $request->input('lastName')):
            $url .= '&lastName='. $request->input('lastName');
        endif;

        if ($request->has('page')):
            $url .= '&page=' . $request->query('page');
        endif;

        $response = $http->get($url);

        return json_decode((string) $response->getBody(), true);
    }

    public function showBarangayResident($request)
    {
        $company = Auth::user()->company();

        $setting = SystemSetting::where('is_default', 1)->first();

        $barangay = $this->companyRepository->isBarangayRelated($company, $request->query('barangayId'));

        $http = new \GuzzleHttp\Client;

        $url = $setting->kapitan_api_url;
        $url .= '/membership/resident/' . $request->query('residentCode');

        $response = $http->get($url);

        return json_decode((string) $response->getBody(), true);
    }
}
?>