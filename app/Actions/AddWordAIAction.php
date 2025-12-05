<?php

namespace App\Actions;

use App\Models\Dictionary;
use App\Models\UsersDictionary;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Actions;
use Telegram\Bot\Api;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Telegram\Bot\FileUpload\InputFile;

class AddWordAIAction extends BotActions
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

            exit;

        }else{

            $dictionary = new Dictionary();
            $word = $dictionary->where('word', mb_strtolower($update->message->text))->first();
            $forwardMessage = '';
    
            if (!$word) {

                $word = $update->message->text;

                if($this->detectLanguage($update->message->text)['from'] === 'ru'){
                    $word = $this->smallTranslate($update->message->text);
                }
        
        
                $wordInfo = $this->translateAi($word);
                sleep(1);

                if(!isset($wordInfo['word'])){
                    sleep(1);
                }

                if(!isset($wordInfo['word'])){
                    Log::info('Answer from API error: ',[$wordInfo]);
                    exit;                    
                }
                
                $translations = '';
                $definitions = '';
                $examples = '';
                $sound = 'https://translate.google.com/translate_tts?ie=UTF-&&client=tw-ob&tl=en&q='.str_replace(' ', '%20', $wordInfo['word']);

                if($wordInfo['translation'] !== NULL && $wordInfo['popular_translations'] !== NULL && $wordInfo['definitions'] !== NULL){
                    foreach($wordInfo['popular_translations'] as $ptranslate){
                        $translations .= $ptranslate.', ';
                    }

                    foreach($wordInfo['definitions'] as $index => $definition){
                        $definitions .= ($index+1).'. '.$definition.PHP_EOL;
                    }

                    foreach($wordInfo['examples'] as $index => $example){
                        $examples .= ($index+1).'. '.$example.PHP_EOL;
                    }
                }
                $translations = rtrim($translations, ', ');
                // Log::info(dump($wordInfo));
                $forwardMessage = '<b>'.$wordInfo['word'].'</b> - '.$translations.PHP_EOL.PHP_EOL;
                $forwardMessage .= ($definitions) ?'<b>Определения: </b>'.PHP_EOL.$definitions: '<b>Определения: </b> Нет определения такому слову';
                $forwardMessage .= ($examples) ? '<b>Примеры: </b>'.PHP_EOL.$examples: '';

                if($wordInfo['definitions']){
                    $word = $dictionary->firstOrCreate(
                        [
                            'word' => mb_strtolower($wordInfo['word'])
                        ],
                        [
                            'translation' => $translations,
                            'definition' => $definitions,
                            'example' => $examples,
                            'sound' => $sound,
                            'popularity' => (int)$wordInfo['popularity'],
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
        }

        
        if(isset($word->sound)){
            $this->telegram->sendAudio([
                'chat_id' => $update->message->chat->id,
                'caption' => $forwardMessage,
                'audio' => InputFile::create('https://translate.google.com/translate_tts?ie=UTF-&&client=tw-ob&tl=en&q='.str_replace(' ', '%20', $word->word), $word->word),
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
