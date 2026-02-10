<?php

/**
 * 完整功能測試資料創建腳本
 * 用於測試標籤系統、稅務功能、Dashboard 顯示等
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\{Project, Receivable, Payable, Company, User, Tag, Department};
use Illuminate\Support\Facades\DB;

echo "🚀 開始創建測試資料...\n\n";

// 設定預設租戶
$tenantId = env('TEST_TENANT_ID', 'tenant_test');
if (empty($tenantId)) {
    die("❌ 請在 .env 中設定 TEST_TENANT_ID\n");
}

$tenant = \App\Models\Tenant::find($tenantId);
if (!$tenant) {
    die("❌ 找不到租戶: {$tenantId}\n");
}

tenancy()->initialize($tenant);
echo "✅ 租戶切換成功: {$tenant->name}\n\n";

// 清理現有測試資料（可選）
$clean = readline("是否清理現有資料？(y/n): ");
if (strtolower($clean) === 'y') {
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    DB::table('taggables')->truncate();
    Tag::truncate();
    Receivable::query()->delete();
    Payable::query()->delete();
    Project::query()->delete();
    Company::query()->delete();
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    echo "✅ 資料清理完成\n\n";
}

// 1. 創建標籤
echo "📌 創建標籤...\n";
$projectTags = [
    ['name' => 'Web開發', 'color' => '#3B82F6', 'type' => 'project'],
    ['name' => 'APP開發', 'color' => '#10B981', 'type' => 'project'],
    ['name' => 'UI/UX設計', 'color' => '#F59E0B', 'type' => 'project'],
    ['name' => '緊急專案', 'color' => '#EF4444', 'type' => 'project'],
];

$receivableTags = [
    ['name' => '已開發票', 'color' => '#10B981', 'type' => 'receivable'],
    ['name' => '逾期', 'color' => '#EF4444', 'type' => 'receivable'],
    ['name' => '分期付款', 'color' => '#F59E0B', 'type' => 'receivable'],
];

$payableTags = [
    ['name' => '外包費用', 'color' => '#8B5CF6', 'type' => 'payable'],
    ['name' => '員工薪資', 'color' => '#3B82F6', 'type' => 'payable'],
    ['name' => '辦公費用', 'color' => '#6B7280', 'type' => 'payable'],
];

foreach (array_merge($projectTags, $receivableTags, $payableTags) as $tagData) {
    Tag::create(array_merge($tagData, ['is_active' => true, 'sort_order' => 0]));
}
echo "✅ 創建 " . count(array_merge($projectTags, $receivableTags, $payableTags)) . " 個標籤\n\n";

// 2. 創建客戶公司
echo "🏢 創建客戶公司...\n";
$companies = [];
for ($i = 1; $i <= 5; $i++) {
    $companies[] = Company::create([
        'code' => 'CMP-' . str_pad($i, 3, '0', STR_PAD_LEFT),
        'name' => '測試客戶' . $i . '號',
        'tax_id' => '1234567' . $i,
        'is_active' => true,
    ]);
}
echo "✅ 創建 " . count($companies) . " 家客戶公司\n\n";

// 3. 創建專案
echo "📁 創建專案...\n";
$fiscalYear = date('Y');
$projects = [];
$projectTagsList = Tag::where('type', 'project')->get();

for ($i = 1; $i <= 10; $i++) {
    $startDate = now()->subDays(rand(1, 180));
    $endDate = (clone $startDate)->addDays(rand(30, 180));
    
    $project = Project::create([
        'code' => 'PRJ-' . date('Y') . str_pad($i, 4, '0', STR_PAD_LEFT),
        'name' => '測試專案 ' . $i,
        'project_type' => ['開發', '設計', '維護', '顧問'][rand(0, 3)],
        'company_id' => $companies[rand(0, count($companies) - 1)]->id,
        'status' => ['planning', 'in_progress', 'completed'][rand(0, 2)],
        'start_date' => $startDate,
        'end_date' => $endDate,
        'budget' => rand(100000, 1000000),
        'quote_no' => 'QT-' . date('Ymd') . str_pad($i, 3, '0', STR_PAD_LEFT),
    ]);
    
    // 附加 1-3 個標籤
    $tags = $projectTagsList->random(rand(1, 3));
    $project->tags()->attach($tags->pluck('id'));
    
    $projects[] = $project;
}
echo "✅ 創建 " . count($projects) . " 個專案（附帶標籤）\n\n";

// 4. 創建應收帳款
echo "💰 創建應收帳款...\n";
$receivableTagsList = Tag::where('type', 'receivable')->get();
$receivables = [];

foreach ($projects as $index => $project) {
    $receiveCount = rand(1, 3);
    for ($j = 1; $j <= $receiveCount; $j++) {
        $amountBeforeTax = rand(50000, 500000);
        $taxRate = 5.00;
        $taxInclusive = rand(0, 1) == 1;
        
        // 計算稅金
        if ($taxInclusive) {
            $amount = $amountBeforeTax;
            $taxAmount = round($amount * $taxRate / (100 + $taxRate), 2);
            $actualBeforeTax = $amount - $taxAmount;
        } else {
            $actualBeforeTax = $amountBeforeTax;
            $taxAmount = round($amountBeforeTax * $taxRate / 100, 2);
            $amount = $actualBeforeTax + $taxAmount;
        }
        
        $receivedPercent = rand(0, 100) / 100;
        $receivedAmount = round($amount * $receivedPercent, 2);
        
        $receivable = Receivable::create([
            'receipt_no' => 'RCV-' . date('Y') . str_pad(($index * 10 + $j), 4, '0', STR_PAD_LEFT),
            'project_id' => $project->id,
            'company_id' => $project->company_id,
            'receipt_date' => $project->start_date->addDays(rand(0, 30)),
            'fiscal_year' => $fiscalYear,
            'content' => '專案付款第 ' . $j . ' 期',
            'quote_no' => $project->quote_no,
            'invoice_no' => 'INV-' . date('Ymd') . rand(1000, 9999),
            'amount' => $amount,
            'amount_before_tax' => $actualBeforeTax,
            'tax_rate' => $taxRate,
            'tax_amount' => $taxAmount,
            'received_amount' => $receivedAmount,
            'status' => $receivedAmount >= $amount ? 'paid' : ($receivedAmount > 0 ? 'partial' : 'unpaid'),
        ]);
        
        // 附加標籤
        $tags = $receivableTagsList->random(rand(1, 2));
        $receivable->tags()->attach($tags->pluck('id'));
        
        $receivables[] = $receivable;
    }
}
echo "✅ 創建 " . count($receivables) . " 筆應收帳款（含稅務計算、標籤）\n\n";

// 5. 創建應付帳款
echo "💸 創建應付帳款...\n";
$payableTagsList = Tag::where('type', 'payable')->get();
$users = User::take(5)->get();
$payables = [];

foreach ($projects as $index => $project) {
    $payCount = rand(1, 4);
    for ($j = 1; $j <= $payCount; $j++) {
        $isEmployee = rand(0, 1) == 1;
        
        $amountBeforeTax = rand(20000, 200000);
        $taxRate = 5.00;
        $taxInclusive = rand(0, 1) == 1;
        
        // 計算稅金
        if ($taxInclusive) {
            $amount = $amountBeforeTax;
            $taxAmount = round($amount * $taxRate / (100 + $taxRate), 2);
            $actualBeforeTax = $amount - $taxAmount;
        } else {
            $actualBeforeTax = $amountBeforeTax;
            $taxAmount = round($amountBeforeTax * $taxRate / 100, 2);
            $amount = $actualBeforeTax + $taxAmount;
        }
        
        $paidPercent = rand(0, 100) / 100;
        $paidAmount = round($amount * $paidPercent, 2);
        
        $payable = Payable::create([
            'payment_no' => 'PAY-' . date('Y') . str_pad(($index * 10 + $j), 4, '0', STR_PAD_LEFT),
            'project_id' => $project->id,
            'payee_type' => $isEmployee ? 'user' : 'company',
            'payee_user_id' => $isEmployee ? $users->random()->id : null,
            'payee_company_id' => $isEmployee ? null : $companies[rand(0, count($companies) - 1)]->id,
            'payment_date' => $project->start_date->addDays(rand(0, 60)),
            'fiscal_year' => $fiscalYear,
            'type' => $isEmployee ? '員工薪資' : ['外包勞務', '設備採購', '辦公費用'][rand(0, 2)],
            'content' => $isEmployee ? '專案成員薪資' : '外包支付第 ' . $j . ' 期',
            'invoice_no' => 'PINV-' . date('Ymd') . rand(1000, 9999),
            'amount' => $amount,
            'amount_before_tax' => $actualBeforeTax,
            'has_tax' => true,
            'tax_rate' => $taxRate,
            'tax_amount' => $taxAmount,
            'tax_inclusive' => $taxInclusive,
            'paid_amount' => $paidAmount,
            'status' => $paidAmount >= $amount ? 'paid' : ($paidAmount > 0 ? 'partial' : 'unpaid'),
        ]);
        
        // 附加標籤
        $tags = $payableTagsList->random(rand(1, 2));
        $payable->tags()->attach($tags->pluck('id'));
        
        $payables[] = $payable;
    }
}
echo "✅ 創建 " . count($payables) . " 筆應付帳款（含稅務計算、標籤）\n\n";

// 6. 統計摘要
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📊 測試資料創建完成！\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "📌 標籤統計：\n";
echo "  - 專案標籤：" . Tag::where('type', 'project')->count() . " 個\n";
echo "  - 應收標籤：" . Tag::where('type', 'receivable')->count() . " 個\n";
echo "  - 應付標籤：" . Tag::where('type', 'payable')->count() . " 個\n\n";

echo "📁 業務資料：\n";
echo "  - 客戶公司：" . count($companies) . " 家\n";
echo "  - 專案：" . count($projects) . " 個\n";
echo "  - 應收帳款：" . count($receivables) . " 筆\n";
echo "  - 應付帳款：" . count($payables) . " 筆\n\n";

$totalReceivable = Receivable::sum('amount');
$totalReceived = Receivable::sum('received_amount');
$totalPayable = Payable::sum('amount');
$totalPaid = Payable::sum('paid_amount');

echo "💰 財務摘要（{$fiscalYear} 年度）：\n";
echo "  - 應收總額：$" . number_format($totalReceivable, 0) . "\n";
echo "  - 已收金額：$" . number_format($totalReceived, 0) . "\n";
echo "  - 應付總額：$" . number_format($totalPayable, 0) . "\n";
echo "  - 已付金額：$" . number_format($totalPaid, 0) . "\n";
echo "  - 淨收入：$" . number_format($totalReceived - $totalPaid, 0) . "\n\n";

echo "✅ 請訪問 Dashboard 查看更新後的顯示效果！\n";
