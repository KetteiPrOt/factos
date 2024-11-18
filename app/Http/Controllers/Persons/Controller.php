<?php

namespace App\Http\Controllers\Persons;

use App\Http\Controllers\Controller as BaseController;
use App\Http\Requests\Persons\IndexRequest;
use App\Http\Requests\Persons\StoreRequest;
use App\Http\Requests\Persons\UpdateRequest;
use App\Models\Persons\Model as Person;
use App\Http\Resources\Basic\Index\PaginatedCollection;
use App\Http\Resources\Persons\SearchResource;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    public function search(Request $request)
    {
        $validated = $request->validate(['identification' => 'required|string|max:255']);
        $person = Person::where('user_id', $this->authUser()->id)
            ->where('identification', 'LIKE', '%' . $validated['identification'] . '%')
            ->first();
        if($person) return new SearchResource($person);
        return response([ 'message' => 'Sin resultados para la búsqueda.'], 404);
    }

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

    public function show(Person $person)
    {
        $this->authUser()->checkModelBelongsToMe($person, 'persons');
        return $person;
    }

    public function update(UpdateRequest $request, Person $person)
    {
        $validated = $request->validated();
        $validated['user_id'] = $this->authUser()->id;
        $person->update($validated);
        return $validated;
        return response(['message' => 'Actualizado.'], 200);
    }

    public function destroy(Person $person)
    {
        $this->authUser()->checkModelBelongsToMe($person, relationship: 'persons');
        $person->delete();
        return response(['message' => 'Eliminado.'], 200);
    }

    public function destroyAll()
    {
        $persons = $this->authUser()->persons;
        if($persons->count() > 0){
            foreach($persons as $person){
                $person->delete();
            }
            return response(['message' => 'Eliminados.'], 200);
        }
        return response(['message' => 'No hay personas para eliminar.'], 200);
    }
}
