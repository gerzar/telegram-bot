<?php

namespace App\Jobs;

use App\Models\UserSetting;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class CheckWordNotificationJob implements ShouldQueue
{
    use Queueable;


    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Получаем текущее время в формате H:i
        $currentTime = now('Europe/Moscow')->format('H:i');
        Log::info([$currentTime]);
        $userSetting =  new UserSetting();



        // Получаем уведомления, соответствующие времени
        $notifications = $userSetting->where('repetition_time', $currentTime)->get();

        // Если уведомления есть
        if ($notifications->isNotEmpty()) {
            foreach ($notifications as $notify) {
                // Создаем задание для отправки уведомления
                dispatch(new SendRepeatWordNotificationJob($notify->chat_id));

            }
        }
    }
}

