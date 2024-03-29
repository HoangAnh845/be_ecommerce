<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;

Route::middleware(['auth:api'])->group(function () {
    Route::controller(ChatController::class)->group(function () {
        Route::get('chats', 'index'); // Lấy đoạn chat của người dùng và người nhận
        Route::get('chats/{id}', 'show');
        Route::post('chats', 'store');
        Route::delete('chats/{id}', 'destroy');
    });
});
