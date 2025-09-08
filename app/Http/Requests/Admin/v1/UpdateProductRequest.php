<?php

namespace App\Http\Requests\Admin\v1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|unique:products,slug,'. $this->product->id.'id',
            'short_description' => 'nullable|string',
            'description' => 'sometimes|string',
            'regular_price' => 'sometimes|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:regular_price|required_with:regular_price',
            'SKU' => 'sometimes|string|unique:products,SKU,'. $this->product->id.'id',
            'stock_status' => 'sometimes|in:instock,outofstock',
            'status'=>'nullable|in:active,inactive',
            'featured' => 'nullable|boolean',
            'quantity' => 'sometimes|integer|min:0',
            'images' => 'nullable',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'category_id' => 'sometimes|exists:categories,id',
            'brand_id' => 'sometimes|exists:brands,id',
        ];
    }
}
