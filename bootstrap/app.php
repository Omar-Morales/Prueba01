<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        api: __DIR__.'/../routes/api.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
            $middleware->appendToGroup('web', \App\Http\Middleware\PreventBackHistory::class);

        $middleware->alias([
        'auth' => \App\Http\Middleware\Authenticate::class,
        'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
        'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
        'active' => \App\Http\Middleware\EnsureUserIsActive::class,
        // puedes registrar más alias aquí
    ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
