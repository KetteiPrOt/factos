<?php

namespace App\Http\Resources\Establishments\IssuancePoints;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Establishments\Sequentials\IndexResource as SequentialResource;

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
            'code' => $this->code,
            'description' => $this->description,
            'active' => $this->active,
            'sequentials' => SequentialResource::collection($this->sequentials)
        ];
    }
}
