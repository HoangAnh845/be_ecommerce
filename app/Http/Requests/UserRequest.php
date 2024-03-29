<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool // phương thức này trả về true nếu người dùng được ủy quyền để thực hiện hành động yêu cầu, ngược lại trả về false
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    { // phương thức này trả về một mảng các quy tắc xác thực mà tất cả các dữ liệu đầu vào của người dùng phải tuân theo
        switch ($this->method()) {
            case 'POST':
                return [
                    'username' => 'required|string|min:3|max:50|unique:users',
                    'first_name' => 'required|string|min:3|max:50',
                    'last_name' => 'required|string|min:3|max:50',
                    'birthday' => 'required|date',
                    'gender' => 'required',
                    'email' => 'required|email|unique:users',
                    'password' => 'required|string|min:5'
                ];
            case 'PUT':
                return [
                    'first_name' => 'required|string|min:3|max:50',
                    'last_name' => 'required|string|min:3|max:50',
                    'gender' => 'required',
                ];
            default:
                return [];
        }
    }

    public function messages()
    {
        return [
            'username.required' => 'Tên không được bỏ trống',
            'username.unique' => 'Username đã tồn tại',
            'username.min' => 'Tên ít nhất 3 ký tự',
            'username.max' => 'Tên không quá 50 ký tự',
            'first_name.required' => 'Tên không được bỏ trống',
            'first_name.min' => 'Tên ít nhất 3 ký tự',
            'first_name.max' => 'Tên không quá 50 ký tự',
            'last_name.required' => 'Tên không được bỏ trống',
            'last_name.min' => 'Tên ít nhất 3 ký tự',
            'last_name.max' => 'Tên không quá 50 ký tự',
            'birthday.required' => 'Ngày sinh không được bỏ trống',
            'birthday.date' => 'Ngày sinh không đúng định dạng',
            'gender.required' => 'Giới tính không được bỏ trống',
            'name.required' => 'Tên không được bỏ trống',
            'email.required' => 'Email không được bỏ trống',
            'email.email' => 'Email không đúng định dạng',
            'email.unique' => 'Email đã tồn tại',
            'password.required' => 'Password không được bỏ trống',
            'password.min' => 'Password ít nhất 5 ký tự'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'name' => 'Lỗi xác thực',
            'status' => 422,
            'message' => $validator->errors()
        ], 422));
    }
}
