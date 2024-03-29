<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class BookAddressRequest extends FormRequest
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
        return [
            'province_id' => 'required',
            'district_id' => 'required',
            'wards_id' => 'required',
            'fullname' => 'required',
            'phone' => 'required',
            'address' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'province_id.required' => 'Tỉnh/Thành phố không được bỏ trống!',
            'district_id.required' => 'Quận/Huyện không được bỏ trống!',
            'wards_id.required' => 'Xã/Phường/Thị trấn không được bỏ trống!',
            'fullname.required' => 'Tên không được bỏ trống!',
            'phone.required' => 'Điện thoại không được bỏ trống!',
            'address.required' => 'Địa chỉ không được bỏ trống!',
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
