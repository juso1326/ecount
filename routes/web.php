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
        
        // 方案管理
        Route::resource('plans', PlanController::class);
        Route::post('plans/{plan}/toggle-active', [PlanController::class, 'toggleActive'])->name('plans.toggle-active');
    });
});

// API 路由（使用 Sanctum）
Route::prefix('api/superadmin')->name('api.superadmin.')->group(function () {
    Route::post('login', [AuthController::class, 'apiLogin']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'apiLogout']);
    });
});

// 租戶路由（根路徑，由子域名識別）
Route::middleware(['web', 'universal'])->group(function () {
    // 認證路由（不需登入）
    Route::middleware('guest')->group(function () {
        Route::get('login', [\App\Http\Controllers\Tenant\AuthController::class, 'showLogin'])->name('tenant.login');
        Route::post('login', [\App\Http\Controllers\Tenant\AuthController::class, 'login'])->name('tenant.login.submit');
        Route::get('register', [\App\Http\Controllers\Tenant\AuthController::class, 'showRegister'])->name('tenant.register');
        Route::post('register', [\App\Http\Controllers\Tenant\AuthController::class, 'register'])->name('tenant.register.submit');
    });

    // 需要認證的租戶路由
    Route::middleware(['auth'])->group(function () {
        Route::post('logout', [\App\Http\Controllers\Tenant\AuthController::class, 'logout'])->name('tenant.logout');
        
        // 儀表板
        Route::get('/', [\App\Http\Controllers\Tenant\DashboardController::class, 'index'])->name('tenant.dashboard');
        Route::get('dashboard', [\App\Http\Controllers\Tenant\DashboardController::class, 'index']);
        
        // 公司管理
        Route::resource('companies', \App\Http\Controllers\Tenant\CompanyController::class)->names([
            'index' => 'tenant.companies.index',
            'create' => 'tenant.companies.create',
            'store' => 'tenant.companies.store',
            'show' => 'tenant.companies.show',
            'edit' => 'tenant.companies.edit',
            'update' => 'tenant.companies.update',
            'destroy' => 'tenant.companies.destroy',
        ]);
        
        // 部門管理
        Route::resource('departments', \App\Http\Controllers\Tenant\DepartmentController::class)->names([
            'index' => 'tenant.departments.index',
            'create' => 'tenant.departments.create',
            'store' => 'tenant.departments.store',
            'show' => 'tenant.departments.show',
            'edit' => 'tenant.departments.edit',
            'update' => 'tenant.departments.update',
            'destroy' => 'tenant.departments.destroy',
        ]);
        
        // 專案管理
        Route::resource('projects', \App\Http\Controllers\Tenant\ProjectController::class)->names([
            'index' => 'tenant.projects.index',
            'create' => 'tenant.projects.create',
            'store' => 'tenant.projects.store',
            'show' => 'tenant.projects.show',
            'edit' => 'tenant.projects.edit',
            'update' => 'tenant.projects.update',
            'destroy' => 'tenant.projects.destroy',
        ]);
    });
});
