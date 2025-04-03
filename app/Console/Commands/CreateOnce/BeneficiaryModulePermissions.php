<?php

namespace App\Console\Commands\CreateOnce;

use Illuminate\Console\Command;

class BeneficiaryModulePermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create-once:beneficiary-module-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $model = new \App\Models\Module;
        $model->is_admin = 0;
        $model->name = 'COMPANY / BENEFICIARIES';
        $model->route_name = 'company-beneficiaries';
        $model->created_at = now();
        $model->created_by = 1;
        $model->save();

        $this->info($model->name . ' is created.');

        $permissionRepository = new \App\Http\Repositories\Base\AccountPermissionRepository;

        $companyAccounts = \App\Models\CompanyAccount::get();

        foreach($companyAccounts as $companyAccount):

            $account = $companyAccount->account;

            if ($account):

                $account->permissions()->save(
                    $permissionRepository->new([
                        'module_id' => $model->id,
                        'access' => 1,
                        'index' => 1,
                        'store' => 1,
                        'update' => 1,
                        'destroy' => 1,
                    ])
                );

                $this->info($account->full_name . ' permission has been updated.');

            endif;

        endforeach;

        $this->info('Done!');


    }
}
