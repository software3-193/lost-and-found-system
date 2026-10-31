<?php

use App\Http\Controllers\AuthController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route(auth()->check() ? 'dashboard' : 'login'));

// صفحات الزائر فقط
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// صفحات المستخدم المسجّل
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');

    // مثال لصفحة خاصة بالمدير (ستبني عليها ميزة الإشراف لاحقاً)
    Route::get('/admin', fn () => view('admin'))
        ->middleware(RoleMiddleware::class . ':admin')
        ->name('admin.home');
});
