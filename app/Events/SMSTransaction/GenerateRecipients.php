<?php

namespace App\Events\SMSTransaction;

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

use App\Models\BarangaySmsTransaction;
use App\Models\BarangaySmsRecipient;

use App\Http\Repositories\Base\BarangaySmsRecipientRepository;

class GenerateRecipients implements ShouldQueue
{
    use Dispatchable, SerializesModels;

    public $smsTransaction;

    public $residents;

    public $smsRecipientRepository;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(BarangaySmsTransaction $smsTransaction, $residents)
    {
        $this->smsTransaction = $smsTransaction;

        $this->residents = $residents;

        $this->smsRecipientRepository = new BarangaySmsRecipientRepository;
    }

    public function handle()
    {
        $count = 0;

        foreach($this->residents as $resident):


            $this->smsTransaction->smsRecipients()->save(
                $this->smsRecipientRepository->new([
                    'mobile_number' => $resident->contact_no ?: '09',
                    'message' => $this->smsTransaction->message
                ])
            );

            $count++;

        endforeach;

        $log = new Logger('generate_sms_transactions_logs');
        $log->pushHandler(new StreamHandler(storage_path('logs/generate_sms_transactions_logs.log')), Logger::INFO);
        $log->info('generate_sms_transactions_logs', [
            'sms_transaction_code' => $this->smsTransaction->code,
            'residents_count' => $this->residents->count(),
            'recipients_count' => $count,
        ]);

        return true;
    }
}
