<?php

use App\Http\Controllers\Article\ArticleController;
use App\Http\Controllers\Article\CommentController;
use App\Http\Controllers\Article\MenuController;
use App\Http\Controllers\Product\ProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\ApiAuthController;
use App\Http\Controllers\API\User\ApiUserController;
use App\Http\Controllers\Order\OrderController;
use App\Http\Controllers\Product\CategoryController;
use App\Http\Controllers\Product\FavouriteController as ProductFavouriteController;
use App\Http\Controllers\Article\FavouriteController as ArticleFavouriteController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\Order\PaymentController;
use App\Http\Controllers\Order\TransactionController;
use App\Http\Controllers\Product\ReviewController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::group(['prefix' => 'v1/admin'], function () {
    Route::group(['prefix' => 'auth'], function () {
        Route::post('login', [ApiAuthController::class, 'login']);
    });

    Route::group(['prefix' => 'user', 'middleware' => 'auth:api'], function () {
        Route::post('logout', [ApiAuthController::class, 'logout']);

        Route::get('', [ApiUserController::class, 'index']);
        Route::post('register', [ApiUserController::class, 'store']);
        Route::put('update/{id}', [ApiUserController::class, 'update']);
        // Route::put('update/{id}', function(){
        //     dd(2);
        // });
        Route::delete('delete/{id}', [ApiUserController::class, 'destroy']);
        Route::get('{id}', [ApiUserController::class, 'show']);
    });

    Route::group(['prefix' => 'admin'], function () {
        Route::group(['prefix' => 'category'], function () {
            Route::get('', [CategoryController::class, 'index']);
            Route::get('{id}', [CategoryController::class, 'show']);
            Route::post('store', [CategoryController::class, 'store']);
            Route::put('update/{id}', [CategoryController::class, 'update']);
            Route::delete('destroy/{id}', [CategoryController::class, 'destroy']);
        });

        Route::group(['prefix' => 'product'], function () {
            Route::get('', [ProductController::class, 'index']);
            Route::get('{id}', [ProductController::class, 'show']);
            Route::post('store', [ProductController::class, 'store']);
            Route::put('update/{id}', [ProductController::class, 'update']);
            Route::delete('destroy/{id}', [ProductController::class, 'destroy']);

            Route::group(['prefix' => 'favorite'], function () {
                Route::get('', [ProductFavouriteController::class, 'index']);
                Route::get('{id}', [ProductFavouriteController::class, 'show']);
                Route::post('store', [ProductFavouriteController::class, 'store']);
                Route::put('update/{id}', [ProductFavouriteController::class, 'update']);
                Route::delete('destroy/{id}', [ProductFavouriteController::class, 'destroy']);
            });

            Route::group(['prefix' => 'review'], function () {
                Route::get('', [ReviewController::class, 'index']);
                Route::get('{id}', [ReviewController::class, 'show']);
                Route::post('store', [ReviewController::class, 'store']);
                Route::put('update/{id}', [ReviewController::class, 'update']);
                Route::delete('destroy/{id}', [ReviewController::class, 'destroy']);
            });
        });


        Route::group(['prefix' => 'order'], function () {
            Route::get('', [OrderController::class, 'index']);
            Route::get('{id}', [OrderController::class, 'show']);
            Route::get('update/{id}', [OrderController::class, 'update']);
            Route::delete('destroy/{id}', [OrderController::class, 'destroy']);

            Route::group(['prefix' => 'transaction'], function () {
                Route::get('', [TransactionController::class, 'index']);
                Route::get('{id}', [TransactionController::class, 'show']);
                Route::post('store', [TransactionController::class, 'store']);
                Route::put('update/{id}', [TransactionController::class, 'update']);
                Route::delete('destroy/{id}', [TransactionController::class, 'destroy']);
            });

            Route::group(['prefix' => 'payment'], function () {
                Route::get('', [PaymentController::class, 'index']);
                Route::get('{id}', [PaymentController::class, 'show']);
                Route::post('store', [PaymentController::class, 'store']);
                Route::put('update/{id}', [PaymentController::class, 'update']);
                Route::delete('destroy/{id}', [PaymentController::class, 'destroy']);
            });
        });

        Route::group(['prefix' => 'menu'], function () {
            Route::get('', [MenuController::class, 'index']);
            Route::get('{id}', [MenuController::class, 'show']);
            Route::post('store', [MenuController::class, 'store']);
            Route::put('update/{id}', [MenuController::class, 'update']);
            Route::delete('destroy/{id}', [MenuController::class, 'destroy']);
        });

        Route::group(['prefix' => 'article'], function () {
            Route::get('', [ArticleController::class, 'index']);
            Route::get('{id}', [ArticleController::class, 'show']);
            Route::post('store', [ArticleController::class, 'store']);
            Route::put('update/{id}', [ArticleController::class, 'update']);
            Route::delete('destroy/{id}', [ArticleController::class, 'destroy']);

            Route::group(['prefix' => 'favorite'], function () {
                Route::get('', [ArticleFavouriteController::class, 'index']);
                Route::get('{id}', [ArticleFavouriteController::class, 'show']);
                Route::post('store', [ArticleFavouriteController::class, 'store']);
                Route::put('update/{id}', [ArticleFavouriteController::class, 'update']);
                Route::delete('destroy/{id}', [ArticleFavouriteController::class, 'destroy']);
            });

            Route::group(['prefix' => 'comment'], function () {
                Route::get('', [CommentController::class, 'index']);
                Route::get('{id}', [CommentController::class, 'show']);
                Route::post('store', [CommentController::class, 'store']);
                Route::put('update/{id}', [CommentController::class, 'update']);
                Route::delete('destroy/{id}', [CommentController::class, 'destroy']);
            });
        });

        Route::group(['prefix' => 'discount'], function () {
            Route::get('', [DiscountController::class, 'index']);
            Route::get('{id}', [DiscountController::class, 'show']);
            Route::post('store', [DiscountController::class, 'store']);
            Route::put('update/{id}', [DiscountController::class, 'update']);
            Route::delete('destroy/{id}', [DiscountController::class, 'destroy']);
        });

        Route::group(['prefix' => 'chat'], function () {
            Route::get('', [ChatController::class, 'index']);
            Route::get('{id}', [ChatController::class, 'show']);
            Route::post('store', [ChatController::class, 'store']);
            Route::put('update/{id}', [ChatController::class, 'update']);
            Route::delete('destroy/{id}', [ChatController::class, 'destroy']);
        });
    });
});
