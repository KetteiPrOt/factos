<?php

namespace App\Http\Resources\Receipts\Invoices\Index;

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
            'access_key' => $this->access_key,
            'issuance_date' => $this->issuance_date,
            'number' => $this->number,
            'status' => $this->status,
            'receipt_type' => $this->receipt_type,
        ];
    }
}
