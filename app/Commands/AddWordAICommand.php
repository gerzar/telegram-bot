<?php

namespace App\Commands;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

class AddWordAICommand extends Command
{
    protected string $name = 'add_word';
    protected string $description = 'Добавить слово или выражение в словарик.';

    public function handle()
    {
        $this->replyWithChatAction(['action' => Actions::TYPING]);
        Cache::delete($this->getUpdate()->getMessage()->chat->id);
        Cache::put($this->getUpdate()->getMessage()->chat->id, $this->name);

        $this->replyWithMessage([
            'text' => 'Напиши мне слово или выражение на русском или английском языке и оно добавиться тебе в словарик!',
        ]);
    }
}