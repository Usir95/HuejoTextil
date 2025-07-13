<?php
// routes/Administrador.php

use App\Http\Controllers\Administrador\MenuAdministradorController;
use App\Http\Controllers\Administrador\Sistema\BitacoraController;
use App\Http\Controllers\Administrador\Sistema\ModulosController;
use App\Http\Controllers\Administrador\Sistema\RolesPermisosController;
use App\Http\Controllers\Administrador\UsuariosController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::resource('/RolesPermisos', RolesPermisosController::class)->names('RolesPermisos');
Route::post('/RolesPermisos/AsignarPermisos', [RolesPermisosController::class, 'AsignarPermisos'])->name('RolesPermisos.AsignarPermisos');

Route::resource('/Modulos', ModulosController::class)->names('Modulos');
Route::post('/Modulos/ObtenCategoriasModulos', [ModulosController::class, 'ObtenCategoriasModulos'])->name('Modulos.ObtenCategoriasModulos');
Route::post('/Modulos/IconosFontAwesome', [ModulosController::class, 'IconosFontAwesome'])->name('Modulos.IconosFontAwesome');

Route::resource('/Bitacora', BitacoraController::class)->names('Bitacora');

Route::resource('/Usuarios', UsuariosController::class)->names('Usuarios');
Route::post('/Usuarios/RestartPassword/{usuario}', [UsuariosController::class, 'RestartPassword'])->name('Usuarios.RestartPassword');
Route::post('/Usuarios/AsignarRolUsuario', [UsuariosController::class, 'AsignarRolUsuario'])->name('Usuarios.AsignarRolUsuario');
