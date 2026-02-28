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
        Route::get('captcha/refresh', [\App\Http\Controllers\Tenant\AuthController::class, 'refreshCaptcha'])->name('tenant.captcha.refresh');
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
        Route::get('companies/export', [\App\Http\Controllers\Tenant\CompanyController::class, 'export'])->name('tenant.companies.export');
        Route::resource('companies', \App\Http\Controllers\Tenant\CompanyController::class)->names([
            'index' => 'tenant.companies.index',
            'create' => 'tenant.companies.create',
            'store' => 'tenant.companies.store',
            'show' => 'tenant.companies.show',
            'edit' => 'tenant.companies.edit',
            'update' => 'tenant.companies.update',
            'destroy' => 'tenant.companies.destroy',
        ]);
        

        // 專案管理
        Route::get('projects', [\App\Http\Controllers\Tenant\ProjectController::class, 'index'])->name('tenant.projects.index');
        Route::get('projects/export', [\App\Http\Controllers\Tenant\ProjectController::class, 'export'])->name('tenant.projects.export');
        Route::get('projects/create', [\App\Http\Controllers\Tenant\ProjectController::class, 'create'])->name('tenant.projects.create');
        Route::post('projects', [\App\Http\Controllers\Tenant\ProjectController::class, 'store'])->name('tenant.projects.store');
        Route::get('projects/{project}', [\App\Http\Controllers\Tenant\ProjectController::class, 'show'])->name('tenant.projects.show');
        Route::get('projects/{project}/edit', [\App\Http\Controllers\Tenant\ProjectController::class, 'edit'])->name('tenant.projects.edit');
        Route::put('projects/{project}', [\App\Http\Controllers\Tenant\ProjectController::class, 'update'])->name('tenant.projects.update');
        Route::patch('projects/{project}/quick-update', [\App\Http\Controllers\Tenant\ProjectController::class, 'quickUpdate'])->name('tenant.projects.quick-update');
        Route::delete('projects/{project}', [\App\Http\Controllers\Tenant\ProjectController::class, 'destroy'])->name('tenant.projects.destroy');
        
        // 專案成員管理
        Route::post('projects/{project}/members', [\App\Http\Controllers\Tenant\ProjectController::class, 'addMember'])->name('tenant.projects.members.add');
        Route::delete('projects/{project}/members/{user}', [\App\Http\Controllers\Tenant\ProjectController::class, 'removeMember'])->name('tenant.projects.members.remove');
        
        // 專案標籤管理
        Route::put('projects/{project}/tags', [\App\Http\Controllers\Tenant\ProjectController::class, 'updateTags'])->name('tenant.projects.tags.update');
        
        // 專案快速新增應收/應付帳款
        Route::post('projects/{project}/receivables/quick-add', [\App\Http\Controllers\Tenant\ProjectController::class, 'quickAddReceivable'])->name('tenant.projects.receivables.quick-add');
        Route::post('projects/{project}/payables/quick-add', [\App\Http\Controllers\Tenant\ProjectController::class, 'quickAddPayable'])->name('tenant.projects.payables.quick-add');
        
        // 快速更新應收/應付帳款
        Route::patch('receivables/{receivable}/quick-update', [\App\Http\Controllers\Tenant\ReceivableController::class, 'quickUpdate'])->name('tenant.receivables.quick-update');
        Route::patch('payables/{payable}/quick-update', [\App\Http\Controllers\Tenant\PayableController::class, 'quickUpdate'])->name('tenant.payables.quick-update');
        
        // 使用者管理
        Route::get('users/export', [\App\Http\Controllers\Tenant\UserController::class, 'export'])->name('tenant.users.export');
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
        
        // 使用者銀行帳戶管理
        Route::post('users/{user}/bank-accounts', [\App\Http\Controllers\Tenant\UserController::class, 'storeBankAccount'])->name('tenant.users.bank-accounts.store');
        Route::put('users/{user}/bank-accounts/{bankAccount}', [\App\Http\Controllers\Tenant\UserController::class, 'updateBankAccount'])->name('tenant.users.bank-accounts.update');
        Route::delete('users/{user}/bank-accounts/{bankAccount}', [\App\Http\Controllers\Tenant\UserController::class, 'destroyBankAccount'])->name('tenant.users.bank-accounts.destroy');
        Route::post('users/{user}/bank-accounts/{bankAccount}/set-default', [\App\Http\Controllers\Tenant\UserController::class, 'setDefaultBankAccount'])->name('tenant.users.bank-accounts.set-default');

        // 應收帳款管理（簡化路由）
        Route::get('receivables', [\App\Http\Controllers\Tenant\ReceivableController::class, 'index'])->name('tenant.receivables.index');
        Route::get('receivables/export', [\App\Http\Controllers\Tenant\ReceivableController::class, 'export'])->name('tenant.receivables.export');
        Route::get('receivables/create', [\App\Http\Controllers\Tenant\ReceivableController::class, 'create'])->name('tenant.receivables.create');
        Route::post('receivables', [\App\Http\Controllers\Tenant\ReceivableController::class, 'store'])->name('tenant.receivables.store');
        Route::get('receivables/{receivable}', [\App\Http\Controllers\Tenant\ReceivableController::class, 'show'])->name('tenant.receivables.show');
        Route::get('receivables/{receivable}/edit', [\App\Http\Controllers\Tenant\ReceivableController::class, 'edit'])->name('tenant.receivables.edit');
        Route::put('receivables/{receivable}', [\App\Http\Controllers\Tenant\ReceivableController::class, 'update'])->name('tenant.receivables.update');
        Route::delete('receivables/{receivable}', [\App\Http\Controllers\Tenant\ReceivableController::class, 'destroy'])->name('tenant.receivables.destroy');
        
        // 應收入帳記錄
        Route::post('receivable-payments/{receivable}', [\App\Http\Controllers\Tenant\ReceivablePaymentController::class, 'store'])->name('tenant.receivable-payments.store');
        Route::get('receivable-payments/{receivable}/list', [\App\Http\Controllers\Tenant\ReceivablePaymentController::class, 'getPayments'])->name('tenant.receivable-payments.list');
        Route::delete('receivable-payments/{payment}', [\App\Http\Controllers\Tenant\ReceivablePaymentController::class, 'destroy'])->name('tenant.receivable-payments.destroy');
        Route::post('receivables/{receivable}/reset-payments', [\App\Http\Controllers\Tenant\ReceivableController::class, 'resetPayments'])->name('tenant.receivables.reset-payments');

        // 應付帳款管理（簡化路由）
        Route::get('payables', [\App\Http\Controllers\Tenant\PayableController::class, 'index'])->name('tenant.payables.index');
        Route::get('payables/export', [\App\Http\Controllers\Tenant\PayableController::class, 'export'])->name('tenant.payables.export');
        Route::get('payables/create', [\App\Http\Controllers\Tenant\PayableController::class, 'create'])->name('tenant.payables.create');
        Route::post('payables', [\App\Http\Controllers\Tenant\PayableController::class, 'store'])->name('tenant.payables.store');
        Route::get('payables/{payable}', [\App\Http\Controllers\Tenant\PayableController::class, 'show'])->name('tenant.payables.show');
        Route::get('payables/{payable}/edit', [\App\Http\Controllers\Tenant\PayableController::class, 'edit'])->name('tenant.payables.edit');
        Route::put('payables/{payable}', [\App\Http\Controllers\Tenant\PayableController::class, 'update'])->name('tenant.payables.update');
        Route::delete('payables/{payable}', [\App\Http\Controllers\Tenant\PayableController::class, 'destroy'])->name('tenant.payables.destroy');
        Route::get('payables/{payable}/quick-pay', [\App\Http\Controllers\Tenant\PayableController::class, 'quickPay'])->name('tenant.payables.quick-pay');
        
        // 應付給付記錄（薪資入帳）
        Route::post('payable-payments/{payable}', [\App\Http\Controllers\Tenant\PayablePaymentController::class, 'store'])->name('tenant.payable-payments.store');
        Route::delete('payable-payments/{payment}', [\App\Http\Controllers\Tenant\PayablePaymentController::class, 'destroy'])->name('tenant.payable-payments.destroy');
        Route::post('payables/{payable}/reset-payments', [\App\Http\Controllers\Tenant\PayableController::class, 'resetPayments'])->name('tenant.payables.reset-payments');

        // 設定管理
        Route::prefix('settings')->name('tenant.settings.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Tenant\SettingsController::class, 'index'])->name('index');
            
            // 公司設定
            Route::get('company', [\App\Http\Controllers\Tenant\SettingsController::class, 'company'])->name('company');
            Route::post('company', [\App\Http\Controllers\Tenant\SettingsController::class, 'updateCompany'])->name('company.update');
            
            // 銀行帳戶管理
            Route::get('bank-accounts', [\App\Http\Controllers\Tenant\BankAccountController::class, 'index'])->name('bank-accounts');
            Route::post('bank-accounts', [\App\Http\Controllers\Tenant\BankAccountController::class, 'store'])->name('bank-accounts.store');
            Route::put('bank-accounts/{bankAccount}', [\App\Http\Controllers\Tenant\BankAccountController::class, 'update'])->name('bank-accounts.update');
            Route::delete('bank-accounts/{bankAccount}', [\App\Http\Controllers\Tenant\BankAccountController::class, 'destroy'])->name('bank-accounts.destroy');
            Route::post('bank-accounts/{bankAccount}/set-default', [\App\Http\Controllers\Tenant\BankAccountController::class, 'setDefault'])->name('bank-accounts.set-default');

            // 財務設定
            Route::get('financial', [\App\Http\Controllers\Tenant\SettingsController::class, 'financial'])->name('financial');
            Route::post('financial', [\App\Http\Controllers\Tenant\SettingsController::class, 'updateFinancial'])->name('financial.update');
            
            // 系統設定
            Route::get('system', [\App\Http\Controllers\Tenant\SettingsController::class, 'system'])->name('system');
            Route::post('system', [\App\Http\Controllers\Tenant\SettingsController::class, 'updateSystem'])->name('system.update');
            Route::post('system/account', [\App\Http\Controllers\Tenant\SettingsController::class, 'updateAccount'])->name('system.account.update');
            Route::delete('system/account', [\App\Http\Controllers\Tenant\SettingsController::class, 'deleteAccount'])->name('system.account.delete');
            Route::post('system/logo', [\App\Http\Controllers\Tenant\SettingsController::class, 'uploadLogo'])->name('system.logo.upload');
        });

        // 標籤管理
        Route::resource('tags', \App\Http\Controllers\Tenant\TagController::class)->names([
            'index' => 'tenant.tags.index',
            'create' => 'tenant.tags.create',
            'store' => 'tenant.tags.store',
            'show' => 'tenant.tags.show',
            'edit' => 'tenant.tags.edit',
            'update' => 'tenant.tags.update',
            'destroy' => 'tenant.tags.destroy',
        ]);
        Route::post('tags/{tag}/set-default-status', [\App\Http\Controllers\Tenant\TagController::class, 'setDefaultStatus'])->name('tenant.tags.set-default-status');
        Route::patch('tags/{tag}/sort', [\App\Http\Controllers\Tenant\TagController::class, 'updateSort'])->name('tenant.tags.sort');

        // 支出項目管理
        Route::resource('expense-categories', \App\Http\Controllers\Tenant\ExpenseCategoryController::class)->names([
            'index' => 'tenant.expense-categories.index',
            'create' => 'tenant.expense-categories.create',
            'store' => 'tenant.expense-categories.store',
            'show' => 'tenant.expense-categories.show',
            'edit' => 'tenant.expense-categories.edit',
            'update' => 'tenant.expense-categories.update',
            'destroy' => 'tenant.expense-categories.destroy',
        ]);

        // 稅款設定管理
        Route::resource('tax-settings', \App\Http\Controllers\Tenant\TaxSettingController::class)->names([
            'index' => 'tenant.tax-settings.index',
            'create' => 'tenant.tax-settings.create',
            'store' => 'tenant.tax-settings.store',
            'show' => 'tenant.tax-settings.show',
            'edit' => 'tenant.tax-settings.edit',
            'update' => 'tenant.tax-settings.update',
            'destroy' => 'tenant.tax-settings.destroy',
        ]);
        Route::post('tax-settings/{taxSetting}/set-default', [\App\Http\Controllers\Tenant\TaxSettingController::class, 'setDefault'])->name('tenant.tax-settings.set-default');

        // 角色權限管理
        Route::resource('roles', \App\Http\Controllers\Tenant\RoleController::class)->names([
            'index' => 'tenant.roles.index',
            'create' => 'tenant.roles.create',
            'store' => 'tenant.roles.store',
            'show' => 'tenant.roles.show',
            'edit' => 'tenant.roles.edit',
            'update' => 'tenant.roles.update',
            'destroy' => 'tenant.roles.destroy',
        ]);

        // 財務報表
        Route::prefix('reports')->name('tenant.reports.')->group(function () {
            Route::get('financial-overview', [\App\Http\Controllers\Tenant\ReportsController::class, 'financialOverview'])->name('financial-overview');
            Route::get('ar-ap-analysis', [\App\Http\Controllers\Tenant\ReportsController::class, 'arApAnalysis'])->name('ar-ap-analysis');
            Route::get('project-profit-loss', [\App\Http\Controllers\Tenant\ReportsController::class, 'projectProfitLoss'])->name('project-profit-loss');
            Route::get('payroll-labor', [\App\Http\Controllers\Tenant\ReportsController::class, 'payrollLabor'])->name('payroll-labor');
        });
        
        // 薪資管理
        Route::prefix('salaries')->name('tenant.salaries.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Tenant\SalaryController::class, 'index'])->name('index');
            Route::get('vendors', [\App\Http\Controllers\Tenant\SalaryController::class, 'vendors'])->name('vendors');
            Route::post('move-to-prev-month', [\App\Http\Controllers\Tenant\SalaryController::class, 'moveToPrevMonth'])->name('move-prev');
            Route::post('move-to-next-month', [\App\Http\Controllers\Tenant\SalaryController::class, 'moveToNextMonth'])->name('move-next');
            Route::get('{user}', [\App\Http\Controllers\Tenant\SalaryController::class, 'show'])->name('show');
            Route::get('{user}/adjustments', [\App\Http\Controllers\Tenant\SalaryController::class, 'adjustments'])->name('adjustments');
            Route::post('{user}/adjustments', [\App\Http\Controllers\Tenant\SalaryController::class, 'storeAdjustment'])->name('adjustments.store');
            Route::post('{user}/quick-adjustment', [\App\Http\Controllers\Tenant\SalaryController::class, 'storeQuickAdjustment'])->name('quick-adjustment.store');
            Route::put('adjustments/{adjustment}', [\App\Http\Controllers\Tenant\SalaryController::class, 'updateAdjustment'])->name('adjustments.update');
            Route::delete('adjustments/{adjustment}', [\App\Http\Controllers\Tenant\SalaryController::class, 'destroyAdjustment'])->name('adjustments.destroy');
            Route::post('{user}/pay', [\App\Http\Controllers\Tenant\SalaryController::class, 'pay'])->name('pay');
            Route::post('{user}/adjustments/{adjustment}/exclude', [\App\Http\Controllers\Tenant\SalaryController::class, 'excludeAdjustment'])->name('exclude-adjustment');
            Route::post('{user}/adjustments/{adjustment}/restore', [\App\Http\Controllers\Tenant\SalaryController::class, 'restoreAdjustment'])->name('restore-adjustment');
        });
    });
});
