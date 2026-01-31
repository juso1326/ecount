<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class MigrateTenantCore extends Command
{
    protected $signature = 'tenant:migrate-core {tenant}';
    protected $description = '為指定租戶執行核心表 migration';

    public function handle()
    {
        $tenantId = $this->argument('tenant');
        
        $tenant = \App\Models\Tenant::find($tenantId);
        
        if (!$tenant) {
            $this->error("租戶 {$tenantId} 不存在");
            return 1;
        }

        tenancy()->initialize($tenant);

        $this->info("正在為租戶 {$tenantId} 執行核心表 migration...");
        
        // 執行特定的 migration 檔案
        $migrations = [
            'database/migrations/tenant/2026_01_29_142903_create_tenant_core_tables.php',
            'database/migrations/tenant/2026_01_29_152218_add_last_login_to_users_table.php',
            'database/migrations/tenant/2026_01_30_173314_add_additional_fields_to_companies_table.php',
            'database/migrations/tenant/2026_01_31_123801_complete_projects_table_fields.php',
            'database/migrations/tenant/2026_01_31_123802_complete_receivables_table_fields.php',
            'database/migrations/tenant/2026_01_31_123803_complete_payables_table_fields.php',
            'database/migrations/tenant/2026_01_31_124000_create_announcements_table.php',
            'database/migrations/tenant/2026_01_31_140001_add_supervisor_to_users_table.php',
            'database/migrations/tenant/2026_01_31_140500_add_is_client_to_companies_table.php',
            'database/migrations/tenant/2026_01_31_161254_create_project_members_table.php',
        ];
        
        foreach ($migrations as $migration) {
            if (file_exists(base_path($migration))) {
                $this->info("執行: " . basename($migration));
                try {
                    Artisan::call('migrate', [
                        '--path' => str_replace(base_path() . '/', '', base_path($migration)),
                        '--force' => true,
                    ]);
                    $this->info("✓ 完成");
                } catch (\Exception $e) {
                    $this->warn("⚠ 跳過（可能已存在）: " . $e->getMessage());
                }
            }
        }
        
        $this->info("Migration 執行完成！");
        
        return 0;
    }
}
