<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest;
use Illuminate\Http\Response;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    private const LOGIN_SUCCESS = 'Đăng nhập thành công.',
        LOGIN_FAILED = 'Thông tin đăng nhập không chính xác.',
        LOGOUT_SUCCESS = 'Đăng xuất thành công.',
        LOGOUT_FAILED = 'Đăng xuất thất bại.';

    public function loginUser(AuthRequest $request): Response
    {
        $input = $request->only('email', 'password');
        
        if (!Auth::attempt($input)) { // Kiểm tra thông tin đăng nhập
            return $this->resAuth(Response::HTTP_BAD_REQUEST, self::LOGIN_FAILED);
        }
        $user = User::where('email', $input['email'])->first();
        $token = $user->createToken('customer-login', [$user->role]);
        $user = (new UserResource($user))
            ->additional(['token' => $token->accessToken])
            ->response()
            ->getData(true);
        return $this->resAuth(Response::HTTP_OK, self::LOGIN_SUCCESS, $user);
    }

    public function logoutUser(Request $request): Response
    {
        if ($request->user()->tokens()->delete()) {
            return $this->resAuth(Response::HTTP_OK, self::LOGOUT_SUCCESS);
        } else {
            return $this->resAuth(Response::HTTP_BAD_REQUEST, self::LOGOUT_FAILED);
        };
        
    }

    public function resAuth(int $status, string $message, ?array $resource = []): Response
    {
        $result = [
            'status' => $status,
            'message' => $message
        ];
        if (count($resource)) {
            $result = array_merge(
                $result,
                [
                    'data' => $resource['data'],
                    'token' => $resource['token']
                ]
            );
        }
        return Response($result, $status);
        // return Response([
        //     'status' => true,
        //     'token_type' => 'Bearer',
        //     'access_token' => $access_token,
        //     'token' => $objToken->token,
        //     'expires_in' => $objToken->token->expires_at->diffInSeconds(Carbon::now()) // // thời gian sống của token tính bằng giây
        // ], 200);
    }
}

