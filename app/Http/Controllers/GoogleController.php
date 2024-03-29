<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Socialite\Facades\Socialite;


class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        try {
            $url = Socialite::driver('google')->redirect()->getTargetUrl();
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'URL để chuyển hướng đến Google',
                'url' => $url,
            ]);
        } catch (\Exception $exception) {
            return $exception; // Xử lý lỗi
        }
        return Socialite::driver('google')->redirect(); // Chuyển hướng đến trang đăng nhập Google
    }

    public function handleGoogleCallback()
    {
        try {

            $googleUser = Socialite::driver('google')->user();
            $user = User::where('email', $googleUser->email)->first();
            if ($user) {
                throw new \Exception(__('google sign in email existed'));
            }
            $user = User::create(
                [
                    'username' => 'required|string|min:3|max:50',
                    'email' => $googleUser->email,
                    'username' => $googleUser->name,
                    'first_name' => $googleUser->user['given_name'],
                    'last_name' => $googleUser->user['family_name'],
                    'avatar' => $googleUser->avatar,
                    'google_id' => $googleUser->id,
                    'password' => '123456',
                ]
            );
            $token = $user->createToken('customer-login-google', ['user']);
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Thông tin người dùng từ Google',
                'user' => $googleUser,
                'token'=> $token->accessToken,
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'error' => $exception,
                'message' => $exception->getMessage()
            ]);
        }
    }
}
