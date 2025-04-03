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

use App\Models\SmsTransaction;

use App\Http\Repositories\Base\SmsCreditRepository;

use App\Traits\MeSender;

class SendSMSTransaction implements ShouldQueue
{
    use Dispatchable, SerializesModels, MeSender;

    public $smsTransaction;

    public $creditRepository;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(SmsTransaction $transaction)
    {
        $this->smsTransaction = $transaction;

        $this->creditRepository = new SmsCreditRepository;
    }

    public function handle()
    {
        $sentMobileNumbers = [];
        $failedMobileNumbers = [];

        $mobileNumber = $this->smsTransaction->mobile_number;
        $smsCredit = $this->smsTransaction->smsCredit;
        $barangay = $smsCredit->profile;
        $smsSetting = $barangay->smsSetting;

        if(mobileNumberValidator($mobileNumber)):
                
            $statusCode = $this->fireSmsTransaction(
                $transaction,
                $smsSetting->branding_api_url,
                $smsSetting->branding_api_key,
            );

            if ($statusCode == 200):

                $this->creditRepository->computeCreditAmount($smsCredit);

                $sentMobileNumbers[] = $mobileNumber;

            else:
                $failedMobileNumbers[] = $mobileNumber;
            endif;

        else:

            $this->smsTransaction->update([
                'status' => 3,
                'failure_message' => 'Invalid number',
                'updated_by' => 1
            ]);

            $failedMobileNumbers[] = $mobileNumber;
        endif;

        $log = new Logger('sms_credit_logs');
        $log->pushHandler(new StreamHandler(storage_path('logs/sms_credit_logs.log')), Logger::INFO);
        $log->info('sms_credit_logs', [
            'api_url' => $smsSetting->branding_api_url,
            'api_key' => $smsSetting->branding_api_key,
            'sms_credit_code' => $smsCredit->code,
            'message' => $this->smsTransaction->message,
            'sent_mobile_numbers' => implode(',', $sentMobileNumbers),
            'failed_mobile_numbers' => implode(',', $failedMobileNumbers),
        ]);
    }
}
