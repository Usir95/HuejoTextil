<?php
// routes/Almacen.php

use App\Http\Controllers\Almacenes\EntradasController;
use App\Http\Controllers\Almacenes\HistoricoEntradasController;
use App\Http\Controllers\Almacenes\PedidosClientesController;
use App\Http\Controllers\Almacenes\ProductosTerminadosController;
use App\Http\Controllers\Almacenes\ReporteEntradasController;
use App\Http\Controllers\Almacenes\SalidasController;
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

Route::resource('/HistoricoEntradas', HistoricoEntradasController::class)->names('HistoricoEntradas');
Route::post('/HistoricoEntradas/FiltrarEntradas', [HistoricoEntradasController::class, 'FiltrarEntradas'])->name('HistoricoEntradas.FiltrarEntradas');
Route::post('/HistoricoEntradas/ExpotarPedido', [HistoricoEntradasController::class, 'ExpotarPedido'])->name('HistoricoEntradas.ExpotarPedido');
Route::post('/HistoricoEntradas/ObtenerEntrada', [HistoricoEntradasController::class, 'ObtenerEntrada'])->name('HistoricoEntradas.ObtenerEntrada');

Route::resource('/ReporteEntradas', ReporteEntradasController::class)->names('ReporteEntradas');

Route::resource('/Entradas', EntradasController::class)->names('Entradas');
Route::post('/Entradas/FiltrarPorducto', [EntradasController::class, 'FiltrarPorducto'])->name('Entradas.FiltrarPorducto');

Route::resource('/ProductosTerminados', ProductosTerminadosController::class)->names('ProductosTerminados');

Route::resource('/Salidas', SalidasController::class)->names('Salidas');
Route::get('/Salidas/buscar/{movimiento}', [SalidasController::class, 'BuscarMovimiento'])->name('Salidas.BuscarMovimiento');
Route::post('/Salidas/{movimiento}/salida', [SalidasController::class, 'RegistrarSalida'])->name('Salidas.RegistrarSalida');
