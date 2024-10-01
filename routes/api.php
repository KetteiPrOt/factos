<?php

use App\Http\Controllers\Auth\Controller as AuthController;
use App\Http\Controllers\Products\Controller as ProductController;
use App\Http\Controllers\Establishments\Controller as EstablishmentController;
use App\Http\Controllers\Products\IceTypeController;
use App\Http\Controllers\Products\VatRateController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return Auth::user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->controller(AuthController::class)->group(function (){
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware(['auth:sanctum'])->controller(ProductController::class)->group(function (){
    Route::get('/products', 'index')->name('products.index');
    Route::post('/products', 'store')->name('products.store');
    Route::get('/products/{product}', 'show')->name('products.show');
    Route::put('/products/{product}', 'update')->name('products.update');
    Route::delete('/products/{product}', 'destroy')->name('products.destroy');
    Route::delete('/products', 'destroyAll')->name('products.destroy-all');
});

Route::middleware(['auth:sanctum'])->get('/vat-rates', [VatRateController::class, 'index']);
Route::middleware(['auth:sanctum'])->get('/ice-types', [IceTypeController::class, 'index']);

Route::middleware(['auth:sanctum'])->controller(EstablishmentController::class)->group(function (){
    Route::get('/establishments', 'index')->name('establishments.index');
    Route::post('/establishments', 'store')->name('establishments.store');
    // Route::get('/establishments/{establishment}', 'show')->name('establishments.show');
    // Route::put('/establishments/{establishment}', 'update')->name('establishments.update');
    // Route::delete('/establishments/{establishment}', 'destroy')->name('establishments.destroy');
});
