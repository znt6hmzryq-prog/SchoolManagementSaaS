<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Configuration\Exceptions;

return Application::configure(basePath: dirname(__DIR__))

    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware) {

        /*
        |--------------------------------------------------------------------------
        | Middleware Aliases
        |--------------------------------------------------------------------------
        */

        $middleware->alias([
            'check.subscription' => \App\Http\Middleware\CheckSubscription::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'school.scope' => \App\Http\Middleware\EnsureSchoolScope::class,
            'permission' => \App\Http\Middleware\PermissionMiddleware::class,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Prevent Laravel from redirecting to login route
        | (Important for API-only applications)
        |--------------------------------------------------------------------------
        */

        $middleware->redirectGuestsTo(function () {
            return null;
        });
    })

    ->withExceptions(function (Exceptions $exceptions) {
        //
    })

    ->create();