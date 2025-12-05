<?php

namespace App\Commands;

use App\Models\UsersDictionary;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

class UnlearnedWordsCommand extends Command
{
    protected string $name = 'learn_list';
    protected string $description = 'Список слов которые ещё не выучил';

    public function handle()
    {
        $chat_id = $this->getUpdate()->getMessage()->chat->id;
        Cache::delete($chat_id);
        $this->replyWithChatAction(['action' => Actions::TYPING]);

        $words = UsersDictionary::with('word')->where('chat_id', $chat_id)->where('success', '<', 10)->paginate(20);

        
        $response = '';
        if(!$words->isEmpty()){
            foreach ($words as $word) {

                

                if($word->fails > 0){
                    $period = strtotime('-'.env('FAILED_WORD_TIME').' hours');
                }else{
                    $period = strtotime('-'.env('SUCCESS_WORD_TIME').' hours');
                }

                if($word->success === 0 && $word->fails === 0){
                    $period = 0;
                }
                
                $last_check = strtotime($word->last_check);

                if($last_check - $period <= 0){
                    $hoursLeft = '<i>Повторяй!</i>';
                }else{
                    $hoursLeft = '⏳'.date('Hчiм', $last_check - $period);
                }

                $response .= '<b>'.$word->word->word.'</b>'. ' - '.$word->word->translation. ' - '.$hoursLeft.  PHP_EOL;
            }
        }else {
            $response = 'Список пока пуст, добавь слов /add_word';
        }

        $this->replyWithMessage([
            'text' => $response,
            'parse_mode' => 'HTML'
        ]);
    }
}