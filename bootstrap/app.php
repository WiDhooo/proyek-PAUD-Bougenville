<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        // Alias Middleware Role
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);

        // Redirect authenticated users to their respective dashboards instead of /home
        $middleware->redirectUsersTo(function (\Illuminate\Http\Request $request) {
            if (auth()->check()) {
                if (auth()->user()->role === 'admin') {
                    return route('admin.dashboard');
                }
                if (auth()->user()->role === 'guru') {
                    return route('guru.dashboard');
                }
            }
            return '/';
        });

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
