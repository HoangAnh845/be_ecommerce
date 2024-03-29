<?php

use App\Http\Controllers\Product\FavouriteController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Product\ReviewController;


Route::middleware(['auth:api'])->group(function () {
    Route::controller(ProductController::class)->group(function () {
        Route::get('products','index')->name('product.index');
        Route::get('products/search','filter')->name('product.search');
        Route::get('products/{id}','show')->name('product.get');
        Route::post('products','store')->name('product.store');
        Route::put('products/{id}','update')->name('product.update');
        Route::delete('products/{id}','destroy')->name('product.destroy');
    });
    
    Route::controller(FavouriteController::class)->group(function () {
        Route::get('favourites/{id}','show')->name('favourite.show');
        Route::post('favourites','store')->name('favourite.create');
        Route::delete('favourites/{id}','destroy')->name('favourite.destroy');
    });
    
    Route::controller(ReviewController::class)->group(function () {
        Route::get('reviews/{id}','show')->name('review.show');
        Route::post('reviews','store')->name('review.create');
        Route::delete('reviews/{id}','destroy')->name('review.destroy');
    });
});

