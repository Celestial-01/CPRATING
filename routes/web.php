<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;


Route::get('/home', [HomeController::class, 'index'])->name('home'); /*  แก้กลับด้วยนะ ปก*/
Route::get('/courses/{code}', [CourseController::class, 'show'])->name('courses.show');

/******** Admin / Course / Teacher / Topic *********/
// Admin Dashboard
Route::get('/', [AdminController::class, 'showDashboard'])->name('admin.dashboard');

/******** Admin *********/
Route::get('/admin', [AdminController::class, 'showDashboard'])->name('admin.dashboard');

Route::prefix('admin/courses')->group(function () {
    Route::get('/', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/create', [CourseController::class, 'create'])->name('courses.create');
    Route::post('/', [CourseController::class, 'store'])->name('courses.store');
    Route::get('/search', [CourseController::class, 'search'])->name('courses.search');
    Route::get('/{course}/edit', [CourseController::class, 'edit'])->name('courses.edit');
    Route::put('/{course}', [CourseController::class, 'update'])->name('courses.update');
    Route::delete('/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');
    Route::patch('/{course}/inline', [CourseController::class, 'inlineUpdate'])->name('courses.inline-update');
    Route::post('/{course}/image', [CourseController::class, 'updateImage'])->name('courses.image');
});

Route::prefix('admin/teachers')->group(function () {
    Route::get('/', [TeacherController::class, 'index'])->name('teachers.index');
    Route::get('/create', [TeacherController::class, 'create'])->name('teachers.create');
    Route::post('/', [TeacherController::class, 'store'])->name('teachers.store');
    Route::get('/search', [TeacherController::class, 'search'])->name('teachers.search');
    Route::get('/{teacher}/edit', [TeacherController::class, 'edit'])->name('teachers.edit');
    Route::put('/{teacher}', [TeacherController::class, 'update'])->name('teachers.update');
    Route::delete('/{teacher}', [TeacherController::class, 'destroy'])->name('teachers.destroy');
});

Route::prefix('admin/topics')->group(function () {
    Route::get('/', [TopicController::class, 'index'])->name('topics.index');
    Route::post('/', [TopicController::class, 'store'])->name('topics.store');
    Route::get('/search', [TopicController::class, 'search'])->name('topics.search');
    Route::put('/{topic}', [TopicController::class, 'update'])->name('topics.update');
    Route::delete('/{topic}', [TopicController::class, 'destroy'])->name('topics.destroy');
});


Route::get('/login', [AuthController::class, 'showLogin'])->name('auth.login');
Route::get('/register', [AuthController::class, 'showRegister'])->name('auth.register');
Route::get('/verify-email', [AuthController::class, 'showVerifyEmail'])->name('auth.verify-email');
Route::get('/set-password', [AuthController::class, 'showSetPassword'])->name('auth.set-password');
Route::get('/set-username', [AuthController::class, 'showSetUsername'])->name('auth.set-username');
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('auth.forgot-password');
Route::get('/password-reset/sent', [AuthController::class, 'showPasswordResetSent'])->name('auth.password-reset-sent');
Route::get('/reset-password', [AuthController::class, 'showResetPassword'])->name('auth.reset-password');
