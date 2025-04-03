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

use App\Http\Repositories\Base\SmsTransactionRepository;

class GenerateSMSTransactions implements ShouldQueue
{
    use Dispatchable, SerializesModels;

    public $smsCredit;

    public $residents;

    public $transactionRepository;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(SmsCredit $smsCredit, $residents)
    {
        $this->smsCredit = $smsCredit;

        $this->residents = $residents;

        $this->transactionRepository = new SmsTransactionRepository;
    }

    public function handle()
    {
        $count = 0;

        foreach($this->residents as $resident):


            $this->smsCredit->smsTransactions()->save(
                $this->transactionRepository->new([
                    'mobile_number' => $resident->contact_no ?: '09',
                    'message' => $this->smsCredit->message
                ])
            );

            $count++;

        endforeach;

        $log = new Logger('generate_sms_transactions_logs');
        $log->pushHandler(new StreamHandler(storage_path('logs/generate_sms_transactions_logs.log')), Logger::INFO);
        $log->info('generate_sms_transactions_logs', [
            'sms_credit_code' => $this->smsCredit->code,
            'residents_count' => $this->residents->count(),
            'transactions_count' => $count,
        ]);

        return true;
    }
}
