<?php
// routes/Almacen.php

use App\Http\Controllers\Catalogos\ClientesController;
use App\Http\Controllers\Catalogos\TiposProductosController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::resource('/Clientes', ClientesController::class)->names('Clientes');
Route::resource('/Productos', TiposProductosController::class)->names('Productos');
