<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;
use Illuminate\Auth\Events\Verified;

class VerificationController extends Controller
{
    public function verify(Request $request)
    {
        if (!$request->fullUrl())
            return $request->throwBadRequestException();

        return response()->json(['status' => Response::HTTP_OK, 'message' => 'Tài khoản đã được xác minh thành công']);
        // $user = User::find($id)->first();
        // if ($user->hasVerifiedEmail()) { // Kiểm tra email đã được xác minh chưa?
        //     return response(['message' => 'Email đã được xác minh'], 400);
        // }
        // if ($user->markEmailAsVerified()) { // Đánh dấu email đã được xác minh
        //     event(new Verified($user)); // Gửi thông báo xác minh email
        // }
        // return response(['message' => 'Xác minh email thành công'], 200);
    }

    public function resend(Request $request)
    {
        $user = $this->getUserByEmail($request->validated()['email']);

        if ($user && !$user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();

            return response()->json(['code' => Response::HTTP_OK, 'message' => 'Verification link has been sent.']);
        }

        return $request->throwBadRequestException();
    }
}
