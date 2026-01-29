<?php

use App\Http\Controllers\SuperAdmin\AuthController;
use App\Http\Controllers\SuperAdmin\DashboardController;
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
    });
});

// API 路由（使用 Sanctum）
Route::prefix('api/superadmin')->name('api.superadmin.')->group(function () {
    Route::post('login', [AuthController::class, 'apiLogin']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'apiLogout']);
    });
});

// 租戶路由（需要租戶識別）
Route::middleware(['web'])->prefix('tenant')->name('tenant.')->group(function () {
    // 認證路由（不需登入）
    Route::middleware('guest')->group(function () {
        Route::get('login', [\App\Http\Controllers\Tenant\AuthController::class, 'showLogin'])->name('login');
        Route::post('login', [\App\Http\Controllers\Tenant\AuthController::class, 'login']);
        Route::get('register', [\App\Http\Controllers\Tenant\AuthController::class, 'showRegister'])->name('register');
        Route::post('register', [\App\Http\Controllers\Tenant\AuthController::class, 'register']);
    });

    // 需要認證的路由
    Route::middleware('auth')->group(function () {
        Route::post('logout', [\App\Http\Controllers\Tenant\AuthController::class, 'logout'])->name('logout');
        
        // 儀表板
        Route::get('dashboard', [\App\Http\Controllers\Tenant\DashboardController::class, 'index'])->name('dashboard');
        
        // 公司管理
        Route::resource('companies', \App\Http\Controllers\Tenant\CompanyController::class);
        
        // 部門管理
        Route::resource('departments', \App\Http\Controllers\Tenant\DepartmentController::class);
        
        // 專案管理
        Route::resource('projects', \App\Http\Controllers\Tenant\ProjectController::class);
    });
});
