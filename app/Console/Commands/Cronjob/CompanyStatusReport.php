<?php

namespace App\Console\Commands\Cronjob;

use Illuminate\Console\Command;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

use App\Http\Repositories\Base\CompanyRepository;
use App\Http\Repositories\Base\CompanySmsTransactionRepository;

class CompanyStatusReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:company-status-report {specificDate?}';

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
        $reportDate = $this->argument('specificDate') ?: now()->format('Y-m-d');

        $smsTransactionRepository = new CompanySmsTransactionRepository;

        $companies = \App\Models\Company::where('status', 1)->get();

        foreach ($companies as $company):

            $response = $smsTransactionRepository->sendStatusReportSms($reportDate, $company);

            $log = new Logger('company_status_report_logs');
            $log->pushHandler(new StreamHandler(storage_path('logs/company_status_report_logs.log')), Logger::INFO);
            $log->info('company_status_report_logs', [
                'company' => $company->name,
                'date_initiated' => $currentDate,
                'report_date' => $reportDate,
                'status' => $response['status'],
                'statusMessage' => $response['statusMessage'],
                'message' => $response['status'] ? $response['message'] : null,
                'sent_mobile_numbers' => $response['status'] ? $response['sent_mobile_numbers'] : null,
                'failed_mobile_numbers' => $response['status'] ? $response['failed_mobile_numbers'] : null,
            ]);

        endforeach;
    }
}
