<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name'          => '基礎方案',
                'slug'          => 'basic',
                'description'   => '適合小型團隊，基本財務與專案管理功能。',
                'price'         => 299.00,
                'annual_price'  => 2990.00,
                'max_users'     => 5,
                'max_companies' => 10,
                'max_projects'  => 20,
                'storage_limit' => 5120,   // MB
                'features'      => [
                    '應收應付帳款',
                    '專案管理',
                    '客戶廠商管理',
                    '基本報表',
                ],
                'is_active'   => true,
                'is_featured' => false,
                'sort_order'  => 1,
            ],
            [
                'name'          => '專業方案',
                'slug'          => 'professional',
                'description'   => '適合中型企業，完整財務管理與薪資功能。',
                'price'         => 799.00,
                'annual_price'  => 7990.00,
                'max_users'     => 20,
                'max_companies' => 50,
                'max_projects'  => 100,
                'storage_limit' => 20480,  // MB
                'features'      => [
                    '應收應付帳款',
                    '專案管理',
                    '客戶廠商管理',
                    '薪資管理',
                    '財務報表',
                    '角色權限管理',
                    '資料匯出 (CSV)',
                ],
                'is_active'   => true,
                'is_featured' => true,
                'sort_order'  => 2,
            ],
            [
                'name'          => '企業方案',
                'slug'          => 'enterprise',
                'description'   => '適合大型企業，無限制使用所有功能。',
                'price'         => 1999.00,
                'annual_price'  => 19990.00,
                'max_users'     => 0,   // 0 = 無限制
                'max_companies' => 0,
                'max_projects'  => 0,
                'storage_limit' => 102400, // MB
                'features'      => [
                    '應收應付帳款',
                    '專案管理',
                    '客戶廠商管理',
                    '薪資管理',
                    '完整財務報表',
                    '角色權限管理',
                    '資料匯出 (CSV)',
                    '優先技術支援',
                    '自訂設定',
                ],
                'is_active'   => true,
                'is_featured' => false,
                'sort_order'  => 3,
            ],
        ];

        foreach ($plans as $data) {
            Plan::updateOrCreate(['slug' => $data['slug']], $data);
        }

        $this->command->info('✅ 方案資料建立完成（3 個方案）');
    }
}
