<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/courses/{code}', [CourseController::class, 'show'])->name('courses.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('auth.login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:auth-login')
        ->name('auth.login.store');
    Route::get('/auth/google/redirect', [AuthController::class, 'redirectToGoogle'])->name('auth.google.redirect');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('auth.register');
    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:auth-register')
        ->name('auth.register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('auth.logout');
Route::get('/verify-email', [AuthController::class, 'showVerifyEmail'])->name('auth.verify-email');
Route::post('/verify-email/resend', [AuthController::class, 'resendRegistrationVerification'])
    ->middleware(['guest', 'throttle:registration-verification'])
    ->name('auth.verify-email.resend');
Route::get('/register/verify/{registration}/{token}', [AuthController::class, 'verifyRegistration'])
    ->middleware('signed')
    ->name('auth.registration.verify');
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('auth.forgot-password');
Route::post('/forgot-password', [AuthController::class, 'sendPasswordResetLink'])
    ->middleware(['guest', 'throttle:password-reset'])
    ->name('auth.forgot-password.store');
Route::get('/password-reset/sent', [AuthController::class, 'showPasswordResetSent'])->name('auth.password-reset-sent');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])
    ->middleware('guest')
    ->name('auth.reset-password.store');

Route::middleware('guest')->group(function () {
    Route::get('/set-password', [AuthController::class, 'showSetPassword'])->name('auth.set-password');
    Route::post('/set-password', [AuthController::class, 'setPassword'])->name('auth.set-password.store');
    Route::get('/set-username', [AuthController::class, 'showSetUsername'])->name('auth.set-username');
    Route::post('/set-username', [AuthController::class, 'setUsername'])->name('auth.set-username.store');
});
