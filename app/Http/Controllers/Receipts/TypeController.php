<?php

namespace App\Http\Controllers\Receipts;

use App\Http\Controllers\Controller;
use App\Models\Receipts\Type;
use App\Http\Resources\Receipts\Type\Index\Resource;

class TypeController extends Controller
{
    public function index()
    {
        return Resource::collection(Type::all());
    }
}
