<?php

namespace App\Http\Controllers\Api;

use App\Commands\WordRepeaterCommand;
use App\Http\Controllers\Controller;
use App\Models\Dictionary;
use App\Models\UsersDictionary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Commands\HelpCommand;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramBotController extends Controller
{
    private $actions;
    private $callback_actions;
    public function __construct()
    {
        $this->actions = [
            'add_word' => 'App\Actions\AddWordAIAction',
            'translate' => 'App\Actions\TranslateAction',
            'settings' => 'App\Actions\UserSettings',
        ];

        $this->callback_actions = [
            'repeat_words' => 'App\Actions\WordRepeaterAction',
        ];

    }

    public function handleCallbackQuery(Request $request)
    {
        if ($data = $request->input('callback_query.data')) {
            // Log::info('1');
            list($chat_id, $callback_command, $word_id, $is_correct) = explode('|', $data);

            Cache::put($callback_command.$chat_id, ['word_id' => $word_id, 'is_correct' => $is_correct]);
            app($this->callback_actions[$callback_command])->action($chat_id);
            exit;
        }



        $update = Telegram::commandsHandler(true);
        if(isset($update->message->chat)){
            $action = Cache::get($update->message->chat->id);
        }
        
        
        if(isset($action) && isset($this->actions[$action]) && strpos($update->message->text, '/') === false){
            app($this->actions[$action])->action($update);
        }
    }


}