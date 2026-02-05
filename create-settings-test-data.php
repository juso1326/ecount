<?php

/**
 * 建立進階設定測試資料
 * - 支出項目管理
 * - 稅款設定
 * - 標籤管理
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Tenant;
use App\Models\Tag;
use App\Models\ExpenseCategory;
use App\Models\TaxSetting;
use App\Models\TenantSetting;

// 指定租戶
$tenantId = $argv[1] ?? 'abc123';

$tenant = Tenant::find($tenantId);

if (!$tenant) {
    echo "❌ 找不到租戶: {$tenantId}\n";
    exit(1);
}

echo "🏢 切換到租戶: {$tenant->id}\n";
tenancy()->initialize($tenant);

echo "\n" . str_repeat('=', 50) . "\n";
echo "建立進階設定測試資料\n";
echo str_repeat('=', 50) . "\n\n";

// ===== 1. 標籤管理 =====
echo "📌 建立標籤資料...\n";

// 專案標籤
$projectTags = [
    ['name' => '重要專案', 'color' => '#EF4444', 'description' => '高優先級專案'],
    ['name' => '長期專案', 'color' => '#3B82F6', 'description' => '持續時間超過6個月'],
    ['name' => '緊急專案', 'color' => '#F59E0B', 'description' => '需要立即處理'],
    ['name' => '研發專案', 'color' => '#8B5CF6', 'description' => '技術研發類專案'],
    ['name' => '維護專案', 'color' => '#10B981', 'description' => '系統維護專案'],
];

foreach ($projectTags as $tagData) {
    Tag::firstOrCreate(
        ['name' => $tagData['name'], 'type' => 'project'],
        ['color' => $tagData['color'], 'description' => $tagData['description']]
    );
}
echo "✅ 已建立 " . count($projectTags) . " 個專案標籤\n";

// 客戶廠商標籤
$companyTags = [
    ['name' => 'VIP客戶', 'color' => '#DC2626', 'description' => '重要客戶'],
    ['name' => '長期合作', 'color' => '#2563EB', 'description' => '長期合作夥伴'],
    ['name' => '新客戶', 'color' => '#16A34A', 'description' => '新開發客戶'],
    ['name' => '海外客戶', 'color' => '#9333EA', 'description' => '國外客戶'],
    ['name' => '政府單位', 'color' => '#0891B2', 'description' => '政府機關'],
];

foreach ($companyTags as $tagData) {
    Tag::firstOrCreate(
        ['name' => $tagData['name'], 'type' => 'company'],
        ['color' => $tagData['color'], 'description' => $tagData['description']]
    );
}
echo "✅ 已建立 " . count($companyTags) . " 個客戶廠商標籤\n";

// 團隊成員標籤
$userTags = [
    ['name' => '開發團隊', 'color' => '#6366F1', 'description' => '開發人員'],
    ['name' => '設計團隊', 'color' => '#EC4899', 'description' => '設計人員'],
    ['name' => '業務團隊', 'color' => '#F59E0B', 'description' => '業務人員'],
    ['name' => '管理階層', 'color' => '#7C3AED', 'description' => '管理人員'],
    ['name' => '外部顧問', 'color' => '#14B8A6', 'description' => '外部專家'],
];

foreach ($userTags as $tagData) {
    Tag::firstOrCreate(
        ['name' => $tagData['name'], 'type' => 'user'],
        ['color' => $tagData['color'], 'description' => $tagData['description']]
    );
}
echo "✅ 已建立 " . count($userTags) . " 個團隊成員標籤\n\n";

// ===== 2. 支出項目管理 =====
echo "💰 建立支出項目資料...\n";

$expenseCategories = [
    // 人力成本（父類別）
    ['name' => '人力成本', 'code' => 'HR', 'parent_id' => null, 'description' => '人員相關支出', 'is_active' => true],
    
    // 外包費用（父類別）
    ['name' => '外包費用', 'code' => 'OS', 'parent_id' => null, 'description' => '外部廠商支出', 'is_active' => true],
    
    // 設備費用（父類別）
    ['name' => '設備費用', 'code' => 'EQ', 'parent_id' => null, 'description' => '硬體設備支出', 'is_active' => true],
    
    // 營運費用（父類別）
    ['name' => '營運費用', 'code' => 'OP', 'parent_id' => null, 'description' => '日常營運支出', 'is_active' => true],
];

$parentCategories = [];
foreach ($expenseCategories as $categoryData) {
    $category = ExpenseCategory::firstOrCreate(
        ['code' => $categoryData['code']],
        $categoryData
    );
    $parentCategories[$categoryData['code']] = $category;
}
echo "✅ 已建立 " . count($expenseCategories) . " 個支出項目父類別\n";

// 子類別
$subCategories = [
    // 人力成本子類別
    ['name' => '正職薪資', 'code' => 'HR-01', 'parent_code' => 'HR', 'description' => '正職員工薪資'],
    ['name' => '兼職薪資', 'code' => 'HR-02', 'parent_code' => 'HR', 'description' => '兼職人員薪資'],
    ['name' => '加班費', 'code' => 'HR-03', 'parent_code' => 'HR', 'description' => '員工加班費用'],
    ['name' => '獎金', 'code' => 'HR-04', 'parent_code' => 'HR', 'description' => '績效獎金'],
    
    // 外包費用子類別
    ['name' => '程式開發', 'code' => 'OS-01', 'parent_code' => 'OS', 'description' => '外包程式開發'],
    ['name' => '設計外包', 'code' => 'OS-02', 'parent_code' => 'OS', 'description' => '外包設計服務'],
    ['name' => '顧問費', 'code' => 'OS-03', 'parent_code' => 'OS', 'description' => '外部顧問費用'],
    ['name' => '翻譯費', 'code' => 'OS-04', 'parent_code' => 'OS', 'description' => '翻譯服務費用'],
    
    // 設備費用子類別
    ['name' => '電腦設備', 'code' => 'EQ-01', 'parent_code' => 'EQ', 'description' => '電腦主機、筆電等'],
    ['name' => '軟體授權', 'code' => 'EQ-02', 'parent_code' => 'EQ', 'description' => '軟體授權費用'],
    ['name' => '伺服器', 'code' => 'EQ-03', 'parent_code' => 'EQ', 'description' => '伺服器設備'],
    ['name' => '網路設備', 'code' => 'EQ-04', 'parent_code' => 'EQ', 'description' => '路由器、交換器等'],
    
    // 營運費用子類別
    ['name' => '租金', 'code' => 'OP-01', 'parent_code' => 'OP', 'description' => '辦公室租金'],
    ['name' => '水電費', 'code' => 'OP-02', 'parent_code' => 'OP', 'description' => '水電瓦斯費用'],
    ['name' => '網路費', 'code' => 'OP-03', 'parent_code' => 'OP', 'description' => '網路通訊費用'],
    ['name' => '文具雜項', 'code' => 'OP-04', 'parent_code' => 'OP', 'description' => '辦公文具用品'],
];

foreach ($subCategories as $subData) {
    $parentId = $parentCategories[$subData['parent_code']]->id ?? null;
    ExpenseCategory::firstOrCreate(
        ['code' => $subData['code']],
        [
            'name' => $subData['name'],
            'parent_id' => $parentId,
            'description' => $subData['description'],
            'is_active' => true
        ]
    );
}
echo "✅ 已建立 " . count($subCategories) . " 個支出項目子類別\n\n";

// ===== 3. 稅款設定 =====
echo "📊 建立稅款設定資料...\n";

$taxSettings = [
    ['name' => '營業稅 5%', 'rate' => 5.00, 'description' => '一般營業稅稅率', 'is_default' => true],
    ['name' => '免稅', 'rate' => 0.00, 'description' => '免徵營業稅', 'is_default' => false],
    ['name' => '零稅率', 'rate' => 0.00, 'description' => '零稅率（出口銷售）', 'is_default' => false],
    ['name' => '代收稅款 10%', 'rate' => 10.00, 'description' => '所得稅扣繳', 'is_default' => false],
];

foreach ($taxSettings as $taxData) {
    $tax = TaxSetting::firstOrCreate(
        ['name' => $taxData['name']],
        $taxData
    );
    
    if ($taxData['is_default']) {
        $tax->setAsDefault();
    }
}
echo "✅ 已建立 " . count($taxSettings) . " 個稅款設定\n\n";

// ===== 4. 財務設定 =====
echo "💼 建立財務設定資料...\n";

TenantSetting::set('closing_day', 25);
TenantSetting::set('default_currency', 'TWD');

echo "✅ 已設定關帳日為每月 25 日\n";
echo "✅ 已設定預設幣值為 TWD\n\n";

// ===== 統計資訊 =====
echo "\n" . str_repeat('=', 50) . "\n";
echo "✨ 測試資料建立完成！\n";
echo str_repeat('=', 50) . "\n\n";

echo "📊 資料統計：\n";
echo "   - 專案標籤：" . Tag::where('type', 'project')->count() . " 個\n";
echo "   - 客戶標籤：" . Tag::where('type', 'company')->count() . " 個\n";
echo "   - 成員標籤：" . Tag::where('type', 'user')->count() . " 個\n";
echo "   - 支出項目：" . ExpenseCategory::count() . " 個 (" . ExpenseCategory::whereNull('parent_id')->count() . " 個父類別)\n";
echo "   - 稅款設定：" . TaxSetting::count() . " 個\n\n";

echo "🎉 現在可以開始測試這些功能了！\n\n";

echo "📍 功能頁面：\n";
echo "   - 標籤管理：https://{$tenantId}.ecount.test/tags\n";
echo "   - 支出項目：https://{$tenantId}.ecount.test/expense-categories\n";
echo "   - 稅款設定：https://{$tenantId}.ecount.test/tax-settings\n";
echo "   - 財務設定：https://{$tenantId}.ecount.test/settings/financial\n\n";
