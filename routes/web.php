<?php

use App\Http\Controllers\GamblingController;
use Illuminate\Support\Facades\Route;

// 首頁重新導向到賭盤列表
Route::get('/', function () {
    return redirect()->route('gamblings.index');
});

// 賭盤相關路由
Route::resource('gamblings', GamblingController::class)
    ->only(['index', 'create', 'store']);
