<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Mail\ForgotPassword;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserResource;
use App\Jobs\SendEmailForgotPassword;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;


class ForgotPasswordController extends Controller
{
    public function sendResetLinkEmail(Request $request)
    {
        $email = $request->input('email');

        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ], [
            'email.required' => 'Email không được để trống',
            'email.email' => 'Email không đúng định dạng'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'name' => 'Quên mật khẩu',
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => $validator->errors()->first(),
            ]);
        }

        $user = User::where('email', $email)->first();
        if ($user) {

            // Gửi mail
            $emailJob = new SendEmailForgotPassword($user); // tạo một job để gửi email
            Log::info('This is some useful information.', ['emailJob' => $emailJob]);
            dispatch($emailJob); // dispatch là phương thức gửi email
            // Mail::to($user)->send(new ForgotPassword($user));
            
            return response()->json([
                'title' => 'Quuên mật khẩu',
                'status' => Response::HTTP_OK,
                'message' => 'Email đã được gửi đi',
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                ]
            ]);
        }
    }
}

// Lỗi này xảy ra khi một phương thức trong model Eloquent của bạn được dự đoán là một phương thức quan hệ (relationship method), nhưng lại không trả về một instance của một quan hệ Eloquent.
            //  phương thức token trong model User của bạn đang trả về null thay vì một instance của một quan hệ Eloquent.
//$user->token = $user->createToken('forgot-password', [$user->role])->accessToken;
// 'token' => $user->token