<?php

namespace App\Http\Controllers\Api\Auth;

use App\Core\ResponseService;
use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Passport\TokenRepository;

class ApiAuthController extends Controller
{
    public function login(AuthRequest $request): JsonResponse
    {
        $input = $request->only('email', 'password');

        if (!Auth::attempt($input)) { // Kiểm tra thông tin đăng nhập
            return ResponseService::error('Thông tin đăng nhập không chính xác', 401);
        }
        $user = User::where('email', $input['email'])->first();
        $token = $user->createToken('customer-login', [$user->role]);
        return ResponseService::success([
            'user' => new UserResource($user),
            'token' => $token->accessToken,
        ], 'Đăng nhập thành công');
    }

    public function logout(Request $request) // : JsonResponse
    {
        $user = Auth::user()->token();
        if ($user->revoke()) {
            return ResponseService::success([], 'Đăng xuất thành công');
        } else {
            return ResponseService::error('Đăng xuất thất bại', 400);
        };
    }
}
