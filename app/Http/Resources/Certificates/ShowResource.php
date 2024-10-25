<?php

namespace App\Http\Resources\Certificates;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uploaded' => $this->uploaded,
            'effective_date' => $this->effective_date,
            'owner' => $this->owner
        ];
    }
}
