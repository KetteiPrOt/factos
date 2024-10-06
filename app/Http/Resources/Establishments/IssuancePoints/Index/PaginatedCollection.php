<?php

namespace App\Http\Resources\Establishments\IssuancePoints\Index;

use App\Http\Resources\Basic\Index\PaginatedCollection as BasicPaginatedCollection;

class PaginatedCollection extends BasicPaginatedCollection
{
     /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = Resource::class;
}
