<?php
// routes/Administrador.php

use App\Http\Controllers\Administrador\MenuAdministradorController;
use App\Http\Controllers\Administrador\Sistema\ModulosController;
use App\Http\Controllers\Administrador\Sistema\RolesPermisosController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::resource('/RolesPermisos', RolesPermisosController::class)->names('RolesPermisos');

Route::resource('/Modulos', ModulosController::class)->names('Modulos');
Route::post('/Modulos/ObtenCategoriasModulos', [ModulosController::class, 'ObtenCategoriasModulos'])->name('Modulos.ObtenCategoriasModulos');
Route::post('/Modulos/IconosFontAwesome', [ModulosController::class, 'IconosFontAwesome'])->name('Modulos.IconosFontAwesome');
