<?php

namespace App\Jobs;

use App\Models\Tenant;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateTenantJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $tenantId,
        public string $tenantName,
        public string $domain,
        public string $adminEmail,
        public string $adminPassword,
        public string $plan = 'basic'
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // 1. 建立租戶記錄（會自動觸發 TenantCreated 事件 → CreateDatabase + MigrateDatabase）
        $tenant = Tenant::create([
            'id' => $this->tenantId,
            'name' => $this->tenantName,
            'email' => $this->adminEmail,
            'plan' => $this->plan,
            'status' => 'active',
        ]);

        // 2. 建立子域名
        $tenant->domains()->create([
            'domain' => $this->domain,
        ]);

        // 3. 在租戶資料庫中建立管理員帳號
        $tenant->run(function () {
            DB::table('users')->insert([
                'name' => 'Admin',
                'email' => $this->adminEmail,
                'password' => Hash::make($this->adminPassword),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }
}
