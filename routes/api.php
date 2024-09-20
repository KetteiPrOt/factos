<?php

use App\Http\Controllers\Auth\Login;
use App\Http\Controllers\Products\Controller as ProductController;
use App\Http\Controllers\Products\IceTypeController;
use App\Http\Controllers\Products\VatRateController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [Login::class, 'login']);

// Route::middleware(['auth:sanctum'])
//      ->controller(ProductController::class)->group(function (){
Route::controller(ProductController::class)->group(function (){
    Route::get('/products', 'index')->name('products.index');
    Route::post('/products', 'store')->name('products.store');
    Route::get('/products/{product}', 'show')->name('products.show');
    Route::put('/products/{product}', 'update')->name('products.update');
});

Route::get('/vat-rates', [VatRateController::class, 'index']);
Route::get('/ice-types', [IceTypeController::class, 'index']);
