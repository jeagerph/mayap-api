<?php

namespace App\Http\Repositories\MyCompany;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use App\Models\Slug;
use App\Models\Company;
use App\Models\CompanyAccount;

use App\Http\Repositories\Base\SlugRepository;
use App\Http\Repositories\Base\PDFRepository;
use App\Http\Repositories\Base\CompanyRepository;
use App\Http\Repositories\Base\UserRepository;
use App\Http\Repositories\Base\CompanyAccountRepository;
use App\Http\Repositories\Base\AccountPermissionRepository;
use App\Http\Repositories\Base\AccountRepository as BaseRepository;

use App\Traits\FileStorage;

class AccountRepository
{
    use FileStorage;

    public function __construct()
    {
        $this->baseRepository = new BaseRepository;
        $this->pdfRepository = new PDFRepository;
        $this->companyRepository = new CompanyRepository;
        $this->userRepository = new UserRepository;
        $this->slugRepository = new SlugRepository;
        $this->companyAccountRepository = new CompanyAccountRepository;
        $this->permissionRepository = new AccountPermissionRepository;
    }

    public function downloadSummaryReport($request)
    {
        $company = Auth::user()->company();

        $from = (new \Carbon\Carbon($request->get('from')))->format('Y-m-d');
        $to = (new \Carbon\Carbon($request->get('to')))->format('Y-m-d');

        $file = $company->report_file;

        $fileName = $this->pdfRepository->fileName(
            $request->get('from'),
            $request->get('to'),
            strtolower($company->name) . '-encoding-summary-report',
            '.xlsx'
        );

        $namespace = '\App\Exports\Accounts\base\SummaryReport';

        \Excel::store(
            new $namespace($request, $company),
            'storage/exports/' . $fileName,
            env('STORAGE_DISK', 'public')
        );

        return [
            'path' => env('CDN_URL') . '/storage/exports/' . $fileName
        ];
    }

    public function storeAccount($request)
    {
        $company = Auth::user()->company();

        $newUser = $this->userRepository->store($request);

        $newAccount = $newUser->account()->save(
            $this->baseRepository->new($request)
        );

        $newAccount->slug()->save(
            $this->slugRepository->new(
                $request->input('full_name') . ' Account'
            )
        );

        $newCompanyAccount = $company->companyAccounts()->save(
            $this->companyAccountRepository->new([
                'account_id' => $newAccount->id,
                'company_position_id' => $request->input('company_position_id'),
            ])
        );

        $newCompanyAccount->slug()->save(
            $this->slugRepository->new(
                $request->input('full_name') . ' Company Account'
            )
        );

        $this->baseRepository->setCompanyPermissions($newCompanyAccount->account);

        return $newCompanyAccount;
    }

    public function updateAccount($request, $code)
    {
        $model = Slug::findCodeOrDie($code);

        $companyAccount = $model->slug;

        $account = $companyAccount->account;

        $user = $account->user;

        $company = Auth::user()->company();

        $this->companyRepository->isAccountRelated($company, $companyAccount->id);

        $companyAccount->update([
            'company_position_id' => $request->input('company_position_id'),
            'updated_by' => Auth::id()
        ]);

        $account->update([
            'full_name' => $request->input('full_name'),
            'email' => $request->input('email'),
            'mobile_number' => $request->input('mobile_number'),
            'updated_by' => Auth::id()
        ]);

        $user->update([
            'username' => $request->input('username'),
            'updated_by' => Auth::id()
        ]);

        return (CompanyAccount::find($companyAccount->id))->toArrayMyCompanyAccountsRelated();
    }

    
    public function updateAccountPhoto($request, $code)
    {
        $model = Slug::findCodeOrDie($code);

        $companyAccount = $model->slug;

        $account = $companyAccount->account;

        $company = Auth::user()->company();

        $this->companyRepository->isAccountRelated($company, $companyAccount->id);

        $filePath = $this->baseRepository->updatePhoto(
            $account->photo,
            $request, 'account/photo'
        );

        $account->update([
            'photo' => $filePath,
            'updated_by' => Auth::id()
        ]);

        return (CompanyAccount::find($companyAccount->id))->toArrayMyCompanyAccountsRelated();
    }
    
    public function updateAccountPermission($request, $code)
    {
        $model = Slug::findCodeOrDie($code);

        $companyAccount = $model->slug;

        $account = $companyAccount->account;

        $company = Auth::user()->company();

        $this->companyRepository->isAccountRelated($company, $companyAccount->id);

        $permission = $account->permissions()->where('module_id', $request->input('module_id'))->first();

        if($permission):

            $permission->update([
                'access' => $request->input('access'),
                'index' => $request->input('index'),
                'store' => $request->input('store'),
                'update' => $request->input('update'),
                'destroy' => $request->input('destroy'),
                'updated_by' => Auth::id()
            ]);

            return $permission;
        else:
            $newPermission = $account->permissions()->save(
                $this->permissionRepository->new([
                    'module_id' => $request->input('module_id'),
                    'access' => $request->input('access'),
                    'index' => $request->input('index'),
                    'store' => $request->input('store'),
                    'update' => $request->input('update'),
                    'destroy' => $request->input('destroy'),
                ])
            );
            
            return $newPermission;
        endif;
    }

    public function destroyAccount($request, $code)
    {
        $model = Slug::findCodeOrDie($code);

        $companyAccount = $model->slug;

        $account = $companyAccount->account;

        $company = Auth::user()->company();

        $this->companyRepository->isAccountRelated($company, $companyAccount->id);

        // $this->companyRepository->isAccountHasRelatedModule($company, $id);

        $account = $companyAccount->account;

        if ($account):
            $account->update([
                'full_name' => 'DELETED',
                'updated_by' => Auth::id(),
            ]);

            $user = $account->user;

            if ($user):
                $user->update([
                    'username' => 'DELETED',
                    'locked' => 1,
                    'updated_by' => Auth::id()
                ]);
            endif;
        endif;

        $companyAccount->delete();

        return response([
            'message' => 'Data deleted successfully.'
        ], 200);
    }

    public function showAccountSummaryTotal($dates, $request, $code)
    {
        $model = Slug::findCodeOrDie($code);

        $companyAccount = $model->slug;

        $account = $companyAccount->account;

        $company = Auth::user()->company();

        $this->companyRepository->isAccountRelated($company, $companyAccount->id);

        $from = (new \Carbon\Carbon($dates['from']))->format('Y-m-d');
        $to = (new \Carbon\Carbon($dates['to']))->format('Y-m-d');

        $dates = [
            'date' => $request->get('date')
        ];

        $beneficiaries = $this->companyAccountRepository->beneficiariesTotal($dates, $companyAccount);
        $assistances = $this->companyAccountRepository->assistancesTotal($dates, $companyAccount);
        $patients = $this->companyAccountRepository->patientsTotal($dates, $companyAccount);

        return [
            'beneficiaries' => $beneficiaries,
            'assistances' => $assistances,
            'patients' => $patients,
        ];
    }

    public function downloadAccountSummaryReport($request, $code)
    {
        $model = Slug::findCodeOrDie($code);

        $companyAccount = $model->slug;

        $account = $companyAccount->account;

        $company = Auth::user()->company();

        $this->companyRepository->isAccountRelated($company, $companyAccount->id);

        $from = (new \Carbon\Carbon($request->get('from')))->format('Y-m-d');
        $to = (new \Carbon\Carbon($request->get('to')))->format('Y-m-d');

        $file = $company->report_file;

        $fileName = $this->pdfRepository->fileName(
            $request->get('from'),
            $request->get('to'),
            strtolower($company->name) . '-encoding-summary-report-' . Str::slug(strtolower($account->full_name), '-'),
            '.xlsx'
        );

        $namespace = '\App\Exports\Accounts\base\AccountSummaryReport';

        \Excel::store(
            new $namespace($request, $companyAccount),
            'storage/exports/' . $fileName,
            env('STORAGE_DISK', 'public')
        );

        return [
            'path' => env('CDN_URL') . '/storage/exports/' . $fileName
        ];
    }

    public function summaryOfBeneficiariesPerMonthTotal($dates, $request, $code)
    {
        $model = Slug::findCodeOrDie($code);

        $companyAccount = $model->slug;

        $account = $companyAccount->account;

        $company = Auth::user()->company();

        $this->companyRepository->isAccountRelated($company, $companyAccount->id);

        $from = (new \Carbon\Carbon($dates['from']))->format('Y-m-d');
        $to = (new \Carbon\Carbon($dates['to']))->format('Y-m-d');


        return $this->baseRepository->beneficiariesPerMonthTotal($dates, $account);
    }

    public function summaryOfAssistancesPerMonthTotal($dates, $request, $code)
    {
        $model = Slug::findCodeOrDie($code);

        $companyAccount = $model->slug;

        $account = $companyAccount->account;

        $company = Auth::user()->company();

        $this->companyRepository->isAccountRelated($company, $companyAccount->id);

        $from = (new \Carbon\Carbon($dates['from']))->format('Y-m-d');
        $to = (new \Carbon\Carbon($dates['to']))->format('Y-m-d');


        return $this->baseRepository->assistancesPerMonthTotal($dates, $account);
    }

    public function summaryOfPatientsPerMonthTotal($dates, $request, $code)
    {
        $model = Slug::findCodeOrDie($code);

        $companyAccount = $model->slug;

        $account = $companyAccount->account;

        $company = Auth::user()->company();

        $this->companyRepository->isAccountRelated($company, $companyAccount->id);

        $from = (new \Carbon\Carbon($dates['from']))->format('Y-m-d');
        $to = (new \Carbon\Carbon($dates['to']))->format('Y-m-d');


        return $this->baseRepository->patientsPerMonthTotal($dates, $account);
    }
}
?>