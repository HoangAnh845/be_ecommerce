<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class DiscountRequest extends FormRequest
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
            'title' => 'required|string',
            'proviso' => 'required|string',
            'expiry' => 'required|date',
            'order_total' => 'required|numeric',
            'discount' => 'required|numeric',
            'code' => 'required|string', 
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Tiêu đề không được để trống',
            'title.string' => 'Tiêu đề phải là chuỗi',
            'proviso.required' => 'Điều kiện không được để trống',
            'proviso.string' => 'Điều kiện phải là chuỗi',
            'expiry.required' => 'Ngày hết hạn không được để trống',
            'expiry.date' => 'Ngày hết hạn phải là ngày',
            'order_total.required' => 'Tổng đơn hàng không được để trống',
            'order_total.numeric' => 'Tổng đơn hàng phải là số',
            'discount.required' => 'Giảm giá không được để trống',
            'discount.numeric' => 'Giảm giá phải là số',
            'code.required' => 'Mã giảm giá không được để trống',
            'code.string' => 'Mã giảm giá phải là chuỗi',
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
