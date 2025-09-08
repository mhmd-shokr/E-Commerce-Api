<?php

namespace App\Http\Resources\Admin\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'subtotal' => $this->subtotal,
            'discount' => $this->discount,
            'tax' => $this->tax,
            'total' => $this->total,
            'name' => $this->name,
            'phone' => $this->phone,
            'locality' => $this->locality,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'landmark' => $this->landmark,
            'zip' => $this->zip,
            'type' => $this->type,
            'status' => $this->status,
            'is_shipping_different' => $this->is_shipping_different,
            'delivered_date' => $this->delivered_date,
            'canceled_date' => $this->canceled_date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'items'=>OrderItemsResource::collection($this->whenLoaded('orderItems')),
        ];
    }
}
