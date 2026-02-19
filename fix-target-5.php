<?php
// 修改目標5 自動修復腳本

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

tenancy()->initialize('abc123');

echo "===== 開始檢查修改目標5項目 =====\n\n";

// 1. 檢查系統公告
echo "1. 檢查系統公告功能...\n";
$announcement = \App\Models\Announcement::getActive();
if ($announcement) {
    echo "   ✅ 系統公告存在，內容: " . substr($announcement->content, 0, 50) . "...\n";
} else {
    echo "   ⚠️  沒有啟用的系統公告\n";
}

// 2. 檢查應收帳款
echo "\n2. 檢查應收帳款...\n";
$receivables = \App\Models\Receivable::take(5)->get();
echo "   ✅ 應收帳款數量: " . \App\Models\Receivable::count() . "\n";

// 3. 檢查應付帳款
echo "\n3. 檢查應付帳款...\n";
$payables = \App\Models\Payable::take(5)->get();
echo "   ✅ 應付帳款數量: " . \App\Models\Payable::count() . "\n";

// 4. 檢查專案
echo "\n4. 檢查專案...\n";
$projects = \App\Models\Project::take(5)->get();
echo "   ✅ 專案數量: " . \App\Models\Project::count() . "\n";

// 5. 檢查公司
echo "\n5. 檢查公司...\n";
$companies = \App\Models\Company::where('is_active', true)->take(5)->get();
echo "   ✅ 活躍公司數量: " . \App\Models\Company::where('is_active', true)->count() . "\n";

// 6. 檢查角色
echo "\n6. 檢查角色...\n";
$roles = \Spatie\Permission\Models\Role::all();
echo "   ✅ 角色數量: " . $roles->count() . "\n";

// 7. 檢查薪資
echo "\n7. 檢查薪資...\n";
$salaries = \App\Models\Salary::take(5)->get();
echo "   ✅ 薪資數量: " . \App\Models\Salary::count() . "\n";

// 8. 檢查設定
echo "\n8. 檢查系統設定...\n";
$settings = \App\Models\TenantSetting::first();
if ($settings) {
    echo "   ✅ 系統設定存在\n";
    echo "      - 日期格式: " . ($settings->date_format ?? 'Y-m-d') . "\n";
    echo "      - 時區: " . ($settings->timezone ?? 'Asia/Taipei') . "\n";
    echo "      - 幣值: " . ($settings->currency ?? 'TWD') . "\n";
} else {
    echo "   ⚠️  沒有系統設定\n";
}

echo "\n===== 檢查完成 =====\n";
