<?php

namespace App\Console\Commands\UpdateOnce;

use Illuminate\Console\Command;

class BeneficiaryAssistancesCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-once:beneficiary-assistances-count {companyId}';

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
        $batch = 1;

        \App\Models\Beneficiary::where('company_id', $companyId)
                        ->chunk(1000, function($beneficiaries) use ($count, $batch)
                        {
                            foreach ($beneficiaries as $beneficiary):

                                $assistancesCount = $beneficiary->assistances()->count();

                                $beneficiary->update([
                                    'assistances_count' => $assistancesCount,
                                    'updated_by' => 1
                                ]);
                    
                                $count++;
                    
                                $this->info('ID: ' .$beneficiary->id . ' | COUNT: ' . $count );

                            endforeach;

                            $batch++;

                        });

        $this->info($count . ' beneficiaries have been updated.');
    }
}
