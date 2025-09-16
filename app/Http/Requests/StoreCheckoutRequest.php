<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCheckoutRequest extends FormRequest
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
                'payment_method'=>'required|string|in:cod,stripe',
                'coupon_code'=>'nullable|string|exists:coupons,code',
                'addr.billing.name' => 'required|string|max:255',
                'addr.billing.email' => 'required|email',
                'addr.billing.phone' => 'nullable|string|max:20',
                'addr.billing.address' => 'required|string',
                'addr.billing.city' => 'required|string',
                'addr.billing.state' => 'required|string',
                'addr.billing.country' => 'required|string',
                'addr.billing.postal_code' => 'nullable|string|max:20',

                'addr.shipping.name' => 'required|string|max:255',
                'addr.shipping.email' => 'required|email',
                'addr.shipping.phone' => 'nullable|string|max:20',
                'addr.shipping.address' => 'required|string',
                'addr.shipping.city' => 'required|string',
                'addr.shipping.state' => 'required|string',
                'addr.shipping.country' => 'required|string',
                'addr.shipping.postal_code' => 'nullable|string|max:20',
            ];
        }
        }
