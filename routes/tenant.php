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
    // 未登入時重定向到登入頁
    Route::get('/', function () {
        if (!auth()->check()) {
            return redirect()->route('tenant.login');
        }
        return app(\App\Http\Controllers\Tenant\DashboardController::class)->index();
    })->name('tenant.dashboard');

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
        Route::get('dashboard', [\App\Http\Controllers\Tenant\DashboardController::class, 'index']);
        Route::post('dashboard/announcement', [\App\Http\Controllers\Tenant\DashboardController::class, 'updateAnnouncement'])->name('tenant.dashboard.announcement');
        
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
        
        // 專案成員管理
        Route::post('projects/{project}/members', [\App\Http\Controllers\Tenant\ProjectController::class, 'addMember'])->name('tenant.projects.members.add');
        Route::delete('projects/{project}/members/{user}', [\App\Http\Controllers\Tenant\ProjectController::class, 'removeMember'])->name('tenant.projects.members.remove');
        
        // 使用者管理
        Route::resource('users', \App\Http\Controllers\Tenant\UserController::class)->names([
            'index' => 'tenant.users.index',
            'create' => 'tenant.users.create',
            'store' => 'tenant.users.store',
            'show' => 'tenant.users.show',
            'edit' => 'tenant.users.edit',
            'update' => 'tenant.users.update',
            'destroy' => 'tenant.users.destroy',
        ]);
        Route::post('users/{user}/toggle-active', [\App\Http\Controllers\Tenant\UserController::class, 'toggleActive'])->name('tenant.users.toggle-active');

        // 應收帳款管理
        Route::resource('receivables', \App\Http\Controllers\Tenant\ReceivableController::class)->names([
            'index' => 'tenant.receivables.index',
            'create' => 'tenant.receivables.create',
            'store' => 'tenant.receivables.store',
            'show' => 'tenant.receivables.show',
            'edit' => 'tenant.receivables.edit',
            'update' => 'tenant.receivables.update',
            'destroy' => 'tenant.receivables.destroy',
        ]);

        // 代碼管理
        Route::get('codes', [\App\Http\Controllers\Tenant\CodeController::class, 'index'])->name('tenant.codes.index');
        Route::get('codes/{category}', [\App\Http\Controllers\Tenant\CodeController::class, 'category'])->name('tenant.codes.category');
        Route::get('codes/{category}/create', [\App\Http\Controllers\Tenant\CodeController::class, 'create'])->name('tenant.codes.create');
        Route::post('codes/{category}', [\App\Http\Controllers\Tenant\CodeController::class, 'store'])->name('tenant.codes.store');
        Route::get('codes/{category}/{code}/edit', [\App\Http\Controllers\Tenant\CodeController::class, 'edit'])->name('tenant.codes.edit');
        Route::put('codes/{category}/{code}', [\App\Http\Controllers\Tenant\CodeController::class, 'update'])->name('tenant.codes.update');
        Route::delete('codes/{category}/{code}', [\App\Http\Controllers\Tenant\CodeController::class, 'destroy'])->name('tenant.codes.destroy');

        // 設定管理
        Route::prefix('settings')->name('tenant.settings.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Tenant\SettingsController::class, 'index'])->name('index');
            
            // 公司設定
            Route::get('company', [\App\Http\Controllers\Tenant\SettingsController::class, 'company'])->name('company');
            
            // 代碼管理設定
            Route::get('codes', [\App\Http\Controllers\Tenant\SettingsController::class, 'codes'])->name('codes');
            Route::post('codes', [\App\Http\Controllers\Tenant\SettingsController::class, 'updateCodes'])->name('codes.update');
            
            // 系統設定
            Route::get('system', [\App\Http\Controllers\Tenant\SettingsController::class, 'system'])->name('system');
            
            // 帳號設定
            Route::get('account', [\App\Http\Controllers\Tenant\SettingsController::class, 'account'])->name('account');
            Route::put('account', [\App\Http\Controllers\Tenant\SettingsController::class, 'updateAccount'])->name('account.update');
        });
    });
});
