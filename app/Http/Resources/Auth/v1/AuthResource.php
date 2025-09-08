<?php

namespace App\Http\Resources\Auth\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'Id'=>$this->id,
            'Name'=>$this->name,
            'Email'=>$this->email,
        ];
    }
}
