<?php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SmsController;
use Illuminate\Support\Facades\Route;

Route::prefix('projects')->middleware('admin_token')->group(function () {
    Route::get('/', [ProjectController::class, 'index']);
    Route::post('/', [ProjectController::class, 'store']);
    Route::put('/{project}', [ProjectController::class, 'update']);
    Route::delete('/{project}', [ProjectController::class, 'destroy']);
});

Route::prefix('sms')->middleware('api_key')->group(function () {
    Route::post('/send', [SmsController::class, 'send']);
    Route::get('/history', [SmsController::class, 'history']);
});
