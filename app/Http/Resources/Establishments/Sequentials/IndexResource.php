<?php

namespace App\Http\Resources\Establishments\Sequentials;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'next' => $this->next,
            'receipt_type_id' => $this->receipt_type_id,
        ];
    }
}
