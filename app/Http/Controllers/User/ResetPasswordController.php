<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ResetPasswordController extends Controller
{
    const RESET_PASSWORD_SUCCESS = 'Mật khẩu đã được đặt lại.',
        RESET_PASSWORD_FAILED = 'Mật khẩu không thể đặt lại.',
        USER_NOT_FOUND = 'Người dùng không tồn tại.';

    protected function resetPassword(AuthRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => self::USER_NOT_FOUND,
            ]);
        }

        $user->password = Hash::make($request->password);// bcrypt($request->password);
        $user->save();
        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => self::RESET_PASSWORD_SUCCESS,
        ]);
    }
}
