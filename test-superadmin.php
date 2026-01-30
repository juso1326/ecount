#!/usr/bin/env php
<?php

/**
 * 超級管理員功能測試腳本
 * 測試登入、租戶 CRUD 等功能
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\SuperAdmin;
use App\Models\Tenant;
use Illuminate\Support\Facades\Hash;

echo "\n";
echo "========================================\n";
echo "  ECount 超級管理員功能測試\n";
echo "========================================\n\n";

// 測試 1: 檢查超級管理員
echo "【測試 1】檢查超級管理員帳號\n";
echo "----------------------------------------\n";
$admins = SuperAdmin::all();
echo "✓ SuperAdmin 數量: " . $admins->count() . "\n";
foreach ($admins as $admin) {
    echo "  - {$admin->name} ({$admin->email})\n";
}
echo "\n";

// 測試 2: 檢查租戶列表
echo "【測試 2】檢查租戶列表\n";
echo "----------------------------------------\n";
$tenants = Tenant::all();
echo "✓ 租戶總數: " . $tenants->count() . "\n";
echo "\n租戶詳細資訊:\n";
foreach ($tenants as $tenant) {
    echo "  【{$tenant->id}】\n";
    echo "    名稱: " . ($tenant->name ?: '(未設定)') . "\n";
    echo "    Email: " . ($tenant->email ?: '(未設定)') . "\n";
    echo "    狀態: {$tenant->status}\n";
    echo "    方案: {$tenant->plan}\n";
    
    // 檢查訂閱日期
    if ($tenant->plan_started_at) {
        echo "    方案開始: {$tenant->plan_started_at->format('Y-m-d')}\n";
    }
    if ($tenant->plan_ends_at) {
        echo "    方案結束: {$tenant->plan_ends_at->format('Y-m-d')}\n";
        $daysRemaining = $tenant->planDaysRemaining();
        if ($daysRemaining !== null) {
            echo "    剩餘天數: {$daysRemaining} 天\n";
            if ($tenant->isPlanExpired()) {
                echo "    ⚠️  狀態: 已過期\n";
            } elseif ($tenant->isPlanExpiringSoon()) {
                echo "    ⚠️  狀態: 即將到期\n";
            } else {
                echo "    ✓ 狀態: 正常\n";
            }
        }
    }
    
    // 檢查訂閱歷史
    $subscriptionCount = $tenant->subscriptions()->count();
    if ($subscriptionCount > 0) {
        echo "    訂閱記錄: {$subscriptionCount} 筆\n";
    }
    
    echo "\n";
}

// 測試 3: 統計資訊
echo "【測試 3】統計資訊\n";
echo "----------------------------------------\n";
$stats = [
    'active' => Tenant::where('status', 'active')->count(),
    'suspended' => Tenant::where('status', 'suspended')->count(),
    'inactive' => Tenant::where('status', 'inactive')->count(),
];
echo "✓ 活躍租戶: {$stats['active']}\n";
echo "✓ 暫停租戶: {$stats['suspended']}\n";
echo "✓ 未啟用租戶: {$stats['inactive']}\n";
echo "\n";

// 測試 4: 方案分布
echo "【測試 4】方案分布\n";
echo "----------------------------------------\n";
$planStats = [
    'basic' => Tenant::where('plan', 'basic')->count(),
    'professional' => Tenant::where('plan', 'professional')->count(),
    'enterprise' => Tenant::where('plan', 'enterprise')->count(),
];
echo "✓ 基礎版: {$planStats['basic']}\n";
echo "✓ 專業版: {$planStats['professional']}\n";
echo "✓ 企業版: {$planStats['enterprise']}\n";
echo "\n";

// 測試 5: 檢查密碼
echo "【測試 5】驗證超級管理員密碼\n";
echo "----------------------------------------\n";
$admin = SuperAdmin::first();
if ($admin) {
    $testPassword = 'admin123456';
    $isValid = Hash::check($testPassword, $admin->password);
    if ($isValid) {
        echo "✓ 密碼驗證成功\n";
        echo "  登入資訊:\n";
        echo "    Email: {$admin->email}\n";
        echo "    密碼: {$testPassword}\n";
    } else {
        echo "✗ 密碼驗證失敗\n";
        echo "  提示: 請使用以下指令重設密碼:\n";
        echo "  php artisan tinker\n";
        echo "  \$admin = App\\Models\\SuperAdmin::first();\n";
        echo "  \$admin->password = Hash::make('admin123456');\n";
        echo "  \$admin->save();\n";
    }
}
echo "\n";

// 測試 6: 測試租戶資料庫連線
echo "【測試 6】測試租戶資料庫\n";
echo "----------------------------------------\n";
$testTenant = Tenant::first();
if ($testTenant) {
    echo "測試租戶: {$testTenant->id}\n";
    $dbName = 'tenant_' . $testTenant->id . '_db';
    echo "資料庫名稱: {$dbName}\n";
    
    try {
        // 檢查資料庫是否存在
        $databases = \DB::connection('central')->select('SHOW DATABASES');
        $dbExists = false;
        foreach ($databases as $db) {
            if ($db->Database === $dbName) {
                $dbExists = true;
                break;
            }
        }
        
        if ($dbExists) {
            echo "✓ 資料庫存在\n";
            
            // 檢查資料表
            $tables = \DB::connection('central')->select("SHOW TABLES FROM `{$dbName}`");
            echo "✓ 資料表數量: " . count($tables) . "\n";
        } else {
            echo "⚠️  資料庫不存在（可能尚未建立）\n";
        }
    } catch (\Exception $e) {
        echo "✗ 錯誤: " . $e->getMessage() . "\n";
    }
}
echo "\n";

echo "========================================\n";
echo "  測試完成！\n";
echo "========================================\n";
echo "\n登入網址: https://ecount.test/superadmin/login\n";
echo "登入帳號: {$admin->email}\n";
echo "登入密碼: admin123456\n\n";
