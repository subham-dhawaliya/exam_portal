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
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'user' => \App\Http\Middleware\UserMiddleware::class,
            'status' => \App\Http\Middleware\CheckUserStatus::class,
            'admin.auth' => \App\Http\Middleware\AdminAuthenticate::class,
            'admin.guest' => \App\Http\Middleware\RedirectIfAdminAuthenticated::class,
            'block.face.api' => \App\Http\Middleware\BlockFaceApiForAdmin::class,
        ]);
        
        // Exclude face-scan API routes from CSRF verification
        // These routes use session-based tokens for security instead
        $middleware->validateCsrfTokens(except: [
            'api/face-scan/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
