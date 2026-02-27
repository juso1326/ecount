<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tag;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            // ── 專案狀態 ──────────────────────────────────────────────
            ['type' => Tag::TYPE_PROJECT_STATUS, 'name' => '新成立',  'color' => '#6366f1', 'sort_order' => 1,  'is_system' => true],
            ['type' => Tag::TYPE_PROJECT_STATUS, 'name' => '規劃中',  'color' => '#f59e0b', 'sort_order' => 2,  'is_system' => false],
            ['type' => Tag::TYPE_PROJECT_STATUS, 'name' => '進行中',  'color' => '#3b82f6', 'sort_order' => 3,  'is_system' => false],
            ['type' => Tag::TYPE_PROJECT_STATUS, 'name' => '已完成',  'color' => '#10b981', 'sort_order' => 4,  'is_system' => false],
            ['type' => Tag::TYPE_PROJECT_STATUS, 'name' => '請款中',  'color' => '#f97316', 'sort_order' => 5,  'is_system' => true],
            ['type' => Tag::TYPE_PROJECT_STATUS, 'name' => '已付款',  'color' => '#22c55e', 'sort_order' => 6,  'is_system' => true],
            ['type' => Tag::TYPE_PROJECT_STATUS, 'name' => '已結案',  'color' => '#6b7280', 'sort_order' => 7,  'is_system' => true],

            // ── 付款方式 ──────────────────────────────────────────────
            ['type' => Tag::TYPE_PAYMENT_METHOD, 'name' => '現金',   'color' => '#10b981', 'sort_order' => 1,  'is_system' => false],
            ['type' => Tag::TYPE_PAYMENT_METHOD, 'name' => '匯款',   'color' => '#3b82f6', 'sort_order' => 2,  'is_system' => false],
            ['type' => Tag::TYPE_PAYMENT_METHOD, 'name' => '支票',   'color' => '#8b5cf6', 'sort_order' => 3,  'is_system' => false],
            ['type' => Tag::TYPE_PAYMENT_METHOD, 'name' => '信用卡', 'color' => '#f59e0b', 'sort_order' => 4,  'is_system' => false],
            ['type' => Tag::TYPE_PAYMENT_METHOD, 'name' => 'ATM',    'color' => '#06b6d4', 'sort_order' => 5,  'is_system' => false],

            // ── 專案職務 ──────────────────────────────────────────────
            ['type' => Tag::TYPE_PROJECT_ROLE, 'name' => '專案經理',   'color' => '#6366f1', 'sort_order' => 1, 'is_system' => false],
            ['type' => Tag::TYPE_PROJECT_ROLE, 'name' => '設計師',     'color' => '#ec4899', 'sort_order' => 2, 'is_system' => false],
            ['type' => Tag::TYPE_PROJECT_ROLE, 'name' => '工程師',     'color' => '#3b82f6', 'sort_order' => 3, 'is_system' => false],
            ['type' => Tag::TYPE_PROJECT_ROLE, 'name' => '業務',       'color' => '#f59e0b', 'sort_order' => 4, 'is_system' => false],
            ['type' => Tag::TYPE_PROJECT_ROLE, 'name' => '行政',       'color' => '#10b981', 'sort_order' => 5, 'is_system' => false],
            ['type' => Tag::TYPE_PROJECT_ROLE, 'name' => '顧問',       'color' => '#8b5cf6', 'sort_order' => 6, 'is_system' => false],

            // ── 專案標籤 ──────────────────────────────────────────────
            ['type' => Tag::TYPE_PROJECT, 'name' => '重點客戶', 'color' => '#ef4444', 'sort_order' => 1, 'is_system' => false],
            ['type' => Tag::TYPE_PROJECT, 'name' => '長期合作', 'color' => '#3b82f6', 'sort_order' => 2, 'is_system' => false],
            ['type' => Tag::TYPE_PROJECT, 'name' => '急件',     'color' => '#f97316', 'sort_order' => 3, 'is_system' => false],
            ['type' => Tag::TYPE_PROJECT, 'name' => '政府標案', 'color' => '#6366f1', 'sort_order' => 4, 'is_system' => false],

            // ── 客戶廠商標籤 ──────────────────────────────────────────
            ['type' => Tag::TYPE_COMPANY, 'name' => 'VIP',      'color' => '#f59e0b', 'sort_order' => 1, 'is_system' => false],
            ['type' => Tag::TYPE_COMPANY, 'name' => '往來中',   'color' => '#10b981', 'sort_order' => 2, 'is_system' => false],
            ['type' => Tag::TYPE_COMPANY, 'name' => '潛在客戶', 'color' => '#06b6d4', 'sort_order' => 3, 'is_system' => false],
            ['type' => Tag::TYPE_COMPANY, 'name' => '黑名單',   'color' => '#ef4444', 'sort_order' => 4, 'is_system' => false],

            // ── 團隊成員標籤 ──────────────────────────────────────────
            ['type' => Tag::TYPE_USER, 'name' => '全職',   'color' => '#10b981', 'sort_order' => 1, 'is_system' => false],
            ['type' => Tag::TYPE_USER, 'name' => '兼職',   'color' => '#f59e0b', 'sort_order' => 2, 'is_system' => false],
            ['type' => Tag::TYPE_USER, 'name' => '外包',   'color' => '#8b5cf6', 'sort_order' => 3, 'is_system' => false],
            ['type' => Tag::TYPE_USER, 'name' => '實習生', 'color' => '#06b6d4', 'sort_order' => 4, 'is_system' => false],
        ];

        foreach ($data as $row) {
            Tag::firstOrCreate(
                ['type' => $row['type'], 'name' => $row['name']],
                array_merge($row, ['is_active' => true])
            );
        }
    }
}
