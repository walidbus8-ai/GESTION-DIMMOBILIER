<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        
        // 1. استثناء المسارات الحساسة من حماية CSRF لضمان عمل طلبات POST
        $middleware->validateCsrfTokens(except: [
            'api/*',
            'login',
            'register',
            'logout'
        ]);

        // 2. تفعيل الـ CORS كأول Middleware كيخدم باش يجاوب على طلبات الـ OPTIONS
        $middleware->append(\Illuminate\Http\Middleware\HandleCors::class);

        // 3. تفعيل الـ Stateful API لخدمات Sanctum والـ Authentication
        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();