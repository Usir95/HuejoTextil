<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        using: function () {
            Route::middleware(['web'])
                ->group(base_path('routes/web.php'));

            Route::middleware(['web', 'auth'])
                ->prefix('Administrador')
                ->group(base_path('routes/Administrador.php'));

            Route::middleware(['web', 'auth'])
                ->prefix('Almacen')
                ->group(base_path('routes/Almacen.php'));

            Route::middleware(['web'])
                ->prefix('Publico')
                ->group(base_path('routes/Public.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
