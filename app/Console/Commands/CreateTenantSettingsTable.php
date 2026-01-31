<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Stancl\Tenancy\Facades\Tenancy;

class CreateTenantSettingsTable extends Command
{
    protected $signature = 'tenant:create-settings-table {tenant}';
    protected $description = '為指定租戶創建 tenant_settings 表';

    public function handle()
    {
        $tenantId = $this->argument('tenant');
        
        $tenant = \App\Models\Tenant::find($tenantId);
        
        if (!$tenant) {
            $this->error("租戶 {$tenantId} 不存在");
            return 1;
        }

        tenancy()->initialize($tenant);

        if (Schema::hasTable('tenant_settings')) {
            $this->info('tenant_settings 表已存在');
            return 0;
        }

        // 創建表
        Schema::create('tenant_settings', function ($table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string');
            $table->string('group')->default('general');
            $table->string('label');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 插入預設設定
        DB::table('tenant_settings')->insert([
            [
                'key' => 'company_code_prefix',
                'value' => 'C',
                'type' => 'string',
                'group' => 'company',
                'label' => '公司代碼前綴',
                'description' => '新增公司時自動產生的代碼前綴',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'company_code_length',
                'value' => '4',
                'type' => 'number',
                'group' => 'company',
                'label' => '公司代碼長度',
                'description' => '公司代碼數字部分的長度',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'company_code_auto',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'company',
                'label' => '自動產生公司代碼',
                'description' => '是否自動產生公司代碼（關閉則需手動輸入）',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->info('tenant_settings 表創建成功');
        
        return 0;
    }
}
