<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
    // Redireccionar la ruta raiz a '/login'
    // para que el frontend se haga cargo
});
