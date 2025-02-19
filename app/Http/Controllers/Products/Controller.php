<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller as BaseController;
use App\Http\Requests\Products\IndexRequest;
use App\Http\Requests\Products\StoreRequest;
use App\Http\Requests\Products\UpdateRequest;
use App\Http\Resources\Basic\Index\PaginatedCollection;
use App\Http\Resources\Products\Resource;
use App\Http\Resources\Products\SearchResource;
use App\Models\Products\Model as Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    public function search(Request $request)
    {
        $validated = $request->validate(['look' => 'required|string|max:255']);
        $look = '%' . $validated['look'] . '%';
        $products = Product::where('user_id', $this->authUser()->id)
            ->where(function(Builder $query) use ($look) {
                $query->where('code', 'LIKE', $look)
                    ->orWhere('name', 'LIKE', $look);
            })->limit(10)->get();
        if($products->count() > 0) return SearchResource::collection($products);
        return response([ 'message' => 'Sin resultados para la búsqueda.'], 404);
    }

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
        )->orderBy('name')->paginate()->withQueryString();
        return new PaginatedCollection($products);
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

    public function show(Product $product)
    {
        $this->authUser()->checkModelBelongsToMe($product, relationship: 'products');
        return new Resource($product);
    }

    public function update(UpdateRequest $request, Product $product)
    {
        $validated = $request->validated();
        $validated['tourism_vat_applies'] = isset($validated['tourism_vat_applies']);
        if(isset($validated['ice_applies'])){
            $validated['ice_applies'] = true;
        } else {
            $validated['ice_applies'] = false;
            $validated['ice_type_id'] = null;
        }
        $validated['user_id'] = Auth::user()->id;
        $product->update($validated);
        return response(['message' => 'Actualizado.'], 200);
    }

    public function destroy(Product $product)
    {
        $this->authUser()->checkModelBelongsToMe($product, relationship: 'products');
        $product->delete();
        return response(['message' => 'Eliminado.'], 200);
    }

    public function destroyAll()
    {
        $products = Auth::user()->products;
        if($products->count() > 0){
            foreach($products as $product){
                $product->delete();
            }
            return response(['message' => 'Eliminados.'], 200);
        }
        return response(['message' => 'No hay productos para eliminar.'], 200);
    }
}
