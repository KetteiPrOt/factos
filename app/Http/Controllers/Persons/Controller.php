<?php

namespace App\Http\Controllers\Persons;

use App\Http\Controllers\Controller as BaseController;
use App\Http\Requests\Persons\IndexRequest;
use App\Http\Requests\Persons\StoreRequest;
use App\Models\Persons\Model as Person;
use App\Http\Resources\Basic\Index\PaginatedCollection;

class Controller extends BaseController
{
    public function index(IndexRequest $request)
    {
        $validated = $request->validated();
        $persons = Person::select(
            'persons.id',
            'identification',
            'social_reason',
            'email',
            'identification_types.name AS identification_type'
        )->join(
            'identification_types', 'identification_types.id', '=', 'persons.identification_type_id'
        )->where(
            'persons.user_id', $this->authUser()->id
        )->where(
            'persons.identification', 'LIKE', '%'.($validated['identification'] ?? null).'%'
        )->where(
            'persons.social_reason', 'LIKE', '%'.($validated['social_reason'] ?? null).'%'
        )->orderBy('social_reason')->paginate()->withQueryString();
        return new PaginatedCollection($persons);
    }

    public function store(StoreRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = $this->authUser()->id;
        Person::create($validated);
        return response(['message' => 'Guardado.'], 200);
    }
}
