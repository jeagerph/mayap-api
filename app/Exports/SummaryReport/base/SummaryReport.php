<?php

namespace App\Exports\SummaryReport\base;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

use App\Http\Repositories\Base\CompanyRepository;

class SummaryReport implements WithMultipleSheets
{
    public function __construct($request, $company)
    {
        $this->company = $company;

        $this->request = $request;
    }

    public function sheets(): array
    {
        $sheets = [];

        $company = $this->company;
        $request = $this->request;

        $dates = [
            'date' => $request->get('date')
        ];

        $companyRepository = new CompanyRepository;

        $beneficiaries = $companyRepository->beneficiariesTotal($dates, $company);
        $patients = $companyRepository->patientsTotal($dates, $company);
        $incentives = $companyRepository->incentivesTotal($dates, $company);
        $household = $companyRepository->householdTotal($dates, $company);
        $requested = $companyRepository->requestedAssistancesTotal($dates, $company);
        $assisted = $companyRepository->assistedAssistancesTotal($dates, $company);
        $officers = $companyRepository->officersTotal($dates, $company);
        $networks = $companyRepository->networksTotal($dates, $company);
        $assistancesByType = $companyRepository->assistancesByTypeTotal($dates, $company);
        $householdByBarangay = $companyRepository->householdByBarangayTotal($company);
        $householdByPurok = $companyRepository->householdByPurokTotal($company);

        $sheets[] = new \App\Exports\SummaryReport\base\Sheets\SummarySheet([
            'company' => $this->company,
            'request' => $this->request,
            'beneficiaries' => $beneficiaries,
            'patients' => $patients,
            'incentives' => $incentives,
            'household' => $household,
            'requested' => $requested,
            'assisted' => $assisted,
            'officers' => $officers,
            'networks' => $networks,
            'assistancesByType' => $assistancesByType,
            'householdByBarangay' => $householdByBarangay,
            'householdByPurok' => $householdByPurok,

        ]);

        return $sheets;
    }
}
