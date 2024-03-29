<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class NoticeRequest extends FormRequest
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
        $unique = $this->method() === 'PUT' ? 'required|string' : 'required|string|unique:notices';
        return [
            'type' => 'required|string',
            'title' => $unique,
            'desc' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Trường Type bắt buộc!',
            'type.string' => 'Trường Type phải là chuỗi!',
            'title.required' => 'Trường Title bắt buộc!',
            'title.string' => 'Trường Title phải là chuỗi!',
            'title.unique' => 'Trường Title đã tồn tại!',
            'desc.required' => 'Trường Desc bắt buộc!',
            'desc.string' => 'Trường Desc phải là chuỗi!',
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
