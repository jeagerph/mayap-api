<?php

namespace App\Http\Repositories\MyCompany;

use Illuminate\Support\Facades\Auth;

use App\Models\CompanyNetworkSetting;

use App\Http\Repositories\Base\CompanyNetworkSettingRepository;

class SettingRepository
{
    public function __construct()
    {
        $this->companyNetworkSettingRepository = new CompanyNetworkSettingRepository;
    }

    public function showNetworkSetting($request)
    {
        $company = Auth::user()->company();

        $networkSetting = $company->networkSetting;

        if ($networkSetting) return $networkSetting->toArrayMyCompanyRelated();

        $newNetworkSetting = $company->networkSetting()->save(
            $this->companyNetworkSettingRepository->new(
                $this->companyNetworkSettingRepository->defaultData
            )
        );

        return $newNetworkSetting->toArrayMyCompanyRelated();
    }

    public function updateNetworkSetting($request)
    {
        $company = Auth::user()->company();

        $networkSetting = $company->networkSetting;

        $networkSetting->update([
            'master_degree_enabled' => $request->input('master_degree_enabled'),
            'master_degree_points' => $request->input('master_degree_points'),
            'first_degree_enabled' => $request->input('first_degree_enabled'),
            'first_degree_points' => $request->input('first_degree_points'),
            'second_degree_enabled' => $request->input('second_degree_enabled'),
            'second_degree_points' => $request->input('second_degree_points'),
            'third_degree_enabled' => $request->input('third_degree_enabled'),
            'third_degree_points' => $request->input('third_degree_points'),
            'fourth_degree_enabled' => $request->input('fourth_degree_enabled'),
            'fourth_degree_points' => $request->input('fourth_degree_points'),
            'fifth_degree_enabled' => $request->input('fifth_degree_enabled'),
            'fifth_degree_points' => $request->input('fifth_degree_points'),
            'updated_by' => Auth::id()
        ]);

        return (CompanyNetworkSetting::find($networkSetting->id))->toArrayMyCompanyRelated();
    }
}
?>