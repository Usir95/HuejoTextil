<?php
// routes/Administrador.php

use App\Http\Controllers\Administrador\Sistema\RolesPermisosController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::resource('/RolesPermisos', RolesPermisosController::class)->names('RolesPermisos');

