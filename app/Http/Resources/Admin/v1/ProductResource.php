<?php

namespace App\Http\Resources\Admin\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return[
            "Id"=>$this->id,
            "Name"=>$this->id,
            "Short Description"=>$this->short_description,
            "Slug"=>$this->slug,
            "SKU"=>$this->SKU,
            "Sale Price"=>$this->sale_price,
            "Regular Price"=>$this->regular_price,
            "Stock Status"=>$this->stock_status,
            "Quantity"=>$this->quantity,
            "Featured"=>$this->featurd,
            "Main Image"=>asset('storage/products/',$this->image),
            "images" => $this->images ? collect(explode(',',$this->images))->map(fn($img)=>asset("storage/products/gallary/".$this->image)):[],
        ];
    }
}
