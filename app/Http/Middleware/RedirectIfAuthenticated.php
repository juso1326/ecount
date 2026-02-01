<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // 根據不同的 guard 重定向到不同的位置
                if ($guard === 'superadmin') {
                    return redirect('/superadmin/dashboard');
                }
                
                // 租戶路由：重定向到根路徑（即 tenant.dashboard）
                return redirect('/');
            }
        }

        return $next($request);
    }
}
