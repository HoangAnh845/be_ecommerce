<?php

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Mail\RegistrationSuccessful;
use App\Mail\ForgotPassword;
use App\Models\User;
use App\Http\Controllers\GoogleController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
// Auth::route

Route::get('/', function () {
    // $user = User::find(1);
    // return new RegistrationSuccessful($user);
    return view('welcome');
});

Route::get('/login', function () {
    return view('login');
})->name('login');

// Xem trước giao diện và thông tin email sẽ gửi đi
Route::get('/mailable', function () {
    $user = User::find(1);
    return new ForgotPassword($user);
});

Route::controller(GoogleController::class)->group(function () {
    Route::get('login/google', 'redirectToGoogle')->name('login.google');
    Route::get('login/google/callback', 'handleGoogleCallback');
});
