<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Models\Products\VatRate;

class VatRateController extends Controller
{
    public function index()
    {
        return VatRate::select('id', 'name', 'percentaje')
                      ->orderBy('percentaje', 'desc')
                      ->get();
    }
}
