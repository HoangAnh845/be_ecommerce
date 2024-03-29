<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Product\KeywordController;

Route::middleware(['auth:api'])->group(function () {
    Route::controller(KeywordController::class)->group(function () {
        Route::get('keywords', 'index')->name('keyword.index');
        Route::post('keywords', 'store')->name('keyword.create');
        Route::put('keywords/{id}', 'update')->name('keyword.update');
        Route::delete('keywords/{id}', 'destroy')->name('keyword.destroy');
        Route::get('keywords/search', 'filter')->name('keyword.filter');
    });
 });
