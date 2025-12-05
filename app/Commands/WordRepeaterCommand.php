<?php

namespace App\Commands;

use App\Models\Dictionary;
use App\Models\UsersDictionary;
use App\Services\RepeatWordsService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Keyboard\Keyboard;

class WordRepeaterCommand extends Command
{
    protected string $name = 'repeat_words';
    protected string $description = 'Повторить слова';


    public function handle()
    {
        $this->replyWithChatAction(['action' => Actions::TYPING]);
        // Log::info();
        $chat_id = $this->getUpdate()->getMessage()->chat->id;
        Cache::delete($chat_id);
        Cache::put($chat_id, $this->name);

        $repeatWordsService = new RepeatWordsService;
        $result = $repeatWordsService->service($chat_id);
        
        if(isset($result['sound'])){
            $this->replyWithAudio([
                'chat_id' => $chat_id,
                'caption' => $result['word'],
                'audio' => InputFile::create($result['sound'], $result['word']),
                'reply_markup' => $result['keyboard'],
            ]);
        }else{
            $this->replyWithMessage([
                'text' => $result['word'],
                'reply_markup' => $result['keyboard']
            ]);
        }        

    }
}