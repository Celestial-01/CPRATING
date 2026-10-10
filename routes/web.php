
<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TopicController;
use Illuminate\Support\Facades\Route;

// Home
Route::get('/home', [HomeController::class, 'index'])->name('home');

// Course detail (user side)
Route::get('/courses/{code}', [CourseController::class, 'show'])
    ->name('courses.show');

// Admin Dashboard
Route::get('/', [AdminController::class, 'showDashboard'])
    ->name('admin.dashboard');

Route::get('/admin', [AdminController::class, 'showDashboard'])
    ->name('admin.home');

// Admin Courses
Route::prefix('admin/courses')->group(function () {
    Route::get('/', [CourseController::class, 'index'])
        ->name('courses.index');

    Route::get('/create', [CourseController::class, 'create'])
        ->name('courses.create');

    Route::post('/', [CourseController::class, 'store'])
        ->name('courses.store');

    Route::get('/search', [CourseController::class, 'search'])
        ->name('courses.search');

    Route::get('/{course}/edit', [CourseController::class, 'edit'])
        ->name('courses.edit');

    Route::put('/{course}', [CourseController::class, 'update'])
        ->name('courses.update');

    Route::delete('/{course}', [CourseController::class, 'destroy'])
        ->name('courses.destroy');

    Route::patch('/{course}/inline', [CourseController::class, 'inlineUpdate'])
        ->name('courses.inline-update');

    Route::post('/{course}/image', [CourseController::class, 'updateImage'])
        ->name('courses.image');
});

// Admin Teachers
Route::prefix('admin/teachers')->group(function () {
    Route::get('/', [TeacherController::class, 'index'])
        ->name('teachers.index');

    Route::get('/create', [TeacherController::class, 'create'])
        ->name('teachers.create');

    Route::post('/', [TeacherController::class, 'store'])
        ->name('teachers.store');

    Route::get('/search', [TeacherController::class, 'search'])
        ->name('teachers.search');

    Route::get('/{teacher}/edit', [TeacherController::class, 'edit'])
        ->name('teachers.edit');

    Route::put('/{teacher}', [TeacherController::class, 'update'])
        ->name('teachers.update');

    Route::delete('/{teacher}', [TeacherController::class, 'destroy'])
        ->name('teachers.destroy');
});

// Admin Topics
Route::prefix('admin/topics')->group(function () {
    Route::get('/', [TopicController::class, 'index'])
        ->name('topics.index');

    Route::post('/', [TopicController::class, 'store'])
        ->name('topics.store');

    Route::get('/search', [TopicController::class, 'search'])
        ->name('topics.search');

    Route::put('/{topic}', [TopicController::class, 'update'])
        ->name('topics.update');

    Route::delete('/{topic}', [TopicController::class, 'destroy'])
        ->name('topics.destroy');
});

// Guest Authentication
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('auth.login');

    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:auth-login')
        ->name('auth.login.store');

    Route::get('/auth/google/redirect', [AuthController::class, 'redirectToGoogle'])
        ->name('auth.google.redirect');

    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])
        ->name('auth.google.callback');

    Route::get('/register', [AuthController::class, 'showRegister'])
        ->name('auth.register');

    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:auth-register')
        ->name('auth.register.store');

    Route::post('/verify-email/resend', [AuthController::class, 'resendRegistrationVerification'])
        ->middleware('throttle:registration-verification')
        ->name('auth.verify-email.resend');

    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])
        ->name('auth.forgot-password');

    Route::post('/forgot-password', [AuthController::class, 'sendPasswordResetLink'])
        ->middleware('throttle:password-reset')
        ->name('auth.forgot-password.store');

    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])
        ->name('password.reset');

    Route::post('/reset-password', [AuthController::class, 'resetPassword'])
        ->name('auth.reset-password.store');

    Route::get('/set-password', [AuthController::class, 'showSetPassword'])
        ->name('auth.set-password');

    Route::post('/set-password', [AuthController::class, 'setPassword'])
        ->name('auth.set-password.store');

    Route::get('/set-username', [AuthController::class, 'showSetUsername'])
        ->name('auth.set-username');

    Route::post('/set-username', [AuthController::class, 'setUsername'])
        ->name('auth.set-username.store');
});

// Email Verification
Route::get('/verify-email', [AuthController::class, 'showVerifyEmail'])
    ->name('auth.verify-email');

Route::get('/register/verify/{registration}/{token}', [AuthController::class, 'verifyRegistration'])
    ->middleware('signed')
    ->name('auth.registration.verify');

// Logout
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('auth.logout');

// Password Reset Confirmation
Route::get('/password-reset/sent', [AuthController::class, 'showPasswordResetSent'])
    ->name('auth.password-reset-sent');
