<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('auth-login', function (Request $request): Limit {
            return Limit::perMinute(5)
                ->by($this->rateLimitKey($request))
                ->response(fn () => back()
                    ->withInput($request->only('email'))
                    ->withErrors(['email' => 'พยายามเข้าสู่ระบบบ่อยเกินไป กรุณารอสักครู่แล้วลองใหม่']));
        });

        RateLimiter::for('registration-verification', function (Request $request): Limit {
            $email = $this->normalizedEmail($request->session()->get('registration_email'));

            return Limit::perMinute(3)
                ->by($this->rateLimitKey($request, $email))
                ->response(fn () => redirect()
                    ->route('auth.verify-email')
                    ->with('verification_resend_error', 'ส่งบ่อยเกินไป กรุณารอสักครู่แล้วลองใหม่'));
        });

        RateLimiter::for('auth-register', function (Request $request): Limit {
            return Limit::perMinute(3)
                ->by($this->rateLimitKey($request))
                ->response(fn () => back()
                    ->withInput($request->only('email'))
                    ->withErrors(['email' => 'ส่งลิงก์สมัครสมาชิกบ่อยเกินไป กรุณารอสักครู่แล้วลองใหม่']));
        });

        RateLimiter::for('password-reset', function (Request $request): Limit {
            return Limit::perMinute(3)
                ->by($this->rateLimitKey($request))
                ->response(fn () => back()
                    ->withInput($request->only('email'))
                    ->withErrors(['email' => 'ส่งคำขอบ่อยเกินไป กรุณารอสักครู่แล้วลองใหม่']));
        });
    }

    private function rateLimitKey(Request $request, ?string $email = null): string
    {
        return $request->ip().'|'.$this->normalizedEmail($email ?? $request->input('email'));
    }

    private function normalizedEmail(mixed $email): string
    {
        return strtolower(trim((string) $email));
    }
}
