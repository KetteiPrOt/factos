<?php

namespace App\Http\Resources\Products\Indexes;

use Illuminate\Http\Request;
use App\Http\Resources\Collection\Index\BasicPaginated;

class Paginated extends BasicPaginated
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}
