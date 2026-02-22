<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the super admin dashboard.
     */
    public function index()
    {
        // 租戶統計
        $stats = [
            'total_tenants'     => Tenant::count(),
            'active_tenants'    => Tenant::where('status', 'active')->count(),
            'suspended_tenants' => Tenant::where('status', 'suspended')->count(),
            'inactive_tenants'  => Tenant::where('status', 'inactive')->count(),
            'expired_tenants'   => Tenant::whereNotNull('plan_ends_at')->where('plan_ends_at', '<', now())->count(),
            'expiring_tenants'  => Tenant::whereNotNull('plan_ends_at')->whereBetween('plan_ends_at', [now(), now()->addDays(7)])->count(),
        ];

        // 方案統計
        $planStats = Tenant::select('plan', DB::raw('count(*) as count'))
            ->groupBy('plan')
            ->get()
            ->pluck('count', 'plan')
            ->toArray();
        
        // 確保所有方案都有鍵值（即使是 0）
        $planStats = array_merge([
            'basic' => 0,
            'professional' => 0,
            'enterprise' => 0,
        ], $planStats);

        // 最近建立的租戶
        $recentTenants = Tenant::orderBy('created_at', 'desc')->take(5)->get();

        // 系統資訊
        $systemInfo = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'database_connection' => config('database.default'),
        ];

        if (request()->wantsJson()) {
            return response()->json([
                'stats' => $stats,
                'plan_stats' => $planStats,
                'recent_tenants' => $recentTenants,
                'system_info' => $systemInfo,
            ]);
        }

        return view('superadmin.dashboard', compact('stats', 'planStats', 'recentTenants', 'systemInfo'));
    }
}
