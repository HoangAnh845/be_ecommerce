<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ArticleRequest extends FormRequest
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
        $title = $this->method() === 'PUT' ? 'required|string' : 'required|string|unique:articles';
        return [
            'title' => $title,
            'content' => 'required|string',
            'avatar' => 'required|url',
            'menu_id' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Tiêu đề không được để trống',
            'title.string' => 'Tiêu đề phải là chuỗi',
            'title.unique' => 'Tiêu đề đã tồn tại',
            'content.required' => 'Nội dung không được để trống',
            'content.string' => 'Nội dung phải là chuỗi',
            'avatar.required' => 'Ảnh đại diện không được để trống',
            'avatar.url' => 'Ảnh đại diện phải là URL',
            'menu_id.required' => 'Menu không được để trống',
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
