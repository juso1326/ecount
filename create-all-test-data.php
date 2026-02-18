<?php

/**
 * 全站測試資料建立腳本
 * 使用方法: php create-all-test-data.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Tenant;
use App\Models\Company;
use App\Models\User;
use App\Models\Project;
use App\Models\Receivable;
use App\Models\Payable;
use App\Models\Tag;
use App\Models\ExpenseCategory;
use App\Models\TaxSetting;
use App\Models\TenantSetting;
use App\Models\BankAccount;

// 切換到測試租戶
$tenant = Tenant::find('abc123');
if (!$tenant) {
    echo "❌ 租戶 abc123 不存在\n";
    exit(1);
}

tenancy()->initialize($tenant);
echo "✅ 已切換到租戶: {$tenant->id}\n\n";

echo str_repeat('=', 60) . "\n";
echo "開始建立全站測試資料\n";
echo str_repeat('=', 60) . "\n\n";

// ===== 1. 建立公司資料 =====
echo "📦 建立公司資料...\n";

$companiesData = [
    [
        'code' => 'C001',
        'name' => '台灣科技股份有限公司',
        'tax_id' => '12345678',
        'contact_person' => '張經理',
        'phone' => '02-2345-6789',
        'email' => 'contact@taiwantech.com',
        'address' => '台北市信義區信義路五段7號',
        'is_active' => true,
        'is_client' => true,
    ],
    [
        'code' => 'C002',
        'name' => '創新設計工作室',
        'tax_id' => '23456789',
        'contact_person' => '李設計師',
        'phone' => '02-8765-4321',
        'email' => 'hello@creative-design.com',
        'address' => '台北市大安區復興南路一段390號',
        'is_active' => true,
        'is_client' => true,
    ],
    [
        'code' => 'C003',
        'name' => '全球行銷股份有限公司',
        'tax_id' => '34567890',
        'contact_person' => '王總監',
        'phone' => '03-1234-5678',
        'email' => 'info@global-marketing.com',
        'address' => '桃園市中壢區中山路123號',
        'is_active' => true,
        'is_client' => true,
    ],
    [
        'code' => 'V001',
        'name' => '優質印刷廠',
        'tax_id' => '45678901',
        'contact_person' => '陳老闆',
        'phone' => '04-2222-3333',
        'email' => 'print@quality.com',
        'address' => '台中市西區民權路456號',
        'is_active' => true,
        'is_client' => false,
    ],
    [
        'code' => 'V002',
        'name' => '雲端服務供應商',
        'tax_id' => '56789012',
        'contact_person' => '林經理',
        'phone' => '02-5555-6666',
        'email' => 'service@cloud.com',
        'address' => '台北市內湖區瑞光路100號',
        'is_active' => true,
        'is_client' => false,
    ],
];

$companies = [];
foreach ($companiesData as $data) {
    $company = Company::firstOrCreate(
        ['code' => $data['code']],
        $data
    );
    $companies[$data['code']] = $company;
    echo "  ✓ {$company->code} - {$company->name}\n";
}
echo "  總計: " . count($companies) . " 家公司\n\n";

// ===== 2. 建立使用者 =====
echo "👥 建立使用者資料...\n";

$usersData = [
    ['name' => '王大明', 'email' => 'wang@test.com', 'employee_id' => 'E001'],
    ['name' => '李小華', 'email' => 'lee@test.com', 'employee_id' => 'E002'],
    ['name' => '陳美玲', 'email' => 'chen@test.com', 'employee_id' => 'E003'],
    ['name' => '林志明', 'email' => 'lin@test.com', 'employee_id' => 'E004'],
    ['name' => '張雅婷', 'email' => 'chang@test.com', 'employee_id' => 'E005'],
];

$users = [];
foreach ($usersData as $data) {
    $user = User::firstOrCreate(
        ['email' => $data['email']],
        array_merge($data, [
            'password' => bcrypt('password'),
            'is_active' => true,
        ])
    );
    $users[] = $user;
    echo "  ✓ {$user->name} ({$user->email})\n";
}
echo "  總計: " . count($users) . " 位使用者\n\n";

// ===== 3. 建立專案 =====
echo "🎯 建立專案資料...\n";

$projectsData = [
    [
        'code' => 'PRJ-2024-001',
        'name' => '企業形象網站設計',
        'project_type' => '網站開發',
        'company_id' => $companies['C001']->id,
        'manager_id' => $users[0]->id,
        'status' => 'in_progress',
        'start_date' => '2024-01-15',
        'end_date' => '2024-03-31',
        'budget' => 500000,
        'description' => '為客戶設計全新企業形象網站，包含響應式設計與後台管理系統',
    ],
    [
        'code' => 'PRJ-2024-002',
        'name' => '品牌識別系統重建',
        'project_type' => '品牌設計',
        'company_id' => $companies['C002']->id,
        'manager_id' => $users[1]->id,
        'status' => 'in_progress',
        'start_date' => '2024-02-01',
        'end_date' => '2024-05-31',
        'budget' => 800000,
        'description' => 'Logo重新設計、視覺識別手冊、應用設計等',
    ],
    [
        'code' => 'PRJ-2025-001',
        'name' => '行銷活動網站建置',
        'project_type' => '活動網站',
        'company_id' => $companies['C003']->id,
        'manager_id' => $users[2]->id,
        'status' => 'planning',
        'start_date' => '2025-01-10',
        'end_date' => '2025-02-28',
        'budget' => 300000,
        'description' => '活動網站設計與開發，含會員系統',
    ],
    [
        'code' => 'PRJ-2025-002',
        'name' => 'ERP系統開發',
        'project_type' => '系統開發',
        'company_id' => $companies['C001']->id,
        'manager_id' => $users[0]->id,
        'status' => 'in_progress',
        'start_date' => '2025-01-01',
        'end_date' => '2025-12-31',
        'budget' => 5000000,
        'description' => '企業資源規劃系統開發',
    ],
];

$projects = [];
foreach ($projectsData as $data) {
    $project = Project::updateOrCreate(
        ['code' => $data['code']],
        $data
    );
    $projects[] = $project;
    echo "  ✓ {$project->code} - {$project->name}\n";
}
echo "  總計: " . count($projects) . " 個專案\n\n";

// ===== 4. 建立應收帳款 =====
echo "💰 建立應收帳款資料...\n";

$receivablesData = [
    [
        'receipt_no' => 'RCV-2024-001',
        'invoice_no' => 'INV-2024-001',
        'company_id' => $companies['C001']->id,
        'project_id' => $projects[0]->id,
        'responsible_user_id' => $users[0]->id,
        'receipt_date' => '2024-02-01',
        'due_date' => '2024-03-01',
        'amount' => 250000,
        'received_amount' => 250000,
        'status' => 'paid',
        'content' => '企業形象網站設計 - 第一期款（50%）',
    ],
    [
        'receipt_no' => 'RCV-2024-002',
        'invoice_no' => 'INV-2024-002',
        'company_id' => $companies['C001']->id,
        'project_id' => $projects[0]->id,
        'responsible_user_id' => $users[0]->id,
        'receipt_date' => '2024-03-15',
        'due_date' => '2024-04-15',
        'amount' => 250000,
        'received_amount' => 0,
        'status' => 'unpaid',
        'content' => '企業形象網站設計 - 第二期款（50%）',
    ],
    [
        'receipt_no' => 'RCV-2024-003',
        'invoice_no' => 'INV-2024-003',
        'company_id' => $companies['C002']->id,
        'project_id' => $projects[1]->id,
        'responsible_user_id' => $users[1]->id,
        'receipt_date' => '2024-02-15',
        'due_date' => '2024-03-15',
        'amount' => 400000,
        'received_amount' => 400000,
        'status' => 'paid',
        'content' => '品牌識別系統重建 - 第一期款（50%）',
    ],
    [
        'receipt_no' => 'RCV-2024-004',
        'invoice_no' => 'INV-2024-004',
        'company_id' => $companies['C002']->id,
        'project_id' => $projects[1]->id,
        'responsible_user_id' => $users[1]->id,
        'receipt_date' => '2024-04-01',
        'due_date' => '2024-05-01',
        'amount' => 400000,
        'received_amount' => 200000,
        'status' => 'partial',
        'content' => '品牌識別系統重建 - 第二期款（50%）',
    ],
    [
        'receipt_no' => 'RCV-2025-001',
        'invoice_no' => 'INV-2025-001',
        'company_id' => $companies['C003']->id,
        'project_id' => $projects[2]->id,
        'responsible_user_id' => $users[2]->id,
        'receipt_date' => '2025-01-20',
        'due_date' => '2025-02-20',
        'amount' => 150000,
        'received_amount' => 0,
        'status' => 'unpaid',
        'content' => '行銷活動網站建置 - 第一期款（50%）',
    ],
    [
        'receipt_no' => 'RCV-2025-002',
        'invoice_no' => 'INV-2025-002',
        'company_id' => $companies['C001']->id,
        'project_id' => $projects[3]->id,
        'responsible_user_id' => $users[0]->id,
        'receipt_date' => '2025-02-01',
        'due_date' => '2025-02-28',
        'amount' => 1500000,
        'received_amount' => 1500000,
        'status' => 'paid',
        'content' => 'ERP系統開發 - 第一期款（30%）',
    ],
];

$receivables = [];
foreach ($receivablesData as $data) {
    $receivable = Receivable::updateOrCreate(
        ['receipt_no' => $data['receipt_no']],
        $data
    );
    $receivables[] = $receivable;
    echo "  ✓ {$receivable->receipt_no} - NT$ " . number_format($receivable->amount) . " ({$receivable->status})\n";
}
echo "  總計: " . count($receivables) . " 筆應收帳款\n\n";

// ===== 5. 建立應付帳款 =====
echo "💸 建立應付帳款資料...\n";

$payablesData = [
    [
        'payment_no' => 'PAY-2024-001',
        'invoice_no' => 'BILL-2024-001',
        'company_id' => $companies['V001']->id,
        'project_id' => $projects[0]->id,
        'responsible_user_id' => $users[0]->id,
        'payment_date' => '2024-02-10',
        'due_date' => '2024-03-10',
        'amount' => 50000,
        'paid_amount' => 50000,
        'status' => 'paid',
        'content' => '印刷品製作費用',
        'type' => '外包費用',
    ],
    [
        'payment_no' => 'PAY-2024-002',
        'invoice_no' => 'BILL-2024-002',
        'company_id' => $companies['V001']->id,
        'project_id' => $projects[1]->id,
        'responsible_user_id' => $users[1]->id,
        'payment_date' => '2024-03-01',
        'due_date' => '2024-04-01',
        'amount' => 80000,
        'paid_amount' => 40000,
        'status' => 'partial',
        'content' => '名片、信紙等印刷品',
        'type' => '外包費用',
    ],
    [
        'payment_no' => 'PAY-2024-003',
        'invoice_no' => 'BILL-2024-003',
        'company_id' => $companies['V001']->id,
        'project_id' => $projects[0]->id,
        'responsible_user_id' => $users[0]->id,
        'payment_date' => '2024-03-20',
        'due_date' => '2024-04-20',
        'amount' => 35000,
        'paid_amount' => 0,
        'status' => 'unpaid',
        'content' => '宣傳海報印刷',
        'type' => '外包費用',
    ],
    [
        'payment_no' => 'PAY-2025-001',
        'invoice_no' => 'BILL-2025-001',
        'company_id' => $companies['V002']->id,
        'project_id' => $projects[3]->id,
        'responsible_user_id' => $users[0]->id,
        'payment_date' => '2025-01-15',
        'due_date' => '2025-02-15',
        'amount' => 120000,
        'paid_amount' => 120000,
        'status' => 'paid',
        'content' => '雲端伺服器租用費用',
        'type' => '設備費用',
    ],
    [
        'payment_no' => 'PAY-2025-002',
        'invoice_no' => 'BILL-2025-002',
        'company_id' => $companies['V002']->id,
        'project_id' => $projects[3]->id,
        'responsible_user_id' => $users[0]->id,
        'payment_date' => '2025-02-10',
        'due_date' => '2025-03-10',
        'amount' => 150000,
        'paid_amount' => 0,
        'status' => 'unpaid',
        'content' => 'SSL憑證與網域費用',
        'type' => '設備費用',
    ],
];

$payables = [];
foreach ($payablesData as $data) {
    $payable = Payable::updateOrCreate(
        ['payment_no' => $data['payment_no']],
        $data
    );
    $payables[] = $payable;
    echo "  ✓ {$payable->payment_no} - NT$ " . number_format($payable->amount) . " ({$payable->status})\n";
}
echo "  總計: " . count($payables) . " 筆應付帳款\n\n";

// ===== 6. 建立標籤 =====
echo "📌 建立標籤資料...\n";

$tagsData = [
    // 專案標籤
    ['name' => '重要專案', 'type' => 'project', 'color' => '#EF4444', 'description' => '高優先級專案'],
    ['name' => '緊急專案', 'type' => 'project', 'color' => '#F59E0B', 'description' => '需要立即處理'],
    ['name' => '研發專案', 'type' => 'project', 'color' => '#8B5CF6', 'description' => '技術研發類專案'],
    // 客戶標籤
    ['name' => 'VIP客戶', 'type' => 'company', 'color' => '#DC2626', 'description' => '重要客戶'],
    ['name' => '長期合作', 'type' => 'company', 'color' => '#2563EB', 'description' => '長期合作夥伴'],
    ['name' => '新客戶', 'type' => 'company', 'color' => '#16A34A', 'description' => '新開發客戶'],
    // 付款方式
    ['name' => '轉帳匯款', 'type' => 'payment_method', 'color' => '#3B82F6', 'description' => '銀行轉帳'],
    ['name' => '現金', 'type' => 'payment_method', 'color' => '#10B981', 'description' => '現金支付'],
    ['name' => '支票', 'type' => 'payment_method', 'color' => '#F59E0B', 'description' => '支票付款'],
];

$tags = [];
foreach ($tagsData as $data) {
    $tag = Tag::firstOrCreate(
        ['name' => $data['name'], 'type' => $data['type']],
        $data
    );
    $tags[] = $tag;
    echo "  ✓ {$tag->name} ({$tag->type})\n";
}
echo "  總計: " . count($tags) . " 個標籤\n\n";

// ===== 7. 建立支出項目 =====
echo "💰 建立支出項目資料...\n";

$expenseCategoriesData = [
    ['name' => '人力成本', 'code' => 'HR', 'parent_id' => null, 'description' => '人員相關支出'],
    ['name' => '外包費用', 'code' => 'OS', 'parent_id' => null, 'description' => '外部廠商支出'],
    ['name' => '設備費用', 'code' => 'EQ', 'parent_id' => null, 'description' => '硬體設備支出'],
    ['name' => '營運費用', 'code' => 'OP', 'parent_id' => null, 'description' => '日常營運支出'],
];

$parentCategories = [];
foreach ($expenseCategoriesData as $data) {
    $category = ExpenseCategory::firstOrCreate(
        ['code' => $data['code']],
        array_merge($data, ['is_active' => true])
    );
    $parentCategories[$data['code']] = $category;
    echo "  ✓ {$category->code} - {$category->name}\n";
}

$subCategoriesData = [
    ['name' => '正職薪資', 'code' => 'HR-01', 'parent_code' => 'HR', 'description' => '正職員工薪資'],
    ['name' => '加班費', 'code' => 'HR-02', 'parent_code' => 'HR', 'description' => '員工加班費用'],
    ['name' => '程式開發', 'code' => 'OS-01', 'parent_code' => 'OS', 'description' => '外包程式開發'],
    ['name' => '設計外包', 'code' => 'OS-02', 'parent_code' => 'OS', 'description' => '外包設計服務'],
    ['name' => '電腦設備', 'code' => 'EQ-01', 'parent_code' => 'EQ', 'description' => '電腦主機、筆電等'],
    ['name' => '軟體授權', 'code' => 'EQ-02', 'parent_code' => 'EQ', 'description' => '軟體授權費用'],
    ['name' => '租金', 'code' => 'OP-01', 'parent_code' => 'OP', 'description' => '辦公室租金'],
    ['name' => '水電費', 'code' => 'OP-02', 'parent_code' => 'OP', 'description' => '水電瓦斯費用'],
];

foreach ($subCategoriesData as $data) {
    $parentId = $parentCategories[$data['parent_code']]->id ?? null;
    ExpenseCategory::firstOrCreate(
        ['code' => $data['code']],
        [
            'name' => $data['name'],
            'parent_id' => $parentId,
            'description' => $data['description'],
            'is_active' => true
        ]
    );
}
echo "  總計: " . ExpenseCategory::count() . " 個支出項目\n\n";

// ===== 8. 建立稅款設定 =====
echo "📊 建立稅款設定資料...\n";

$taxSettingsData = [
    ['name' => '營業稅 5%', 'rate' => 5.00, 'description' => '一般營業稅稅率', 'is_default' => true, 'is_active' => true],
    ['name' => '免稅', 'rate' => 0.00, 'description' => '免徵營業稅', 'is_default' => false, 'is_active' => true],
    ['name' => '零稅率', 'rate' => 0.00, 'description' => '零稅率（出口銷售）', 'is_default' => false, 'is_active' => true],
];

foreach ($taxSettingsData as $data) {
    $tax = TaxSetting::firstOrCreate(
        ['name' => $data['name']],
        $data
    );
    echo "  ✓ {$tax->name} - {$tax->rate}%\n";
}
echo "  總計: " . TaxSetting::count() . " 個稅款設定\n\n";

// ===== 9. 建立銀行帳戶 =====
echo "🏦 建立銀行帳戶資料...\n";

$bankAccountsData = [
    [
        'account_name' => '台灣銀行營業帳戶',
        'bank_name' => '台灣銀行',
        'bank_branch' => '信義分行',
        'bank_account' => '123-456-789012',
        'is_active' => true,
        'is_default' => true,
    ],
    [
        'account_name' => '國泰世華支票帳戶',
        'bank_name' => '國泰世華銀行',
        'bank_branch' => '敦南分行',
        'bank_account' => '987-654-321098',
        'is_active' => true,
        'is_default' => false,
    ],
    [
        'account_name' => '玉山銀行外幣帳戶',
        'bank_name' => '玉山銀行',
        'bank_branch' => '南京東路分行',
        'bank_account' => '555-888-999111',
        'is_active' => true,
        'is_default' => false,
    ],
];

foreach ($bankAccountsData as $data) {
    $bankAccount = BankAccount::firstOrCreate(
        ['bank_account' => $data['bank_account']],
        $data
    );
    echo "  ✓ {$bankAccount->bank_name} - {$bankAccount->bank_account}\n";
}
echo "  總計: " . BankAccount::count() . " 個銀行帳戶\n\n";

// ===== 10. 設定系統參數 =====
echo "⚙️  設定系統參數...\n";

TenantSetting::set('closing_day', 25);
TenantSetting::set('default_currency', 'TWD');
TenantSetting::set('fiscal_year_start_month', 1);

echo "  ✓ 關帳日: 每月25日\n";
echo "  ✓ 預設幣值: TWD\n";
echo "  ✓ 會計年度起始月: 1月\n\n";

// ===== 統計報告 =====
echo str_repeat('=', 60) . "\n";
echo "✨ 測試資料建立完成！\n";
echo str_repeat('=', 60) . "\n\n";

echo "📊 資料統計：\n";
echo "  • 公司客戶：" . Company::where('is_client', true)->count() . " 家\n";
echo "  • 供應商：" . Company::where('is_client', false)->count() . " 家\n";
echo "  • 使用者：" . User::count() . " 位\n";
echo "  • 專案：" . Project::count() . " 個\n";
echo "  • 應收帳款：" . Receivable::count() . " 筆 (總金額: NT$ " . number_format(Receivable::sum('amount')) . ")\n";
echo "  • 應付帳款：" . Payable::count() . " 筆 (總金額: NT$ " . number_format(Payable::sum('amount')) . ")\n";
echo "  • 標籤：" . Tag::count() . " 個\n";
echo "  • 支出項目：" . ExpenseCategory::count() . " 個\n";
echo "  • 稅款設定：" . TaxSetting::count() . " 個\n";
echo "  • 銀行帳戶：" . BankAccount::count() . " 個\n\n";

echo "🌐 請訪問以下頁面測試：\n";
echo "  • 專案管理：https://abc123.ecount.test/projects\n";
echo "  • 應收帳款：https://abc123.ecount.test/receivables\n";
echo "  • 應付帳款：https://abc123.ecount.test/payables\n";
echo "  • 客戶廠商：https://abc123.ecount.test/companies\n";
echo "  • 標籤管理：https://abc123.ecount.test/tags\n";
echo "  • 支出項目：https://abc123.ecount.test/expense-categories\n";
echo "  • 稅款設定：https://abc123.ecount.test/tax-settings\n";
echo "  • 銀行帳戶：https://abc123.ecount.test/bank-accounts\n\n";

echo "🎉 所有測試資料已建立完成，可以開始測試系統功能了！\n";
