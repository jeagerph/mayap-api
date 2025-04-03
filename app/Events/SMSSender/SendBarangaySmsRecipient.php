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

use App\Models\BarangaySmsRecipient;

use App\Http\Repositories\Base\BarangaySmsTransactionRepository;

class SendBarangaySmsRecipient implements ShouldQueue
{
    use Dispatchable, SerializesModels;

    public $smsRecipient;

    public $smsTransactionRepository;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(BarangaySmsRecipient $smsRecipient)
    {
        $this->smsRecipient = $smsRecipient;

        $this->smsTransactionRepository = new BarangaySmsTransactionRepository;
    }

    public function handle()
    {
        $this->smsTransactionRepository->sendSmsRecipient($this->smsRecipient);
    }
}
