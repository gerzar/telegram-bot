<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;

class SendRepeatWordNotificationJob implements ShouldQueue
{
    use Queueable;
    public $chat_id;
    public $telegram;
    /**
     * Create a new job instance.
     */
    public function __construct(int $chat_id)
    {
        $this->chat_id = $chat_id;
        $this->telegram = new Api();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->telegram->sendMessage([
            'chat_id' => $this->chat_id,
            'text' => 'Повтори пожалуйста слова /repeat_words',
            'parse_mode' => 'HTML',
        ]);
    }
}
