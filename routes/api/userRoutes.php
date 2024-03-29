<?php

use App\Http\Controllers\User\BookAddressController;
use App\Http\Controllers\User\NoticeController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;


Route::controller(UserController::class)->group(function () {
    Route::get('users', 'index');
    Route::post('register', 'store')->name('register');
    Route::middleware(['auth:api'])->group(function () { // 'check.signature'
        Route::get('users/search', 'filter');
        Route::get('users/{id}', 'show');
        Route::put('users/{id}', 'update');
        Route::delete('users/{id}', 'destroy');
    });
});

Route::middleware(['auth:api'])->group(function () {
    Route::controller(BookAddressController::class)->group(function () {
        Route::get('book-addresses/user/{id}', 'show');
        Route::get('book-addresses/accompany ', 'showAccompany'); // Địa chỉ đi kèm
        Route::post('book-address', 'store');
        Route::put('book-addresses/{id}', 'update');
        Route::delete('book-addresses/{id}', 'destroy');
    });
    
    Route::controller(NoticeController::class)->group(function () {
        Route::get('notices', 'index');
        Route::get('notices/user/{id}', 'filter');
        Route::post('notices/sender', 'sender');
        Route::post('notices', 'store');
        Route::put('notices/{id}', 'update');
        Route::delete('notices/{id}', 'destroy');
    });
});
