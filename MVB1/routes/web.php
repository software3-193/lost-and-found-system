<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
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
    Route::get('/dashboard', [ItemController::class, 'index'])->name('dashboard');
    Route::post('/items', [ItemController::class, 'store'])->name('items.store');
    Route::post('/items/{item}/claim', [ItemController::class, 'claim'])->name('items.claim');

    Route::prefix('admin')->name('admin.')->middleware(RoleMiddleware::class . ':admin')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('home');
        Route::patch('/items/{item}/status', [AdminController::class, 'updateItemStatus'])->name('items.status');
        Route::patch('/claims/{claim}/status', [AdminController::class, 'updateClaimStatus'])->name('claims.status');
        Route::patch('/claims/{claim}/delivered', [AdminController::class, 'markDelivered'])->name('claims.delivered');
    });
});
