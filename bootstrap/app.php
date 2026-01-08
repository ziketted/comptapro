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
        $middleware->alias([
            'manager' => \App\Http\Middleware\EnsureUserIsManager::class,
            'superadmin' => \App\Http\Middleware\EnsureUserIsSuperAdmin::class,
            'check.subscription' => \App\Http\Middleware\CheckSubscription::class,
        ]);
        $middleware->append(\App\Http\Middleware\TenantMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
