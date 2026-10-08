<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/courses/{code}', [CourseController::class, 'show'])->name('courses.show');

Route::get('/login', [AuthController::class, 'showLogin'])->name('auth.login');
Route::get('/register', [AuthController::class, 'showRegister'])->name('auth.register');
Route::get('/verify-email', [AuthController::class, 'showVerifyEmail'])->name('auth.verify-email');
Route::get('/set-password', [AuthController::class, 'showSetPassword'])->name('auth.set-password');
Route::get('/set-username', [AuthController::class, 'showSetUsername'])->name('auth.set-username');
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('auth.forgot-password');
Route::get('/password-reset/sent', [AuthController::class, 'showPasswordResetSent'])->name('auth.password-reset-sent');
Route::get('/reset-password', [AuthController::class, 'showResetPassword'])->name('auth.reset-password');
