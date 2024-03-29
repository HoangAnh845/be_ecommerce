<?php

use App\Http\Controllers\ClientController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\SignatureController;
use Illuminate\Routing\Router;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

$routes = glob(__DIR__ . "/api/*.php");
foreach ($routes as $route) require($route);

// Đường dẫn API
// Route::middleware(['check.signature'])->group(function () {
//     Route::get('users', [UserController::class, 'getAllUsers']);
//     Route::get('users/{id}', [UserController::class, 'getUser']);
// });

// Route::middleware('auth:api')->group(function () {
//     Route::get('user/token', [UserController::class, 'getUserDetail']);
// });

// Đường dẫn bên hệ thống


// Đường dẫn hành động với client
// Route::prefix('clients')->group(function () {
//     Route::get('/', [ClientController::class, 'getAllClients']);
//     Route::post('/{id}', [ClientController::class, 'getClient']);
//     Route::post('/add', [ClientController::class, 'addClient']);
//     Route::post('/edit/{id}', [ClientController::class, 'updateClient']);
//     Route::post('/delete/{id}', [ClientController::class, 'deleteClient']);
// });

// Đường dẫn hành động với chữ ký
// Route::prefix('signature')->group(function () {
//     Route::get('/{id}', [SignatureController::class, 'getSignature']);
//     Route::post('/create', [SignatureController::class, 'createSignature']);
// });

// // Đường dẫn hành động với người dùng
// Route::prefix('user')->group(function () {
//     Route::post('/login', [AuthController::class, 'loginUser']);
//     Route::middleware('auth:api')->post('/logout', [AuthController::class, 'userLogout']);
// });



// Route::controller(UserController::class)->group(function () {
//     Route::get('user/token', 'getUserDetail');
// })->middleware('auth:api');

// // Đường dẫn bên hệ thống
// // Route::post('register', [UserController::class, 'registerUser']);


// // Đường dẫn hành động với client
// Route::controller(ClientController::class)->group(function () {
//     Route::get('/clients', 'getAllClients');
//     Route::post('/clients/{id}', 'getClient');
//     Route::post('/client/add', 'addClient');
//     Route::post('/clients/edit/{id}', 'updateClient');
//     Route::post('/clients/delete/{id}', 'deleteClient');
// });
// // Đường dĐường dẫn hành động với chữ kýchữ ký
// Route::controller(SignatureController::class)->group(function () {
//     Route::get('/signature/{id}', 'getSignature');
//     Route::post('/createSignature', 'createSignature');
// });

