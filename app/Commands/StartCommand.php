<?php

namespace App\Commands;

use Illuminate\Support\Facades\Cache;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

class StartCommand extends Command
{
    protected string $name = 'start';
    protected string $description = 'Команда для моего запуска';

    public function handle()
    {
        Cache::delete($this->getUpdate()->getMessage()->chat->id);
        $this->replyWithChatAction(['action' => Actions::TYPING]);

        $commands = $this->getTelegram()->getCommands();

        $response = '';
        foreach ($commands as $name => $command) {
            $response .= sprintf('/%s - %s' . PHP_EOL, $name, $command->getDescription());
        }

        $this->replyWithMessage([
            'text' => 'Привет! Вот список моих команд:'. PHP_EOL.$response . PHP_EOL.'Я ещё пока нахожусь в стадии разработки, так что не ожидайте от меня многого',
        ]);
    }
}