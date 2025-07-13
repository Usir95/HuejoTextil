<?php
// routes/Almacen.php

use App\Http\Controllers\Catalogos\ClientesController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::resource('/Clientes', ClientesController::class)->names('Clientes');
