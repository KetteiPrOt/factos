<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Models\Products\IceType;

class IceTypeController extends Controller
{
    public function index()
    {
        return IceType::orderBy('code')->get();
    }
}
