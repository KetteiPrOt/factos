<?php

namespace App\Http\Controllers\Persons;

use App\Http\Controllers\Controller;
use App\Models\Persons\IdentificationType as Type;
use Illuminate\Http\Request;

class IdentificationTypeController extends Controller
{
    public function index(Request $request)
    {
        $request->validate(['exclude_final_consumer' => 'boolean']);
        $types = Type::select('id', 'name')->get();
        if($request->get('exclude_final_consumer')){
            return $types->except(Type::finalConsumer()->id);
        };
        return $types;
    }
}
