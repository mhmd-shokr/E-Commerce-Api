<?php

namespace App\Http\Requests\Admin\v1;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
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
            'name'=>'required|string',
            'slug'=>'required|unique:categories,slug',
            'status'=>'nullable|in:active,inactive',
            'image'=>'nullable|mimes:png,jpg,jpeg|max:2048',
        ];
    }
}
