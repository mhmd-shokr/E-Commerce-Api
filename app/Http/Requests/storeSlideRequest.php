<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class storeSlideRequest extends FormRequest
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
            'tagline'=>'required|string|max:255',
            'title'=>'required|string',
            'subtitle'=>'required|string',
            'link'=>'required|url',
            'status'    => 'required|in:0,1',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];
    }
}
