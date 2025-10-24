<?php

use App\Http\Controllers\ExampleController;
use Carbon\Carbon;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
include 'Administrador.php';
include 'Almacen.php';
Route::get('/', function () {
    return redirect()->route('login');
});


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/dashboard', function () {
        // === Bloqueo automático el 15 de noviembre ===
        $fechaLimite = Carbon::create(2025, 11, 15, 23, 59, 59);
        if (now()->greaterThan($fechaLimite)) {
            abort(403, 'Licencia expirada. Contacta al administrador.');
        }

        return Inertia::render('Dashboard');
    })->name('dashboard');

});

Route::resource('Example', ExampleController::class)->names('Example');

