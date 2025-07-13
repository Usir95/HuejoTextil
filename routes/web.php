<?php

use App\Http\Controllers\ExampleController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
    Route::get('/modulos', function () {
        return Inertia::render('Administrador/Sistema/Modulos');
    })->name('modulos');
    Route::get('/usuarios', function () {
        return Inertia::render('Administrador/Usuarios');
    })->name('bitacora');
});

Route::resource('Example', ExampleController::class)->names('Example');

