<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TenantSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // 財務設定
            [
                'key' => 'closing_day',
                'value' => '1',
                'type' => 'number',
                'group' => 'financial',
                'label' => '每月關帳日',
                'description' => '每月關帳的日期（1-31）',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'default_currency',
                'value' => 'TWD',
                'type' => 'string',
                'group' => 'financial',
                'label' => '預設交易幣值',
                'description' => '系統預設的交易貨幣代碼',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // 系統設定
            [
                'key' => 'date_format',
                'value' => 'Y-m-d',
                'type' => 'string',
                'group' => 'system',
                'label' => '日期格式',
                'description' => '系統顯示的日期格式',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'time_format',
                'value' => 'H:i',
                'type' => 'string',
                'group' => 'system',
                'label' => '時間格式',
                'description' => '系統顯示的時間格式',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'timezone',
                'value' => 'Asia/Taipei',
                'type' => 'string',
                'group' => 'system',
                'label' => '時區',
                'description' => '系統使用的時區',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('tenant_settings')->updateOrInsert(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
