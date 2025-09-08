<?php

namespace App\Http\Resources\Admin\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemsResource extends JsonResource
{
    
        public function toArray(Request $request): array
        {
            return [
                'id' => $this->id,
                'product_id' => $this->product_id,
                'order_id' => $this->order_id,
                'price' => $this->price,
                'quantity' => $this->quantity,
                'options' => $this->options,
                'rstatus' => $this->rstatus,
            ];
        }
    
}
