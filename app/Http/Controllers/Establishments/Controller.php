<?php

namespace App\Http\Controllers\Establishments;

use App\Http\Resources\Establishments\Resource;
use App\Models\Establishments\Model as Establishment;
use App\Http\Controllers\Controller as BaseController;
use App\Http\Requests\Establishments\StoreRequest;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    public function index()
    {
        $estabishments = Establishment::where(
            'user_id', Auth::user()->id
        )->orderBy('code', 'asc')->get();
        return Resource::collection($estabishments);
    }

    public function store(StoreRequest $request)
    {
        $validated = $request->validated();
        $validated['code'] = $this->numberToChar($validated['code']);
        $validated['user_id'] = Auth::user()->id;
        Establishment::create($validated);
        return response(['message' => 'Guardado'], 200);
    }

    private function numberToChar(int $n): string
    {
        // 100 - 999
        if($n > 99) return "$n";
        // 10 - 99
        if($n > 9) return "0$n";
        // 1 - 9
        return "00$n";
    }
}
