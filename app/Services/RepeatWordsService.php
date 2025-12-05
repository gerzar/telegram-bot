<?php

namespace App\Services;

use App\Models\Dictionary;
use App\Models\UsersDictionary;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Keyboard\Keyboard;

class RepeatWordsService 
{
    public function service($chat_id)
    {
        $success_cap = env('SUCCESS_CAP');
        $success_word_time = env('SUCCESS_WORD_TIME');
        $word = UsersDictionary::where('chat_id', $chat_id)
            ->where('fails',  0)
            ->where('success', 0)
            ->inRandomOrder()
            ->with('word')->first();

        if(!$word){
            $word = UsersDictionary::where('chat_id', $chat_id)
                ->where('last_check', '<', date('Y-m-d H:i:s', strtotime('-'.env('FAILED_WORD_TIME').' hours'))) //now much time bettwen failed tries
                ->where('fails', '>', 0)
                ->inRandomOrder()
                ->with('word')->first();
        }
        

        if(!$word){
            $word = UsersDictionary::where('chat_id', $chat_id)
            ->where('last_check', '<', date('Y-m-d H:i:s', strtotime('-'.$success_word_time.' hours'))) //now much time bettwen successful tries
            ->where('success', '<', round($success_cap/3)) //how many success tries user had
            ->inRandomOrder()
            ->with('word')->first();
        }

        if(!$word){
            $word = UsersDictionary::where('chat_id', $chat_id)
            ->where('last_check', '<', date('Y-m-d H:i:s', strtotime('-'.($success_word_time*3).' hours'))) //now much time bettwen successful tries
            ->where('success', '>', round($success_cap/3)) //how many success tries user had
            ->where('success', '<', round($success_cap/3)*2)
            ->inRandomOrder()
            ->with('word')->first();
        }

        if(!$word){
            $word = UsersDictionary::where('chat_id', $chat_id)
            ->where('last_check', '<', date('Y-m-d H:i:s', strtotime('-'.($success_word_time*6).' hours'))) //now much time bettwen successful tries
            ->where('success', '>', round($success_cap/3)*2) //how many success tries user had
            ->where('success', '<', $success_cap+1) //количество успешных попыток +1
            ->inRandomOrder()
            ->with('word')->first();
        }

        if(!$word){
            return ['word' => 'Нет слов в словарике или ещё слишком рано для повторения /add_word', 'keyboard' => Keyboard::remove(['selective' => false])];
        }


        $fillWords = Dictionary::where('id','!=',$word->word_id)->inRandomOrder()->limit(3)->get();
        if(!isset($fillWords[2])){
            exit;
        }
        $answers = [
            ['word' => $word->word->translation, 'true' => 1],
            ['word' => $fillWords[0]->translation, 'true' => 0],
            ['word' => $fillWords[1]->translation, 'true' => 0],
            ['word' => $fillWords[2]->translation, 'true' => 0],
        ];
        $values = array_values($answers);
        shuffle($values);
        $answers = array_combine(array_keys($answers), $values);

        $keyboard = Keyboard::make()
            ->inline()
            ->row([
                Keyboard::button([
                    'text' => $answers[0]['word'],
                    'callback_data' => $chat_id . '|repeat_words|' .$word->id.'|'. $answers[0]['true']
                ]),
                Keyboard::button([
                    'text' => $answers[1]['word'],
                    'callback_data' => $chat_id . '|repeat_words|' .$word->id.'|'. $answers[1]['true']
                ])
            ])
            ->row([
                Keyboard::button([
                    'text' => $answers[2]['word'],
                    'callback_data' => $chat_id . '|repeat_words|' .$word->id.'|'. $answers[2]['true']
                ]),
                Keyboard::button([
                    'text' => $answers[3]['word'],
                    'callback_data' => $chat_id . '|repeat_words|' .$word->id.'|'. $answers[3]['true']
                ])
            ]);
        
        if(isset($word->word->sound)){
            return ['word' => $word->word->word, 'sound'=> $word->word->sound,'keyboard' => $keyboard];
        }else {
            return ['word' => $word->word->word, 'keyboard' => $keyboard];
        }
        
    }
}