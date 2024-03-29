<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class AuthRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        # lấy ra đường dẫn API hiện tại
        $path = $this->path();
        if ($path === 'api/login') {
            return [
                'email' => 'required|email',
                'password' => 'required|min:6',
            ];
        } else { // api/password/reset
            return [
                'email' => 'required|email',
                'password' => 'required|min:6|confirmed',
            ];
        }
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Email không được bỏ trống',
            'email.email' => 'Email không đúng định dạng',
            'password.required' => 'Password không được bỏ trống',
            'password.min' => 'Password ít nhất 6 ký tự',
            'password.confirmed' => 'Password không trùng khớp'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        // dd($validator->errors());
        throw new HttpResponseException(response()->json([
            'name' => 'Lỗi xác thực',
            'status' => 422,
            'message' => $validator->errors()
        ], 422));
    }
}
