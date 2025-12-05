<?php

namespace App\Actions;

use App\Models\Dictionary;
use App\Models\UserSetting;
use DateTime;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Actions;
use Telegram\Bot\Api;
use Telegram\Bot\Laravel\Facades\Telegram;

class UserSettings extends BotActions
{
    protected $telegram;
    

    public function __construct(Api $telegram)
    {
        $this->telegram = $telegram;
    }

    public function action($update)
    {
        
        $text = $update->message->text;
        $text = str_replace(' ', '', $text);
        $settingsArray = explode(';',$text);
        $settingsArray = array_filter($settingsArray);
        $settingsFinal = [];
        $key_words = ['words','time'];

        $answer = '';
        foreach($settingsArray as $set){
            $buffer = explode('-', $set);

            if(!in_array($buffer[0], $key_words)){
                $answer = 'Неверный формат ответа должен быть words - 10; time - 14:30;';
                $this->sendMessage($update, $answer);
                exit;
            }

            if($buffer[0] === 'words'){
                $buffer[1] = (int)$buffer[1];
                if($buffer[1] < 10){
                    $answer = 'Вы указали неправильное число слов, должно быть больше 10';
                    $this->sendMessage($update, $answer);
                    exit;
                }
            }

            if($buffer[0] === 'time'){
                $date = DateTime::createFromFormat('H:i', $buffer[1]);
                if (!$date || $date->format('H:i') !== $buffer[1]) {
                    $answer = 'Вы указали время не в правильном формате должен быть например 14:30';
                    $this->sendMessage($update, $answer);
                    exit;
                }
            }

            $settingsFinal[$buffer[0]] = $buffer[1];
        }

        // Log::info($settingsFinal);

        $user = UserSetting::updateOrCreate(
            ['chat_id' => $update->message->chat->id], // Find by chat_id
            ['repetition_time' => $settingsFinal['time'], 'words_per_day' => $settingsFinal['words']]  // Update or create these fields
        );

        if($user){
            $answer = 'Настройки сохранены. '.PHP_EOL.'Повтор слов в: '. $user->repetition_time .PHP_EOL.'Количество слов за один раз: '. $user->words_per_day;
            
        }else{
            $answer = 'Что-то пошло не так, попробуйте ещё раз, пожалуйста';
        }
        $this->sendMessage($update, $answer);
    }

    public function sendMessage($update, $answer)
    {
        $this->telegram->sendMessage([
            'chat_id' => $update->message->chat->id,
            'text' => $answer,
            'parse_mode' => 'HTML',
        ]);
    }

}