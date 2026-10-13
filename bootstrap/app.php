<?php

use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\EnsureUserProfileIsComplete;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Routing\Exceptions\InvalidSignatureException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(fn () => route('auth.login'));
        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class,
            'profile.complete' => EnsureUserProfileIsComplete::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $exceptions->renderable(function (InvalidSignatureException $exception, Request $request) {
            if (! $request->routeIs('auth.registration.verify')) {
                return null;
            }

            $hasPendingRegistration = $request->session()->has('pending_registration_id')
                && $request->session()->has('registration_email');

            return redirect()
                ->route($hasPendingRegistration ? 'auth.verify-email' : 'auth.register')
                ->with('verification_link_error', 'ลิงก์ยืนยันอีเมลหมดอายุหรือไม่ถูกต้อง กรุณาขอลิงก์ใหม่');
        });
    })->create();
