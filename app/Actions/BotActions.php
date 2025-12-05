<?php

namespace App\Actions;

use App\Models\Dictionary;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Actions;
use Telegram\Bot\Api;
use Telegram\Bot\Laravel\Facades\Telegram;

class BotActions 
{
    protected $guzzle;

    public function __construct()
    {
        $this->guzzle = new Client([
            'proxy' => [
                'http'  => 'http://gggzzzrrr:2653552234Trall@us-ca.proxymesh.com:31280',  
                'https' => 'http://gggzzzrrr:2653552234Trall@us-ca.proxymesh.com:31280',  
            ]
        ]);

    }

    public function translate($text){

        $response = $this->guzzle->get('https://ftapi.pythonanywhere.com/translate?sl=en&dl=ru&text=' . urlencode($text));
        $result = json_decode($response->getBody()->getContents(), true);
        
        $definitions = '';
        $translations = '';
        $examples = '';
        $word = '';
        $sound = '';

        foreach ($result['translations']['possible-translations'] as $translate) {
            $translations .= $translate . ', ';
        }

        if(isset($result['definitions'][0]['definition']))
        {
            foreach($result['definitions'] as $index => $defenition)
            {
                $definitions .= $this->smallTranslate($defenition['definition']).PHP_EOL.PHP_EOL;

                if(!isset($definitions)){
                    return null;
                }

                if(isset($defenition['example'])){
                    $examples .= $defenition['example'].PHP_EOL.PHP_EOL;
                }
                if($index === 2){
                    break;
                }

            }
        }
        $word = $text;
        if(isset($result['translations']['possible-mistakes'][0])){
            $word = $result['translations']['possible-mistakes'][1];
        }
        
        if(isset($result['pronunciation']['source-text-audio'])){
            $sound = $result['pronunciation']['source-text-audio'];
        }
    

        return [
            'word' => $word,
            'definitions' => $definitions,
            'translations' => rtrim($translations, ', '),
            'examples' => $examples,
            'sound' => $sound,
        ];


    }

    protected function translateAi($word)
    {
        $messages = 
        'Переведи с английского это слово или выражение: "' . $word. '" ответ мне дай в массиве, где будут отображены следующие пункты:
        "word" - "English version of word or expression",
        "translation" - "translation in Russian",
        "popular_translations" -"variant_1","variant_2","variant_3".
        "definitions" - "Definition of variant 1 in Russian.","Definition of variant 2 in Russian.","Definition of variant 3 in Russian.",
        "examples" - "Example of variant 1 in English","Example of variant 2 in English","Example of variant 3 in English,
        "popularity" - from 0 to 100,
        Оцени по шкале от 0 до 100, насколько часто встречается выражение или слово "' . $word. '" в современной повседневной речи носителей английского языка? дай мне в ответе только число и больше никаких разъяснений не нужно и впиши это значение в popularity

        если полученный текст выглядит как набор букв, то отправь в поле translation NULL.
        ';

        try {
            $response = $this->guzzle->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-8b:generateContent', [
                'query' => ['key' => env('GEMINI_API_KEY')], // Ваш API ключ
                'json' => [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $messages]
                            ]
                        ]
                    ]
                ]
            ]);

            // Log::info([$messages]);

        } catch (RequestException $e) {

            Log::error('Guzzle error: ', ['exception' => $e->getMessage()]);
            if ($e->hasResponse()) {
                return json_decode($e->getResponse()->getBody(true), true);
            }
            throw $e; 
        }

        $response = json_decode($response->getBody()->getContents())->candidates;

        // Извлекаем текст ответа
        $response = $response[0]->content->parts[0]->text;

        // Удаляем лишние символы (например, троичные кавычки и пробелы)
        $responseText = preg_replace('/^```json|\s*$/', '', $response); // Убираем начальные и конечные лишние символы
        $responseText = preg_replace('/```/', '', $responseText); // Убираем все остальное, если есть дополнительные ```

        // Log::info([$responseText]);
        return json_decode($responseText, true);
    }

    protected function smallTranslate($text){
        $langs = $this->detectLanguage($text);
        $url = 'https://ftapi.pythonanywhere.com/translate?sl=' . $langs['from'] . '&dl=' . $langs['to'] . '&text=' . urlencode($text);
        
        try {
            $response = $this->guzzle->get($url);
            $responseBody = json_decode($response->getBody()->getContents(), true);
    
            if (isset($responseBody['translations']['possible-translations'])) {
                return $responseBody['translations']['possible-translations'][0];
            }
        } catch (\Exception $e) {
            // Обработка ошибки, если запрос не удался
            // Логируем ошибку или уведомляем пользователя
        }
        
        // Повторный запрос через секунду в случае ошибки
        sleep(1);
        try {
            $response = $this->guzzle->get($url);
            $responseBody = json_decode($response->getBody()->getContents(), true);
            
            if (isset($responseBody['translations']['possible-translations'])) {
                return $responseBody['translations']['possible-translations'][0];
            }
        } catch (\Exception $e) {
            // Логируем или обрабатываем повторную ошибку
        }
        
        return null; // Если перевод не получен
    }

    protected function detectLanguage($word) {
        
        if (preg_match('/[а-яА-ЯЁё]/u', $word)) {
            return ['from' => 'ru', 'to' => 'en'];  
        }
        return  ['from' => 'en', 'to' => 'ru'];   
    }

}
