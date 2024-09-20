<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller as BaseController;
use App\Http\Requests\Products\IndexRequest;
use App\Http\Requests\Products\StoreRequest;
use App\Http\Requests\Products\UpdateRequest;
use App\Models\Products\Model as Product;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
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
            'products.user_id', Auth::user()?->id ?? 1
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
        $validated['user_id'] = Auth::user()?->id ?? 1;
        Product::create($validated);
        return response(['message' => 'Guardado.'], 200);
    }

    public function show(Product $product)
    {
        return $product;
    }

    public function update(UpdateRequest $request, Product $product)
    {
        $validated = $request->validated();
        $validated['tourism_vat_applies'] = isset($validated['tourism_vat_applies']);
        $validated['ice_applies'] = isset($validated['ice_applies']);
        $validated['user_id'] = Auth::user()?->id ?? 1;
        $product->update($validated);
        return response(['message' => 'Actualizado.'], 200);
    }
}
