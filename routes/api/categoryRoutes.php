<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Product\CategoryController;

Route::controller(CategoryController::class)->group(function () {
    Route::get('categorys','index')->name('category.index');
    Route::get('categorys/{id}','show')->name('category.show');
    Route::post('categorys','store')->name('category.create');
    Route::put('categorys/{id}','update')->name('category.update');
    Route::delete('categorys/{id}','destroy')->name('category.destroy');
    Route::post('categorys/search/{id}','filter')->name('category.search');
});
