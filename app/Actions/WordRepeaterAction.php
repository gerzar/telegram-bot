<?php

namespace App\Actions;

use App\Models\Dictionary;
use App\Models\UsersDictionary;
use App\Models\UserSetting;
use App\Services\RepeatWordsService;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Actions;
use Telegram\Bot\Api;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;

class WordRepeaterAction extends BotActions
{
    protected $telegram;
    

    public function __construct(Api $telegram)
    {
        parent::__construct();
        $this->telegram = $telegram;
        
    }

    public function action($chat_id)
    {
        $this->telegram->sendChatAction([
            'chat_id' => $chat_id,
            'action'  => Actions::TYPING,
        ]);

        $userAnswer = Cache::get('repeat_words'.$chat_id);
        
        $userDictionary = UsersDictionary::find($userAnswer['word_id']);

        $userDictionary->last_check = date('Y-m-d H:i:s');
        $correctWord = Dictionary::find($userDictionary->word_id);

        if($userAnswer['is_correct']){
            $answer = 'Правильно - <b>'.$correctWord->word.'</b> это <b>'.$correctWord->translation.'</b>';
        }else{
            $answer = 'Ошибочка - <b>'.$correctWord->word.'</b> это <b>'.$correctWord->translation.'</b>';
        }

        

        if($userAnswer['is_correct']){
            $userDictionary->success +=1;
            $userDictionary->fails = 0;
        }else {
            $userDictionary->fails +=1;
            $userDictionary->success = ($userDictionary->success > 1) ? $userDictionary->success - 1 : 0;
        }

        $userDictionary->save();

        Telegram::sendMessage([
            'chat_id' => $chat_id,
            'text' => $answer,
            'parse_mode' => 'HTML'
        ]);

        $this->telegram->sendChatAction([
            'chat_id' => $chat_id,
            'action'  => Actions::TYPING,
        ]);

        
        if($usettings = UserSetting::where('chat_id', $chat_id)->first()){

            if(null === Cache::get('repeat_words_count'.$chat_id)){
                Cache::put('repeat_words_count'.$chat_id, 0);
            }

            if(Cache::get('repeat_words_count'.$chat_id) > $usettings->words_per_day){
                Telegram::sendMessage([
                    'chat_id' => $chat_id,
                    'text' => 'Вы повторили '.$usettings->words_per_day. ' слов. Но у вас остались ещё не повторённые слова, продолжим? /repeat_words',
                    'parse_mode' => 'HTML'
                ]);
                Cache::delete('repeat_words_count'.$chat_id);
                exit;
            }

            $words_count = Cache::get('repeat_words_count'.$chat_id)+1;
            Cache::put('repeat_words_count'.$chat_id, $words_count);
        }

        $repeatWordService = new RepeatWordsService;
        $result = $repeatWordService->service($chat_id);
        

        if(isset($result['sound'])){
            Telegram::sendAudio([
                'chat_id' => $chat_id,
                'caption' => $result['word'],
                'audio' => InputFile::create($result['sound'], $result['word']),
                'reply_markup' => $result['keyboard'],
            ]);
        }else{
            Telegram::sendMessage([
                'chat_id' => $chat_id,
                'text' => $result['word'],
                'reply_markup' => $result['keyboard']
            ]);
        }
        

        
    }

}