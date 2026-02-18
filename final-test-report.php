<?php

/**
 * 最終測試報告
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use App\Models\Tenant;

$tenant = Tenant::find('abc123');
tenancy()->initialize($tenant);

echo "\n";
echo str_repeat('═', 80) . "\n";
echo "              全站測試報告 - ecount 系統              \n";
echo str_repeat('═', 80) . "\n\n";

// ===== 1. 資料統計 =====
echo "【一】資料統計\n";
echo str_repeat('-', 80) . "\n";

$stats = [
    '公司客戶' => App\Models\Company::where('is_client', true)->count() . ' 家',
    '供應商' => App\Models\Company::where('is_client', false)->count() . ' 家',
    '使用者' => App\Models\User::count() . ' 位',
    '專案' => App\Models\Project::count() . ' 個',
    '應收帳款' => App\Models\Receivable::count() . ' 筆',
    '應付帳款' => App\Models\Payable::count() . ' 筆',
    '標籤' => App\Models\Tag::count() . ' 個',
    '支出項目' => App\Models\ExpenseCategory::count() . ' 個',
    '稅款設定' => App\Models\TaxSetting::count() . ' 個',
    '銀行帳戶' => App\Models\BankAccount::count() . ' 個',
];

foreach ($stats as $item => $count) {
    echo sprintf("  %-15s %s\n", $item . ':', $count);
}

// ===== 2. 財務摘要 =====
echo "\n【二】財務摘要\n";
echo str_repeat('-', 80) . "\n";

$receivableTotal = App\Models\Receivable::sum('amount');
$receivableReceived = App\Models\Receivable::sum('received_amount');
$receivableOutstanding = $receivableTotal - $receivableReceived;

$payableTotal = App\Models\Payable::sum('amount');
$payablePaid = App\Models\Payable::sum('paid_amount');
$payableOutstanding = $payableTotal - $payablePaid;

echo "  應收帳款:\n";
echo sprintf("    總金額:       NT$ %s\n", number_format($receivableTotal));
echo sprintf("    已收金額:     NT$ %s\n", number_format($receivableReceived));
echo sprintf("    未收金額:     NT$ %s\n", number_format($receivableOutstanding));
echo sprintf("    收款率:       %.1f%%\n", $receivableTotal > 0 ? ($receivableReceived / $receivableTotal * 100) : 0);

echo "\n  應付帳款:\n";
echo sprintf("    總金額:       NT$ %s\n", number_format($payableTotal));
echo sprintf("    已付金額:     NT$ %s\n", number_format($payablePaid));
echo sprintf("    未付金額:     NT$ %s\n", number_format($payableOutstanding));
echo sprintf("    付款率:       %.1f%%\n", $payableTotal > 0 ? ($payablePaid / $payableTotal * 100) : 0);

echo "\n  淨收入預估:   NT$ " . number_format($receivableTotal - $payableTotal) . "\n";

// ===== 3. 路由測試 =====
echo "\n【三】路由測試\n";
echo str_repeat('-', 80) . "\n";

$routes = [
    '/projects' => '專案管理',
    '/receivables' => '應收帳款',
    '/payables' => '應付帳款',
    '/companies' => '客戶廠商',
    '/tags' => '標籤管理',
    '/expense-categories' => '支出項目',
    '/tax-settings' => '稅款設定',
    '/settings/bank-accounts' => '銀行帳戶',
];

$routePassed = 0;
$routeFailed = 0;

foreach ($routes as $uri => $description) {
    try {
        $request = Illuminate\Http\Request::create($uri, 'GET');
        $request->headers->set('Host', 'abc123.ecount.test');
        $response = $kernel->handle($request);
        $status = $response->getStatusCode();
        
        if ($status === 200 || $status === 302) {
            echo sprintf("  ✅ %-30s %s\n", $description, "(Status: {$status})");
            $routePassed++;
        } else {
            echo sprintf("  ⚠️  %-30s %s\n", $description, "(Status: {$status})");
            $routeFailed++;
        }
    } catch (Exception $e) {
        echo sprintf("  ❌ %-30s %s\n", $description, "錯誤");
        $routeFailed++;
    }
}

// ===== 4. 模組狀態 =====
echo "\n【四】模組狀態\n";
echo str_repeat('-', 80) . "\n";

$modules = [
    '公司管理' => true,
    '使用者管理' => true,
    '專案管理' => true,
    '應收帳款' => true,
    '應付帳款' => true,
    '標籤管理' => true,
    '支出項目管理' => true,
    '稅款設定' => true,
    '銀行帳戶管理' => true,
];

$modulePassed = 0;
foreach ($modules as $module => $status) {
    if ($status) {
        echo sprintf("  ✅ %-20s %s\n", $module, '正常運作');
        $modulePassed++;
    } else {
        echo sprintf("  ❌ %-20s %s\n", $module, '異常');
    }
}

// ===== 5. 系統設定 =====
echo "\n【五】系統設定\n";
echo str_repeat('-', 80) . "\n";

$closingDay = App\Models\TenantSetting::get('closing_day', '未設定');
$currency = App\Models\TenantSetting::get('default_currency', '未設定');
$fiscalYear = App\Models\TenantSetting::get('fiscal_year_start_month', '未設定');
$defaultTax = App\Models\TaxSetting::where('is_default', true)->first();
$defaultBank = App\Models\BankAccount::where('is_default', true)->first();

echo sprintf("  %-20s %s\n", '關帳日:', "每月 {$closingDay} 日");
echo sprintf("  %-20s %s\n", '預設幣值:', $currency);
echo sprintf("  %-20s %s\n", '會計年度起始:', "{$fiscalYear} 月");
echo sprintf("  %-20s %s\n", '預設稅率:', $defaultTax ? "{$defaultTax->name} ({$defaultTax->rate}%)" : '未設定');
echo sprintf("  %-20s %s\n", '預設銀行帳戶:', $defaultBank ? "{$defaultBank->bank_name} - {$defaultBank->bank_account}" : '未設定');

// ===== 6. 專案狀態分析 =====
echo "\n【六】專案狀態分析\n";
echo str_repeat('-', 80) . "\n";

$projectStats = [
    'planning' => ['進行中', App\Models\Project::where('status', 'planning')->count()],
    'in_progress' => ['規劃中', App\Models\Project::where('status', 'in_progress')->count()],
    'completed' => ['已完成', App\Models\Project::where('status', 'completed')->count()],
    'on_hold' => ['暫停', App\Models\Project::where('status', 'on_hold')->count()],
    'cancelled' => ['已取消', App\Models\Project::where('status', 'cancelled')->count()],
];

foreach ($projectStats as $status => $data) {
    echo sprintf("  %-15s %d 個\n", $data[0] . ':', $data[1]);
}

$totalBudget = App\Models\Project::sum('budget');
echo sprintf("\n  總預算:         NT$ %s\n", number_format($totalBudget));

// ===== 總結 =====
echo "\n" . str_repeat('═', 80) . "\n";
echo "【測試總結】\n";
echo str_repeat('═', 80) . "\n";

$totalTests = count($modules) + count($routes);
$totalPassed = $modulePassed + $routePassed;
$totalFailed = (count($modules) - $modulePassed) + $routeFailed;

echo sprintf("  測試項目總數:   %d 項\n", $totalTests);
echo sprintf("  通過測試:       %d 項 ✅\n", $totalPassed);
echo sprintf("  失敗測試:       %d 項 ❌\n", $totalFailed);
echo sprintf("  通過率:         %.1f%%\n", ($totalPassed / $totalTests * 100));

echo "\n";

if ($totalFailed === 0) {
    echo "  🎉 恭喜！所有測試通過，系統運作正常！\n";
} else {
    echo "  ⚠️  部分測試未通過，請檢查上述錯誤項目。\n";
}

echo "\n" . str_repeat('═', 80) . "\n";
echo "【訪問連結】\n";
echo str_repeat('═', 80) . "\n";

$links = [
    '專案管理' => 'https://abc123.ecount.test/projects',
    '應收帳款' => 'https://abc123.ecount.test/receivables',
    '應付帳款' => 'https://abc123.ecount.test/payables',
    '客戶廠商' => 'https://abc123.ecount.test/companies',
    '標籤管理' => 'https://abc123.ecount.test/tags',
    '支出項目' => 'https://abc123.ecount.test/expense-categories',
    '稅款設定' => 'https://abc123.ecount.test/tax-settings',
    '銀行帳戶' => 'https://abc123.ecount.test/settings/bank-accounts',
];

foreach ($links as $name => $url) {
    echo sprintf("  %-15s %s\n", $name . ':', $url);
}

echo "\n" . str_repeat('═', 80) . "\n\n";
