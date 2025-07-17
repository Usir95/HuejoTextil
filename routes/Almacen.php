<?php
// routes/Almacen.php

use App\Http\Controllers\Almacenes\EntradasController;
use App\Http\Controllers\Almacenes\PedidosClientesController;
use App\Http\Controllers\Catalogos\ClientesController;
use App\Http\Controllers\Catalogos\ColoresController;
use App\Http\Controllers\Catalogos\TiposProductosController;
use App\Http\Controllers\ProductosController;
use App\Models\Catalogos\Productos;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::resource('/Clientes', ClientesController::class)->names('Clientes');
Route::resource('/TiposProductos', TiposProductosController::class)->names('TiposProductos');
Route::resource('/Productos', ProductosController::class)->names('Productos');
Route::resource('/Colores', ColoresController::class)->names('Colores');
Route::resource('/Pedidos', PedidosClientesController::class)->names('Pedidos');


Route::resource('/Entradas', EntradasController::class)->names('Entradas');
