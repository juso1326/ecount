<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
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
                $query = DB::table('cache')->where('key', 'like', 'login_tenant:%');

                if ($ip) {
                    $query->where('key', 'like', "%{$ip}%");
                }

                $deleted = $query->delete();
                $this->info("[{$tenant->id}] 已清除 {$deleted} 筆鎖定記錄" . ($ip ? "（IP: {$ip}）" : ''));
            });
        }

        return 0;
    }
}
