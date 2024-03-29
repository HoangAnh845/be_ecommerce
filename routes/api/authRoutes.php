<?php

use App\Http\Controllers\User\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\ForgotPasswordController;
use App\Http\Controllers\User\ResetPasswordController;

/*
|--------------------------------------------------------------------------
| Authentication API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register Authentication API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'loginUser');
    Route::post('/logout', 'logoutUser')->middleware('auth:api');
});

// Quên mật khuẩn -> Gửi email -> Nhập email -> Nhập mật khẩu mới
Route::post("/password/email", [ForgotPasswordController::class, "sendResetLinkEmail"])->name("auth.password.send-email");
Route::post("/password/reset", [ResetPasswordController::class, "resetPassword"])->name("auth.password.reset");

// Login xác thực 2 bước
// Route::get("/email/verify/{id}/{hash}", [VerificationController::class, "verify"])->middleware('signed') // Kiểm tra xem link có hợp lệ không
//     ->name("verification.verify");
// Route::post("/email/resend", [VerificationController::class, "resend"])->middleware('throttle:6,1') // Giới hạn 6 lần gửi email trong 1 phút
//     ->name("verification.send");

