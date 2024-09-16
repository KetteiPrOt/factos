<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/home');
    // Redireccionar la ruta raiz a '/home'
    // para que el frontend se haga cargo
});
