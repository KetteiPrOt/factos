<?php

use App\Http\Controllers\Auth\Login;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [Login::class, 'login']);

// Route::middleware(['auth:sanctum'])->controller(ProductController::class)->group(function (){
Route::controller(ProductController::class)->group(function (){
    Route::get('/products', 'index')->name('products.index');
    Route::post('/products', 'store')->name('products.store');
});
