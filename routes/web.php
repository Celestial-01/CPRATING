<?php

use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\TeacherController as AdminTeacherController;
use App\Http\Controllers\Admin\TopicController as AdminTopicController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseCatalogController;
use App\Http\Controllers\CourseRatingController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudyPlanController;
use Illuminate\Support\Facades\Route;

Route::middleware('profile.complete')->group(function (): void {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/home', [HomeController::class, 'index'])->name('home.index');
    Route::get('/courses', [CourseCatalogController::class, 'index'])->name('courses.index');
    Route::get('/plans', [StudyPlanController::class, 'index'])->name('plans.index');
    Route::get('/history', [HistoryController::class, 'index'])->name('history.index');
    Route::get('/courses/{code}', [CourseCatalogController::class, 'show'])->name('courses.show');
    Route::post('/courses/{code}/rating', [CourseRatingController::class, 'store'])
        ->middleware('auth')
        ->name('courses.rating.store');
});

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->as('admin.')
    ->group(function (): void {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('courses/search', [AdminCourseController::class, 'search'])->name('courses.search');
        Route::patch('courses/{course}/inline', [AdminCourseController::class, 'inlineUpdate'])->name('courses.inline-update');
        Route::post('courses/{course}/image', [AdminCourseController::class, 'updateImage'])->name('courses.image');
        Route::resource('courses', AdminCourseController::class)->except(['show']);
        Route::get('teachers/search', [AdminTeacherController::class, 'search'])->name('teachers.search');
        Route::resource('teachers', AdminTeacherController::class)->except(['show']);
        Route::get('topics/search', [AdminTopicController::class, 'search'])->name('topics.search');
        Route::resource('topics', AdminTopicController::class)->only(['index', 'store', 'update', 'destroy']);
    });

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

Route::middleware('auth')->group(function (): void {
    Route::get('/profile', [ProfileController::class, 'show'])->middleware('profile.complete')->name('profile.show');
    Route::get('/profile/complete', [ProfileController::class, 'edit'])->name('profile.complete');
    Route::post('/profile/complete', [ProfileController::class, 'update'])->name('profile.complete.store');
    Route::get('/profile/complete/success', [ProfileController::class, 'success'])->name('profile.complete.success');
});
Route::get('/verify-email', [AuthController::class, 'showVerifyEmail'])
    ->middleware('guest')
    ->name('auth.verify-email');
Route::post('/verify-email/resend', [AuthController::class, 'resendRegistrationVerification'])
    ->middleware(['guest', 'throttle:registration-verification'])
    ->name('auth.verify-email.resend');
Route::get('/register/verify/{registration}/{token}', [AuthController::class, 'verifyRegistration'])
    ->middleware(['guest', 'signed'])
    ->missing(fn () => redirect()->route('auth.register')
        ->with('verification_link_error', 'ไม่พบลิงก์ยืนยันนี้แล้ว กรุณาขอลิงก์ใหม่'))
    ->name('auth.registration.verify');
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])
    ->middleware('guest')
    ->name('auth.forgot-password');
Route::post('/forgot-password', [AuthController::class, 'sendPasswordResetLink'])
    ->middleware(['guest', 'throttle:password-reset'])
    ->name('auth.forgot-password.store');
Route::get('/password-reset/sent', [AuthController::class, 'showPasswordResetSent'])
    ->middleware('guest')
    ->name('auth.password-reset-sent');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])
    ->middleware('guest')
    ->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])
    ->middleware('guest')
    ->name('auth.reset-password.store');

Route::middleware('guest')->group(function () {
    Route::get('/set-password', [AuthController::class, 'showSetPassword'])->name('auth.set-password');
    Route::post('/set-password', [AuthController::class, 'setPassword'])->name('auth.set-password.store');
    Route::get('/set-username', [AuthController::class, 'showSetUsername'])->name('auth.set-username');
    Route::post('/set-username', [AuthController::class, 'setUsername'])->name('auth.set-username.store');
});
