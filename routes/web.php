<?php

use App\Http\Controllers\SuperAdmin\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// 超級管理員路由
Route::prefix('superadmin')->name('superadmin.')->group(function () {
    // 登入/登出（不需認證）
    Route::middleware('guest:superadmin')->group(function () {
        Route::get('login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('login', [AuthController::class, 'login']);
    });

    // 需要認證的路由
    Route::middleware('auth:superadmin')->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        
        // 儀表板
        Route::get('dashboard', function () {
            return view('superadmin.dashboard');
        })->name('dashboard');
    });
});

// API 路由（使用 Sanctum）
Route::prefix('api/superadmin')->name('api.superadmin.')->group(function () {
    Route::post('login', [AuthController::class, 'apiLogin']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'apiLogout']);
    });
});
