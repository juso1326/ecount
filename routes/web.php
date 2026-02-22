<?php

use App\Http\Controllers\SuperAdmin\AuthController;
use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\SuperAdmin\PlanController;
use App\Http\Controllers\SuperAdmin\TenantController;
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
        Route::get('captcha/refresh', [AuthController::class, 'refreshCaptcha'])->name('captcha.refresh');
    });

    // 需要認證的路由
    Route::middleware('auth:superadmin')->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        
        // 儀表板
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // 租戶管理
        Route::resource('tenants', TenantController::class);
        Route::post('tenants/{tenant}/suspend', [TenantController::class, 'suspend'])->name('tenants.suspend');
        Route::post('tenants/{tenant}/activate', [TenantController::class, 'activate'])->name('tenants.activate');
        Route::post('tenants/{tenant}/renew', [TenantController::class, 'renew'])->name('tenants.renew');
        
        // 方案管理
        Route::resource('plans', PlanController::class);
        Route::post('plans/{plan}/toggle-active', [PlanController::class, 'toggleActive'])->name('plans.toggle-active');

        // Error Log
        Route::get('error-log', [\App\Http\Controllers\SuperAdmin\ErrorLogController::class, 'index'])->name('error-log.index');
        Route::post('error-log/clear', [\App\Http\Controllers\SuperAdmin\ErrorLogController::class, 'clear'])->name('error-log.clear');
    });
});

// API 路由（使用 Sanctum）
Route::prefix('api/superadmin')->name('api.superadmin.')->group(function () {
    Route::post('login', [AuthController::class, 'apiLogin']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'apiLogout']);
    });
});
