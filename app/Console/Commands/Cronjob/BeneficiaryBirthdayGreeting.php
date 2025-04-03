<?php

namespace App\Console\Commands\Cronjob;

use Illuminate\Console\Command;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

use App\Http\Repositories\Base\CompanyRepository;
use App\Http\Repositories\Base\CompanySmsTransactionRepository;

class BeneficiaryBirthdayGreeting extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:beneficiary-birthday-greeting {specificDate?}';

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
        $currentDate = now()->format('Y-m-d');
        $birthDate = $this->argument('specificDate') ?: now()->format('Y-m-d');
        $smsTransactionRepository = new CompanySmsTransactionRepository;

        $companies = \App\Models\Company::where('status', 1)->get();

        foreach ($companies as $company):

            $response = $smsTransactionRepository->sendBirthdaySms($birthDate, $company);

            $log = new Logger('beneficiary_birthday_greetings_logs');
            $log->pushHandler(new StreamHandler(storage_path('logs/beneficiary_birthday_greetings_logs.log')), Logger::INFO);
            $log->info('beneficiary_birthday_greetings_logs', [
                'company' => $company->name,
                'date_initiated' => $currentDate,
                'birth_date' => $birthDate,
                'status' => $response['status'],
                'statusMessage' => $response['statusMessage'],
                'sent_beneficiaries' => $response['status'] ? $response['sent_beneficiaries'] : null,
                'failed_beneficiaries' => $response['status'] ? $response['failed_beneficiaries'] : null,
            ]);

        endforeach;
    }
}
