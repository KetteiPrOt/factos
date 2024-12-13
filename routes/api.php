<?php

use App\Http\Controllers\Receipts\Invoices\Controller as InvoiceController;
use App\Http\Controllers\Receipts\TypeController as ReceiptTypeController;
use App\Http\Controllers\Auth\Controller as AuthController;
use App\Http\Controllers\Products\Controller as ProductController;
use App\Http\Controllers\Establishments\Controller as EstablishmentController;
use App\Http\Controllers\Establishments\IssuancePointController;
use App\Http\Controllers\Receipts\Invoices\PayMethodController;
use App\Http\Controllers\Persons\Controller as PersonController;
use App\Http\Controllers\Persons\IdentificationTypeController;
use App\Http\Controllers\Products\IceTypeController;
use App\Http\Controllers\Products\VatRateController;
use App\Http\Controllers\Profile\Controller as ProfileController;
use App\Http\Controllers\Profile\CertificateController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/user', function(){
    return Auth::user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->controller(ProfileController::class)->group(function (){
    Route::get('/profile', 'show')->name('profile.show');
    Route::put('/profile', 'update')->name('profile.update');
    Route::put('/profile/password', 'updatePassword')->name('profile.update-password');
});

Route::middleware(['auth:sanctum'])->controller(CertificateController::class)->group(function (){
    Route::get('/certificate', 'show')->name('certificate.show');
    Route::put('/certificate', 'update')->name('certificate.update');
});

Route::middleware(['auth:sanctum'])->controller(AuthController::class)->group(function (){
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware(['auth:sanctum'])->controller(ProductController::class)->group(function (){
    Route::get('/products/search', 'search')->name('products.search');
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
    Route::get('/establishments/{establishment}', 'show')->name('establishments.show');
    Route::put('/establishments/{establishment}', 'update')->name('establishments.update');
    Route::delete('/establishments/{establishment}', 'destroy')->name('establishments.destroy');
});

Route::middleware(['auth:sanctum'])->get('/receipt-types', [ReceiptTypeController::class, 'index']);

Route::middleware(['auth:sanctum'])->controller(IssuancePointController::class)->group(function (){
    Route::get('/issuance-points/{establishment}/choose', 'choose')->name('issuance-points.choose');
    Route::get('/issuance-points/{establishment}', 'index')->name('issuance-points.index');
    Route::post('/issuance-points/{establishment}', 'store')->name('issuance-points.store');
    Route::get('/issuance-points/show/{issuancePoint}', 'show')->name('issuance-points.show');
    Route::put('/issuance-points/{issuancePoint}', 'update')->name('issuance-points.update');
    // Route::delete('/issuance-points/{issuancePoint}', 'destroy')->name('issuance-points.destroy');
});

Route::middleware(['auth:sanctum'])->get(
    '/identification-types',
    [IdentificationTypeController::class, 'index']
);

Route::middleware(['auth:sanctum'])->controller(PersonController::class)->group(function (){
    Route::get('/persons/search', 'search')->name('persons.search');
    Route::get('/persons', 'index')->name('persons.index');
    Route::post('/persons', 'store')->name('persons.store');
    Route::get('/persons/{person}', 'show')->name('persons.show');
    Route::put('/persons/{person}', 'update')->name('persons.update');
    Route::delete('/persons/{person}', 'destroy')->name('persons.destroy');
    Route::delete('/persons', 'destroyAll')->name('persons.destroy-all');
});

Route::middleware(['auth:sanctum'])->get('/pay-methods', [PayMethodController::class, 'index'])->name('pay-methods.index');

Route::middleware(['auth:sanctum'])->controller(InvoiceController::class)->group(function(){
    Route::post('/receipts/invoices', 'issue')->name('invoices.issue');
});
