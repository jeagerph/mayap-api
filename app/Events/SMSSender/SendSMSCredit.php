<?php

namespace App\Events\SMSSender;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

use App\Models\SmsCredit;

use App\Http\Repositories\Base\SmsCreditRepository;
use App\Http\Repositories\Base\SystemSettingRepository;
use App\Http\Repositories\Base\BarangayProfileRepository;
use App\Http\Repositories\Base\SmsContractRepository;

use App\Traits\MeSender;

class SendSMSCredit implements ShouldQueue
{
    use Dispatchable, SerializesModels, MeSender;

    public $smsCredit;

    public $creditRepository;

    public $systemSettingRepository;

    public $profileRepository;

    public $contractRepository;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(SmsCredit $credit)
    {
        $this->smsCredit = $credit;

        $this->creditRepository = new SmsCreditRepository;

        $this->systemSettingRepository = new SystemSettingRepository;

        $this->profileRepository = new BarangayProfileRepository;

        $this->contractRepository = new SmsContractRepository;
    }

    public function handle()
    {
        $transactions = $this->smsCredit->smsTransactions()->where('status', 1)->get();
        $barangay = $this->smsCredit->profile;
        $smsSetting = $barangay->smsSetting;
        
        $sentMobileNumbers = [];
        $failedMobileNumbers = [];

        foreach($transactions as $transaction):

            if(mobileNumberValidator($transaction->mobile_number)):

                if(!in_array($transaction->mobile_number, $sentMobileNumbers)):
                
                    $statusCode = $this->fireSmsTransaction(
                        $transaction,
                        $smsSetting->branding_api_url,
                        $smsSetting->branding_api_key,
                    );

                    if ($statusCode == 200):

                        $sentMobileNumbers[] = $transaction->mobile_number;

                    else:

                        $failedMobileNumbers[] = $transaction->mobile_number;
                        
                    endif;

                endif;

            else:

                $transaction->update([
                    'status' => 3,
                    'failure_message' => 'Invalid number',
                    'updated_by' => 1
                ]);

                $failedMobileNumbers[] = $transaction->mobile_number;
            endif;

        endforeach;

        $this->creditRepository->computeCreditAmount($this->smsCredit);

        $this->creditRepository->checkCreditStatus($this->smsCredit);

        $updatedSmsCredit = SmsCredit::find($this->smsCredit->id);

        $barangay->smsContracts()->save(
            $this->contractRepository->new([
                'contract_date' => now()->format('Y-m-d'),
                'amount' => $updatedSmsCredit->amount,
                'mode' => 2,
                'remarks' => $updatedSmsCredit->code . ' | '
            ], $barangay
            )
        );

        $this->profileRepository->refreshCreditAmount($barangay);

        $log = new Logger('sms_credit_logs');
        $log->pushHandler(new StreamHandler(storage_path('logs/sms_credit_logs.log')), Logger::INFO);
        $log->info('sms_credit_logs', [
            'api_url' => $smsSetting->branding_api_url,
            'api_key' => $smsSetting->branding_api_key,
            'sms_credit_code' => $this->smsCredit->code,
            'message' => $transaction->message,
            'sent_mobile_numbers' => implode(',', $sentMobileNumbers),
            'failed_mobile_numbers' => implode(',', $failedMobileNumbers),
        ]);
    }
}
