<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        // Redirect unauthenticated users to the correct login page
        $middleware->redirectGuestsTo(function (Request $request) {
            if (str_starts_with($request->path(), 'portal')) {
                return route('client.login');
            }
            return route('login');
        });

        // Middleware aliases
        $middleware->alias([
            'client.auth' => \App\Http\Middleware\ClientAuthenticated::class,
            'module' => \App\Http\Middleware\ModuleAccess::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();