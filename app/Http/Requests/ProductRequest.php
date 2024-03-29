<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProductRequest extends FormRequest
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
        // Lấy phương thức API
        $method = $this->method();
        return $method === 'POST' ? [
            'category_id' => 'required|integer',
            'name' => 'required|string|unique:products',
            'describe' => 'required|string',
            'amount' => 'required|integer',
            'price' => 'required|integer',
        ] : [
            'category_id' => 'required|integer',
            'name' => 'required|string',
            'describe' => 'required|string',
            'amount' => 'required|integer',
            'price' => 'required|integer',
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
            'category_id.required' => 'Trường Category bắt buộc!',
            'category_id.integer' => 'Trường Category phải là số nguyên!',
            'name.required' => 'Trường Name bắt buộc!',
            'name.string' => 'Trường Name phải là chuỗi!',
            'name.unique' => 'Name đã tồn tại!',
            'describe.required' => 'Trường Describe bắt buộc!',
            'describe.string' => 'Trường Describe phải là chuỗi!',
            'amount.required' => 'Trường Amount bắt buộc!',
            'amount.integer' => 'Trường Amount phải là số nguyên!',
            'price.required' => 'Trường Price bắt buộc!',
            'price.integer' => 'Trường Price phải là số nguyên!',
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
