<?php

namespace App\Http\Resources\Persons;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SearchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'identification' => $this->identification,
            'identification_type' => [
                'id' => $this->identificationType->id,
                'name' => $this->identificationType->name
            ],
            'social_reason' => $this->social_reason,
            'address' => $this->address,
            'phone_number' => $this->phone_number,
            'email' => $this->email
        ];
    }
}
