<?php

namespace App\Http\Controllers\Establishments;

use App\Http\Controllers\Controller;
use App\Models\Establishments\Model as Establishment;
use App\Http\Resources\Establishments\IssuancePoints\Index\PaginatedCollection;

class IssuancePointController extends Controller
{
    public function index(Establishment $establishment)
    {
        $this->authUser()->checkModelBelongsToMe($establishment, relationship: 'establishments');
        return new PaginatedCollection(
            $establishment->issuancePoints()->with('sequentials')->paginate(15)
        );
    }
}
