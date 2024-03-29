<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Order\OrderController;
use App\Http\Controllers\Order\PaymentController;
use App\Http\Controllers\Order\TransactionController;
use App\Models\Payment;

Route::middleware(['auth:api'])->group(function () {
    Route::controller(OrderController::class)->group(function () {
        Route::get('orders/user/{id}', 'show');
        Route::post('orders', 'store'); 
    });
    
    Route::post('payments', [PaymentController::class, 'store']);
    Route::controller(TransactionController::class)->group(function () {
        Route::put('transactions/{id}', 'update');
        Route::post('transactions/{id}', 'destroy');
    });
});
