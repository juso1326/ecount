<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\RolePermissionSeeder;

class SeedTenantRoles extends Command
{
    protected $signature = 'tenant:seed-roles {tenant}';
    protected $description = '為指定租戶建立角色和權限';

    public function handle()
    {
        $tenantId = $this->argument('tenant');
        $tenant = \App\Models\Tenant::find($tenantId);

            $this->error("租戶 {$tenantId} 不存在");
            return 1;
        }

        $this->info("初始化租戶: {$tenant->name} ({$tenantId})");
        tenancy()->initialize($tenant);

        $this->info("執行 RolePermissionSeeder...");
        $seeder = new RolePermissionSeeder();
        $seeder->setCommand($this);
        $seeder->run();

        $this->info("✅ 完成！");
        return 0;
    }
}

