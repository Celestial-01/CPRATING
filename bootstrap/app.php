<?php

use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\EnsureUserProfileIsComplete;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

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
        $isAuthPath = static fn (Request $request): bool => $request->is(
            'login',
            'register',
            'verify-email',
            'verify-email/resend',
            'register/verify/*',
            'set-password',
            'set-username',
            'forgot-password',
            'password-reset/sent',
            'reset-password',
            'reset-password/*',
            'auth/google/*',
        );

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

        $exceptions->renderable(function (HttpExceptionInterface $exception, Request $request) use ($isAuthPath) {
            if ($exception->getStatusCode() !== 419 || ! $isAuthPath($request) || $request->expectsJson()) {
                return null;
            }

            return response()->view('auth.error-recovery', [
                'errorTitle' => 'หน้านี้หมดอายุแล้ว',
                'errorMessage' => 'เพื่อความปลอดภัย กรุณาเปิดหน้าใหม่และลองทำรายการอีกครั้ง',
                'recoveryRoute' => $request->is('register', 'verify-email*', 'register/verify/*', 'set-password', 'set-username')
                    ? 'auth.register'
                    : ($request->is('forgot-password', 'password-reset/*', 'reset-password*') ? 'auth.forgot-password' : 'auth.login'),
                'recoveryLabel' => 'กลับไปเริ่มใหม่',
            ], 419);
        });

        $exceptions->renderable(function (Throwable $exception, Request $request) use ($isAuthPath) {
            if (! $isAuthPath($request)
                || $request->expectsJson()
                || $exception instanceof HttpExceptionInterface
                || $exception instanceof ValidationException
                || $exception instanceof AuthenticationException
                || $exception instanceof HttpResponseException) {
                return null;
            }

            $recoveryRoute = match (true) {
                $request->is('register', 'register/verify/*', 'verify-email*', 'set-password', 'set-username') => 'auth.register',
                $request->is('forgot-password', 'password-reset/*', 'reset-password*') => 'auth.forgot-password',
                default => 'auth.login',
            };

            return response()->view('auth.error-recovery', [
                'errorTitle' => 'ดำเนินการไม่สำเร็จ',
                'errorMessage' => 'ระบบขัดข้องชั่วคราว กรุณาลองอีกครั้ง หากยังพบปัญหาให้กลับมาภายหลัง',
                'recoveryRoute' => $recoveryRoute,
                'recoveryLabel' => 'กลับไปลองอีกครั้ง',
            ], 500);
        });

        $exceptions->renderable(function (HttpExceptionInterface $exception, Request $request) use ($isAuthPath) {
            if (! in_array($exception->getStatusCode(), [404, 405], true)
                || ! $isAuthPath($request)
                || $request->expectsJson()) {
                return null;
            }

            return response()->view('auth.error-recovery', [
                'errorTitle' => 'ไม่พบลิงก์นี้แล้ว',
                'errorMessage' => 'ลิงก์อาจไม่ครบหรือถูกเปลี่ยนไป กรุณากลับไปขอลิงก์ใหม่',
                'recoveryRoute' => $request->is('register/verify/*') ? 'auth.register' : 'auth.forgot-password',
                'recoveryLabel' => 'กลับไปขอลิงก์ใหม่',
            ], $exception->getStatusCode());
        });
    })->create();
