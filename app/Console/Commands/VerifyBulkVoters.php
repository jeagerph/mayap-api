<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Beneficiary;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class VerifyBulkVoters extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'voters:verify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify voters by matching them with beneficiaries for company Mayap';

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
        $companyId = 4;
        Log::info("Starting voter verification update for company ID: {$companyId}");

        $batchSize = 100;
        DB::table('beneficiaries')
            ->join('slugs', function ($join) {
                $join->on('beneficiaries.id', '=', 'slugs.slug_id')
                    ->where('slugs.slug_type', '=', Beneficiary::class);
            })
            ->where('beneficiaries.company_id', $companyId)
            ->where(function ($query) {
                $query->whereNotIn('beneficiaries.verify_voter', [1, 2])
                    ->orWhereNull('beneficiaries.verify_voter');
            })
            ->select('beneficiaries.id', 'beneficiaries.first_name', 'beneficiaries.last_name', 'beneficiaries.middle_name')
            ->orderBy('beneficiaries.id')
            ->chunk($batchSize, function ($beneficiaries) use ($companyId) {
                $beneficiaryIds = $beneficiaries->pluck('id')->toArray();


                $matchedBeneficiaryIds = DB::table('beneficiaries')
                    ->join('voters', function ($join) use ($companyId) {
                        $join->on('voters.first_name', '=', 'beneficiaries.first_name')
                            ->on('voters.last_name', '=', 'beneficiaries.last_name')
                            ->where('voters.company_id', '=', $companyId);
                    })
                    ->whereIn('beneficiaries.id', $beneficiaryIds)
                    ->pluck('beneficiaries.id')
                    ->toArray();


                if (!empty($matchedBeneficiaryIds)) {
                    DB::table('beneficiaries')
                        ->whereIn('id', $matchedBeneficiaryIds)
                        ->update(['verify_voter' => 1]);

                    Log::info("Updated " . count($matchedBeneficiaryIds) . " beneficiaries as verified.");
                }
            });

        Log::info("Voter verification process completed for company ID: {$companyId}");
        $this->info("Voter verification process completed.");
    }
}
