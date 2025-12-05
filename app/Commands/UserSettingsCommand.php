<?php

namespace App\Commands;

use Illuminate\Support\Facades\Cache;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

class UserSettingsCommand extends Command
{
    protected string $name = 'settings';
    protected string $description = 'Настрой меня для себя!';

    public function handle()
    {
        Cache::delete($this->getUpdate()->getMessage()->chat->id);
        Cache::put($this->getUpdate()->getMessage()->chat->id, $this->name);
        $this->replyWithChatAction(['action' => Actions::TYPING]);

        
        $this->replyWithMessage([
            'text' => '<b>Пожалуйста заполни свои настройки для меня.</b>'.PHP_EOL. PHP_EOL. 
            '- Заполнять нужно в формате <b>название - значение.</b>'.PHP_EOL. 
            '- Нужно заполнить время когда тебе будет максимально удобно повторять слова. <b>time - 17:30;</b>'. PHP_EOL. 
            '- А ещё укажи количество слов которое <b>ты хочешь повторять за один раз</b>, значение должно быть <b>не меньше 10</b>. <b>words - 15;</b>',
            'parse_mode' => 'HTML'
        ]);
    }
}