<?php

namespace App\Exports\Accounts\base;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

use App\Http\Repositories\Base\CompanyAccountRepository;

class SummaryReport implements WithMultipleSheets
{
    public function __construct($request, $company)
    {
        $this->company = $company;

        $this->request = $request;

        $this->companyAccountRepository = new CompanyAccountRepository;
    }

    public function sheets(): array
    {
        $sheets = [];

        $company = $this->company;
        $request = $this->request;

        $dates = [
            'from' => $request->get('from'),
            'to' => $request->get('to')
        ];

        $arrCompanyAccounts = [];

        $companyAccounts = $company->companyAccounts()->get();

        foreach ($companyAccounts as $companyAccount):

            $beneficiarySummary = $this->companyAccountRepository->beneficiariesTotal($dates, $companyAccount);

            $assistanceSummary = $this->companyAccountRepository->assistancesTotal($dates, $companyAccount);

            $patientSummary = $this->companyAccountRepository->patientsTotal($dates, $companyAccount);

            $arrCompanyAccounts[] = [
                'encoder' => $companyAccount->account->full_name,
                'beneficiary' => $beneficiarySummary['total'] ?: 0,
                'assistance' => $assistanceSummary['total'] ?: 0,
                'patient' => $patientSummary['total'] ?: 0,
            ];
        endforeach;

        $sheets[] = new \App\Exports\Accounts\base\Sheets\SummarySheet([
            'company' => $companyAccount->company,
            'request' => $this->request,
            'companyAccounts' => $arrCompanyAccounts,

        ]);

        return $sheets;
    }
}
