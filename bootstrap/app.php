<?php

use App\Http\Middleware\EnsureNotInstalled;
use App\Http\Middleware\EnsureSetup;
use App\Http\Middleware\RestrictUserAccess;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'not.installed' => EnsureNotInstalled::class,
            'restrict.user' => RestrictUserAccess::class,
            'ensure.setup' => EnsureSetup::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
