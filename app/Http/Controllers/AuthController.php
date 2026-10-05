<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function showVerifyEmail(): View
    {
        return view('auth.verify-email');
    }

    public function showSetPassword(): View
    {
        return view('auth.set-password');
    }

    public function showSetUsername(): View
    {
        $suggestedUsername = Str::lower(Str::random(8));

        return view('auth.set-username', compact('suggestedUsername'));
    }

    public function showForgotPassword(): View
    {
        return view('auth.forgot-password');
    }

    public function showPasswordResetSent(): View
    {
        return view('auth.password-reset-sent');
    }

    public function showResetPassword(): View
    {
        return view('auth.reset-password');
    }
}
