<?php

namespace App\Http\Requests\Admin\v1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBrandRequest extends FormRequest
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
            'name'=>'sometimes|string',
            'slug'=>'sometimes|unique:brands,slug,'.$this->brand->id,
            'status'=>'required|in:active,inactive',
            'image'=>'sometimes|mimes:png,jpg,jpeg|max:2048',
        ];
    }
}
