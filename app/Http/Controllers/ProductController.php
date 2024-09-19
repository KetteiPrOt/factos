<?php

namespace App\Http\Controllers;

use App\Http\Requests\Products\IndexRequest;
use App\Http\Requests\Products\StoreRequest;
use App\Models\Products\Model as Product;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index(IndexRequest $request)
    {
        $validated = $request->validated();
        $products = Product::select(
            'products.id',
            'products.code',
            'products.name',
            'products.price',
            'vat_rates.name AS vat_rate'
        )->join(
            'vat_rates', 'vat_rates.id', '=', 'products.vat_rate_id'
        )->where(
            'products.user_id', Auth::user()->id
        )->where(
            'products.code', 'LIKE', '%'.($validated['code'] ?? null).'%'
        )->where(
            'products.name', 'LIKE', '%'.($validated['name'] ?? null).'%'
        )->orderBy('name')->get();
        return $products;
    }

    public function store(StoreRequest $request)
    {
        $validated = $request->validated();
        $validated['tourism_vat_applies'] = isset($validated['tourism_vat_applies']);
        $validated['ice_applies'] = isset($validated['ice_applies']);
        $validated['user_id'] = Auth::user()->id;
        Product::create($validated);
        return response(['message' => 'Guardado.'], 200);
    }
}
