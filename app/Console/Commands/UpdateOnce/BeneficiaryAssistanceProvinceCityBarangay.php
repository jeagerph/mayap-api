<?php

namespace App\Console\Commands\UpdateOnce;

use Illuminate\Console\Command;

class BeneficiaryAssistanceProvinceCityBarangay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-once:beneficiary-assistance-province-city-barangay {companyId} {provinceId} {cityId} {barangayId}';

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
        $provinceId = $this->argument('provinceId');
        $cityId = $this->argument('cityId');
        $barangayId = $this->argument('barangayId');

        $count = 0;

        $assistances = \App\Models\BeneficiaryAssistance::where('company_id', $companyId)->whereNull('province_id')->get();

        foreach($assistances as $assistance):

            $assistance->update([
                'province_id' => $assistance->beneficiary
                    ? $assistance->beneficiary->province_id
                    : $provinceId,
                'city_id' => $assistance->beneficiary
                    ? $assistance->beneficiary->city_id
                    : $cityId,
                'barangay_id' => $assistance->beneficiary
                    ? $assistance->beneficiary->barangay_id
                    : $barangayId,
                'updated_by' => 1
            ]);

            $count++;

            $this->info($count .' / ' . $assistances->count() . ' - ' . $assistance->id . ' has been updated.');

        endforeach;

        $this->info($count . ' assistances have been updated.');
    }
}
