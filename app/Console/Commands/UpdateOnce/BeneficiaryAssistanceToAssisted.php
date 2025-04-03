<?php

namespace App\Console\Commands\UpdateOnce;

use Illuminate\Console\Command;

class BeneficiaryAssistanceToAssisted extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-once:beneficiary-assistance-to-assisted {companyId}';

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
        $companyId = $this->argument('companyId');

        $count = 0;

        $assistances = \App\Models\BeneficiaryAssistance::where('company_id', $companyId)->where('is_assisted', 0)->get();

        foreach($assistances as $assistance):

            $assistance->update([
                'is_assisted' => 1,
                'assisted_date' => $assistance->assistance_date,
                'assisted_by' => 'STAFF',
                'updated_by' => 1
            ]);

            $count++;

            $this->info($count .' / ' . $assistances->count() . ' - ' . $assistance->assistance_type . ' has been updated.');

        endforeach;

        $this->info($count . ' assistances have been updated.');
    }
}
