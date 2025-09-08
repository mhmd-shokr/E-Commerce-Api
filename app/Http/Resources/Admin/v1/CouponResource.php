<?php

namespace App\Http\Resources\Admin\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "Id"=>$this->id,
            "code"=>$this->code,
            "value"=>$this->value,
            "cart_value"=>$this->cart_value,
            "expiry_date"=>$this->expiry_date,
        ];
    }
}
