<?php

namespace App\Commands;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

class TranslateCommand extends Command
{
    protected string $name = 'translate';
    protected string $description = 'Перевести любой текст';

    public function handle()
    {
        $this->replyWithChatAction(['action' => Actions::TYPING]);
        // Log::info();
        Cache::delete($this->getUpdate()->getMessage()->chat->id);
        Cache::put($this->getUpdate()->getMessage()->chat->id, $this->name);

        $this->replyWithMessage([
            'text' => 'Напиши что угодно и я тебе переведу!',
        ]);
    }
}