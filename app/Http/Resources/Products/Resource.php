<?php

namespace App\Http\Resources\Products;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Resource extends JsonResource
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
            'code' => $this->code,
            'name' => $this->name,
            'price' => $this->price,
            'additional_info' => $this->additional_info,
            'tourism_vat_applies' => $this->tourism_vat_applies,
            'ice_applies' => $this->ice_applies,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user_id' => $this->user_id,
            'ice_type_id' => $this->ice_type_id,
            'vat_rate_id' => $this->vat_rate_id
        ];
    }
}
