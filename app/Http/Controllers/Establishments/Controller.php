<?php

namespace App\Http\Controllers\Establishments;

use App\Http\Resources\Establishments\IndexResource as Resource;
use App\Models\Establishments\Model as Establishment;
use App\Http\Controllers\Controller as BaseController;
use App\Http\Requests\Establishments\StoreRequest;
use App\Http\Requests\Establishments\UpdateRequest;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    public function index()
    {
        $establishments = Establishment::where(
            'user_id', Auth::user()->id
        )->orderBy('code', 'asc')->get();
        return Resource::collection($establishments);
    }

    public function store(StoreRequest $request)
    {
        $validated = $request->validated();
        $validated['code'] = $this->numberToChar($validated['code']);
        $validated['user_id'] = Auth::user()->id;
        Establishment::create($validated);
        return response(['message' => 'Guardado'], 200);
    }

    public function show(Establishment $establishment)
    {
        $this->authUser()->checkModelBelongsToMe($establishment, relationship: 'establishments');
        return new Resource($establishment);
    }

    public function update(UpdateRequest $request, Establishment $establishment)
    {
        $validated = $request->validated();
        $validated['code'] = $this->numberToChar($validated['code']);
        $validated['user_id'] = Auth::user()->id;
        $establishment->update($validated);
        return response(['message' => 'Actualizado.'], 200);
    }

    public function destroy(Establishment $establishment)
    {
        $this->authUser()->checkModelBelongsToMe($establishment, relationship: 'establishments');
        $establishment->delete();
        return response(['message' => 'Eliminado.'], 200);
    }
}
