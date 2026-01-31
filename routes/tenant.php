<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
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
