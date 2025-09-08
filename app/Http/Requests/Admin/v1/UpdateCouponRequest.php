<?php

namespace App\Http\Requests\Admin\v1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCouponRequest extends FormRequest
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
            'code' => 'sometimes|unique:coupons,code,' . $this->coupon->id,
            'type' => ['sometimes', Rule::in(['fixed', 'percent'])],
            'value' => 'sometimes|numeric|min:0',
            'cart_value' => 'sometimes|numeric|min:0',
            'expiry_date' => 'sometimes|date|after_or_equal:today',
        ];
    }
}
