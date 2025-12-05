<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\CheckWordNotificationJob;
use App\Models\UserSetting;
use Illuminate\Http\Request;

class CronJobController extends Controller
{
    public function pickJob()
    {
        $userSetting = new UserSetting();
        dispatch(new CheckWordNotificationJob($userSetting));
    }
}
