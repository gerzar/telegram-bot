<?php

use App\Http\Controllers\Admin\DictionaryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('dictionary', DictionaryController::class);
Route::post('search', [DictionaryController::class, 'search'])->name('search');