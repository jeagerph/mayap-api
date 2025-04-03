<?php

namespace App\Events\SMSCredit;

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

class UpdateSMSMessage implements ShouldQueue
{
    use Dispatchable, SerializesModels;

    public $smsCredit;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(SmsCredit $smsCredit)
    {
        $this->smsCredit = $smsCredit;
    }

    public function handle()
    {
        $count = 0;

        $message = $this->smsCredit->message;

        $transactions = $this->smsCredit->smsTransactions()->get();

        if ($this->smsCredit->status == 1)
        {
            foreach($transactions as $transaction):

                $transaction->update([
                    'message' => $message,
                    'updated_by' => 1
                ]);

                $count++;

            endforeach;
        }

        $log = new Logger('update_sms_credit_message_logs');
        $log->pushHandler(new StreamHandler(storage_path('logs/update_sms_credit_message_logs.log')), Logger::INFO);
        $log->info('update_sms_credit_message_logs', [
            'sms_credit_code' => $this->smsCredit->code,
            'message' => $this->smsCredit->message,
            'transactions_count' => $count,
        ]);
    }
}
