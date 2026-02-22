<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // 排除 API 路由的 CSRF 驗證
        $middleware->validateCsrfTokens(except: [
            'api/*',
            'deploy',
        ]);
        
        // 註冊自訂的 guest 中介層別名
        $middleware->alias([
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        ]);
        
        // 設定認證重定向路由 - 針對不同守衛
        $middleware->redirectGuestsTo(function ($request) {
            // 如果是 superadmin 路由，重定向到 superadmin.login
            if ($request->is('superadmin') || $request->is('superadmin/*')) {
                return route('superadmin.login');
            }
            // 如果是 tenant 路由，重定向到 tenant.login
            if ($request->is('tenant') || $request->is('tenant/*')) {
                return route('tenant.login');
            }
            // 預設重定向到首頁
            return '/';
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (
            \Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedOnDomainException $e,
            \Illuminate\Http\Request $request
        ) {
            $domain = $request->getHost();
            return response()->view('errors.tenant-not-found', compact('domain'), 404);
        });
    })->create();
