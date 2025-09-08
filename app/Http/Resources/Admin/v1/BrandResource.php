<?php

namespace App\Http\Resources\Admin\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BrandResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"=>$this->id,
            "slug"=>$this->slug,
            'status'=>$this->status,
            "image"=>asset('storage/brands/'.$this->image ),
            "created_at" => $this->created_at?->format('Y-m-d H:i:s'),
            "updated_at" => $this->updated_at?->format('Y-m-d H:i:s'),

        ];
    }
}
