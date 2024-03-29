<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DiscountController;

Route::middleware(['auth:api'])->group(function () {
    Route::controller(DiscountController::class)->group(function(){
        Route::get('discounts', 'index');
        Route::get('discounts/{id}', 'show');
        Route::post('discounts', 'store');
        Route::put('discounts/{id}', 'update');
        Route::delete('discounts/{id}', 'destroy');
    
        Route::post('discounts/share/{id}', 'createDiscountShare');
        Route::delete('discounts/share/{id}', 'deleteDiscountShare');
    });
});