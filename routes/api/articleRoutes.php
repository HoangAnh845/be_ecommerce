<?php

use App\Http\Controllers\Article\ArticleController;
use App\Http\Controllers\Article\MenuController;
use App\Http\Controllers\Article\CommentController;
use App\Http\Controllers\Article\FavouriteController;

use Illuminate\Support\Facades\Route;

Route::middleware(['auth:api'])->group(function () {
    Route::controller(MenuController::class)->group(function () {
        Route::post('menus', 'store')->name('menu.create');
        Route::put('menus/{id}', 'update')->name('menu.update');
        Route::delete('menus/{id}', 'destroy')->name('menu.destroy');
    });

    Route::controller(ArticleController::class)->group(function () {
        Route::get('articles', 'index')->name('article.index');
        Route::get('articles/{id}', 'show')->name('article.show');
        Route::post('articles', 'store')->name('article.create');
        Route::put('articles/{id}', 'update')->name('article.update');
        Route::delete('articles/{id}', 'destroy')->name('article.destroy');
    });
    Route::controller(FavouriteController::class)->group(function () {
        Route::get('favourites/article/{id}', 'show')->name('favourite.show');
        Route::post('favourites/article', 'store')->name('favourite.create');
        Route::delete('favourites/article/{id}', 'destroy')->name('favourite.destroy');
    });

    Route::controller(CommentController::class)->group(function () {
        Route::get('comments/{id}', 'show')->name('comment.show');
        Route::post('comments', 'store')->name('comment.create');
        Route::delete('comments/{id}', 'destroy')->name('comment.destroy');
    });
});
