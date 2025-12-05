<?php

namespace App\Actions;

use App\Models\Dictionary;
use App\Models\UsersDictionary;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Actions;
use Telegram\Bot\Api;
use Telegram\Bot\FileUpload\InputFile;

class AddWordAction extends BotActions
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

        if(str_word_count($update->message->text) > 5){

            $this->telegram->sendMessage([
                'chat_id' => $update->message->chat->id,
                'text' => 'Слишком много слов, попробуй /translate',
                'parse_mode' => 'HTML',
            ]);

        }else{

            $dictionary = new Dictionary();
            $word = $dictionary->where('word', mb_strtolower($update->message->text))->first();
            $forwardMessage = '';
    
            if (!$word) {

                $wordInfo = $this->translate($update->message->text);

                // Log::info(dump($wordInfo));
                $forwardMessage = '<b>'.$wordInfo['word'].'</b> - '.$wordInfo['translations'].PHP_EOL.PHP_EOL;
                $forwardMessage .= ($wordInfo['definitions']) ?'<b>Определения: </b>'.PHP_EOL.$wordInfo['definitions']: '<b>Определения: </b> Нет определения такому слову';
                $forwardMessage .= ($wordInfo['examples']) ? '<b>Примеры: </b>'.PHP_EOL.$wordInfo['examples']: '';

                if($wordInfo['definitions']){
                    $word = $dictionary->firstOrCreate(
                        [
                            'word' => mb_strtolower($wordInfo['word'])
                        ],
                        [
                            'translation' => $wordInfo['translations'],
                            'definition' => $wordInfo['definitions'],
                            'example' => $wordInfo['examples'],
                            'sound' => $wordInfo['sound'],
                        ]
                    );
                }

            }else{
                $forwardMessage = '<b>'.$word->word.'</b> - '.$word->translation.PHP_EOL.PHP_EOL;
                $forwardMessage .= ($word->definition) ?'<b>Определения: </b>'.PHP_EOL.$word->definition: '<b>Определения: </b> Нет определения такому слову';
                $forwardMessage .= ($word->example) ? '<b>Примеры: </b>'.PHP_EOL.$word->example: '';
            }


            if(isset($word->id)){ //добавить слово в словарик для пользователя, если слово существует

                $userDictionary = new UsersDictionary();
                $userDictionary->firstOrCreate(
                    [
                        'word_id' => $word->id,
                        'chat_id' => $update->message->chat->id,
                    ],
                    [
                        'last_check' => date('Y-m-d H:i:s'),
                        'success' => 0,
                        'fails' => 0,
                        'learned' => 0,
                    ]
                );
            }
            
            if(isset($word->sound)){
                $this->telegram->sendAudio([
                    'chat_id' => $update->message->chat->id,
                    'caption' => $forwardMessage,
                    'audio' => InputFile::create($word->sound, $word->word),
                    'parse_mode' => 'HTML',
                ]);
            }else{
                $this->telegram->sendMessage([
                    'chat_id' => $update->message->chat->id,
                    'text' => $forwardMessage,
                    'parse_mode' => 'HTML',
                ]);
            }

            

            

        }
    }




}