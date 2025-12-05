<?php

use App\Http\Controllers\Api\CronJobController;
use App\Http\Controllers\Api\TelegramBotController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Route::post('/telegram/webhook', [TelegramBotController::class, 'getMessage']);
Route::post('/telegram/webhook', [TelegramBotController::class, 'handleCallbackQuery']);
Route::get('/cronjob/pick', [CronJobController::class, 'pickJob']);
