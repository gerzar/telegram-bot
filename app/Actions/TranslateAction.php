<?php

namespace App\Actions;

use App\Models\Dictionary;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Actions;
use Telegram\Bot\Api;
use Telegram\Bot\Laravel\Facades\Telegram;

class TranslateAction extends BotActions
{
    protected $telegram;
    

    public function __construct(Api $telegram)
    {
        parent::__construct();
        $this->telegram = $telegram;
        
    }

    public function action($update)
    {
        $this->telegram->sendChatAction([
            'chat_id' => $update->message->chat->id,
            'action'  => Actions::TYPING,
        ]);

        $result = $this->smallTranslate($update->message->text);
        $this->telegram->sendMessage([
            'chat_id' => $update->message->chat->id,
            'text' => $result,
            'parse_mode' => 'HTML',
        ]);
    }

}