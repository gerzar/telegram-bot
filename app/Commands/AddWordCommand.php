<?php

namespace App\Commands;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

class AddWordCommand extends Command
{
    protected string $name = 'add_word_closed';
    protected string $description = 'Добавить одно слово в словарик';

    public function handle()
    {
        $this->replyWithChatAction(['action' => Actions::TYPING]);
        // Log::info();
        Cache::delete($this->getUpdate()->getMessage()->chat->id);
        Cache::put($this->getUpdate()->getMessage()->chat->id, $this->name);

        $this->replyWithMessage([
            'text' => 'Напиши мне слово и я переведу его тебе, а также сохраню на будущее!',
        ]);
    }
}