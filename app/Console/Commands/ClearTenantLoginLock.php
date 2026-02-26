<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\DB;

class ClearTenantLoginLock extends Command
{
    protected $signature = 'tenant:clear-login-lock
                            {--tenant= : 租戶 ID（不填則清所有租戶）}
                            {--ip= : 指定 IP（不填則清所有 IP）}';

    protected $description = '清除租戶登入鎖定（RateLimiter）';

    public function handle(): int
    {
        $tenantId = $this->option('tenant');
        $ip       = $this->option('ip');

        $tenants = $tenantId
            ? Tenant::where('id', $tenantId)->get()
            : Tenant::all();

        if ($tenants->isEmpty()) {
            $this->error("找不到租戶：{$tenantId}");
            return 1;
        }

        foreach ($tenants as $tenant) {
            $tenant->run(function () use ($tenant, $ip) {
                if ($ip) {
                    // 清除指定 IP 的鎖定
                    RateLimiter::clear('login_tenant:' . $ip);
                    $this->info("[{$tenant->id}] 已清除 IP {$ip} 的登入鎖定");
                } else {
                    // 清除所有 login_tenant: 開頭的 cache key（含 tenant prefix）
                    $deleted = DB::table('cache')
                        ->where('key', 'like', '%login_tenant:%')
                        ->delete();
                    $this->info("[{$tenant->id}] 已清除 {$deleted} 筆登入鎖定記錄");
                }
            });
        }

        return 0;
    }
}
