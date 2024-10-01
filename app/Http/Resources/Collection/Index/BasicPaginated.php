<?php

namespace App\Http\Resources\Collection\Index;

use Illuminate\Http\Resources\Json\ResourceCollection;

class BasicPaginated extends ResourceCollection
{
    /**
     * Customize the pagination information for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array $paginated
     * @param  array $default
     * @return array
     */
    public function paginationInformation($request, $paginated, $default)
    {
        unset($default['meta']['links']);
        unset($default['meta']['from']);
        unset($default['meta']['to']);
        unset($default['meta']['path']);
        unset($default['meta']['per_page']);
        return $default;
    }
}
