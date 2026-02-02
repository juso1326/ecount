<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// 切換到測試租戶
$tenant = \App\Models\Tenant::find('abc123');
if (!$tenant) {
    echo "❌ 租戶 abc123 不存在\n";
    exit(1);
}

tenancy()->initialize($tenant);
echo "✅ 已切換到租戶: {$tenant->id}\n\n";

use App\Models\Company;
use App\Models\Department;
use App\Models\Project;
use App\Models\User;
use App\Models\Receivable;
use App\Models\Payable;

// 1. 建立公司資料（客戶/供應商）
echo "📦 建立公司資料...\n";

$companies = [
    [
        'code' => 'C001',
        'name' => '台灣科技有限公司',
        'type' => 'company',
        'contact_person' => '張經理',
        'phone' => '02-2345-6789',
        'email' => 'contact@taiwantech.com',
        'address' => '台北市信義區信義路五段7號',
        'is_active' => true,
    ],
    [
        'code' => 'C002',
        'name' => '創新設計工作室',
        'type' => 'company',
        'contact_person' => '李設計',
        'phone' => '02-8765-4321',
        'email' => 'hello@creative-design.com',
        'address' => '台北市大安區復興南路一段390號',
        'is_active' => true,
    ],
    [
        'code' => 'C003',
        'name' => '全球行銷股份有限公司',
        'type' => 'company',
        'contact_person' => '王總監',
        'phone' => '03-1234-5678',
        'email' => 'info@global-marketing.com',
        'address' => '桃園市中壢區中山路123號',
        'is_active' => true,
    ],
    [
        'code' => 'V001',
        'name' => '優質印刷廠',
        'type' => 'company',
        'contact_person' => '陳老闆',
        'phone' => '04-2222-3333',
        'email' => 'print@quality.com',
        'address' => '台中市西區民權路456號',
        'is_active' => true,
    ],
];

foreach ($companies as $companyData) {
    $company = Company::firstOrCreate(
        ['code' => $companyData['code']],
        $companyData
    );
    echo "  ✓ {$company->code} - {$company->name}\n";
}

// 2. 建立部門（如果還沒有）
echo "\n📁 建立部門資料...\n";

$departments = [
    ['code' => 'D01', 'name' => '設計部'],
    ['code' => 'D02', 'name' => '工程部'],
    ['code' => 'D03', 'name' => '業務部'],
];

foreach ($departments as $deptData) {
    $dept = Department::firstOrCreate(
        ['code' => $deptData['code']],
        $deptData
    );
    echo "  ✓ {$dept->code} - {$dept->name}\n";
}

// 3. 建立專案
echo "\n🎯 建立專案資料...\n";

$dept = Department::where('code', 'D01')->first();
$user = User::first(); // 取得第一個使用者作為負責人

$projects = [
    [
        'code' => 'PJ2024001',
        'name' => '企業形象網站設計',
        'company_id' => Company::where('code', 'C001')->first()->id,
        'department_id' => $dept->id,
        'manager_id' => $user->id,
        'start_date' => '2024-01-15',
        'end_date' => '2024-03-31',
        'budget' => 500000,
        'status' => 'in_progress',
        'description' => '為客戶設計全新企業形象網站，包含響應式設計與後台管理系統',
    ],
    [
        'code' => 'PJ2024002',
        'name' => '品牌識別系統重建',
        'company_id' => Company::where('code', 'C002')->first()->id,
        'department_id' => $dept->id,
        'manager_id' => $user->id,
        'start_date' => '2024-02-01',
        'end_date' => '2024-05-31',
        'budget' => 800000,
        'status' => 'in_progress',
        'description' => 'Logo 重新設計、視覺識別手冊、應用設計等',
    ],
    [
        'code' => 'PJ2025001',
        'name' => '行銷活動網站建置',
        'company_id' => Company::where('code', 'C003')->first()->id,
        'department_id' => $dept->id,
        'manager_id' => $user->id,
        'start_date' => '2025-01-10',
        'end_date' => '2025-02-28',
        'budget' => 300000,
        'status' => 'planning',
        'description' => '活動網站設計與開發，含會員系統',
    ],
];

foreach ($projects as $projectData) {
    $project = Project::where('code', $projectData['code'])->first();
    if (!$project) {
        $project = Project::create($projectData);
    }
    echo "  ✓ {$project->code} - {$project->name}\n";
}

// 4. 建立應收帳款
echo "\n💰 建立應收帳款資料...\n";

$project1 = Project::where('code', 'PJ2024001')->first();
$project2 = Project::where('code', 'PJ2024002')->first();
$project3 = Project::where('code', 'PJ2025001')->first();

$receivables = [
    [
        'company_id' => Company::where('code', 'C001')->first()->id,
        'project_id' => $project1->id,
        'receipt_no' => 'INV-2024-001',
        'invoice_no' => 'INV-2024-001',
        'receipt_date' => '2024-02-01',
        'due_date' => '2024-03-01',
        'amount' => 250000,
        'received_amount' => 250000,
        'status' => 'paid',
        'note' => '企業形象網站設計 - 第一期款（50%）',
    ],
    [
        'company_id' => Company::where('code', 'C001')->first()->id,
        'project_id' => $project1->id,
        'receipt_no' => 'INV-2024-002',
        'invoice_no' => 'INV-2024-002',
        'receipt_date' => '2024-03-15',
        'due_date' => '2024-04-15',
        'amount' => 250000,
        'received_amount' => 0,
        'status' => 'unpaid',
        'note' => '企業形象網站設計 - 第二期款（50%）',
    ],
    [
        'company_id' => Company::where('code', 'C002')->first()->id,
        'project_id' => $project2->id,
        'receipt_no' => 'INV-2024-003',
        'invoice_no' => 'INV-2024-003',
        'receipt_date' => '2024-02-15',
        'due_date' => '2024-03-15',
        'amount' => 400000,
        'received_amount' => 400000,
        'status' => 'paid',
        'note' => '品牌識別系統重建 - 第一期款（50%）',
    ],
    [
        'company_id' => Company::where('code', 'C002')->first()->id,
        'project_id' => $project2->id,
        'receipt_no' => 'INV-2024-004',
        'invoice_no' => 'INV-2024-004',
        'receipt_date' => '2024-04-01',
        'due_date' => '2024-05-01',
        'amount' => 400000,
        'received_amount' => 200000,
        'status' => 'partially_paid',
        'note' => '品牌識別系統重建 - 第二期款（50%）',
    ],
    [
        'company_id' => Company::where('code', 'C003')->first()->id,
        'project_id' => $project3->id,
        'receipt_no' => 'INV-2025-001',
        'invoice_no' => 'INV-2025-001',
        'receipt_date' => '2025-01-20',
        'due_date' => '2025-02-20',
        'amount' => 150000,
        'received_amount' => 0,
        'status' => 'unpaid',
        'note' => '行銷活動網站建置 - 第一期款（50%）',
    ],
];

foreach ($receivables as $receivableData) {
    $receivable = Receivable::where('receipt_no', $receivableData['receipt_no'])->first();
    if (!$receivable) {
        $receivable = Receivable::create($receivableData);
    }
    echo "  ✓ {$receivable->receipt_no} - NT$ " . number_format($receivable->amount) . " ({$receivable->status})\n";
}

// 5. 建立應付帳款
echo "\n💸 建立應付帳款資料...\n";

$payables = [
    [
        'company_id' => Company::where('code', 'V001')->first()->id,
        'project_id' => $project1->id,
        'payment_no' => 'BILL-2024-001',
        'invoice_no' => 'BILL-2024-001',
        'payment_date' => '2024-02-10',
        'due_date' => '2024-03-10',
        'amount' => 50000,
        'paid_amount' => 50000,
        'status' => 'paid',
        'note' => '印刷品製作費用',
    ],
    [
        'company_id' => Company::where('code', 'V001')->first()->id,
        'project_id' => $project2->id,
        'payment_no' => 'BILL-2024-002',
        'invoice_no' => 'BILL-2024-002',
        'payment_date' => '2024-03-01',
        'due_date' => '2024-04-01',
        'amount' => 80000,
        'paid_amount' => 40000,
        'status' => 'partially_paid',
        'note' => '名片、信紙等印刷品',
    ],
    [
        'company_id' => Company::where('code', 'V001')->first()->id,
        'project_id' => $project1->id,
        'payment_no' => 'BILL-2024-003',
        'invoice_no' => 'BILL-2024-003',
        'payment_date' => '2024-03-20',
        'due_date' => '2024-04-20',
        'amount' => 35000,
        'paid_amount' => 0,
        'status' => 'unpaid',
        'note' => '宣傳海報印刷',
    ],
];

foreach ($payables as $payableData) {
    $payable = Payable::where('payment_no', $payableData['payment_no'])->first();
    if (!$payable) {
        $payable = Payable::create($payableData);
    }
    echo "  ✓ {$payable->payment_no} - NT$ " . number_format($payable->amount) . " ({$payable->status})\n";
}

echo "\n✅ 測試資料建立完成！\n";
echo "\n📊 資料統計：\n";
echo "  • 公司：" . Company::count() . " 筆\n";
echo "  • 部門：" . Department::count() . " 筆\n";
echo "  • 專案：" . Project::count() . " 筆\n";
echo "  • 應收帳款：" . Receivable::count() . " 筆\n";
echo "  • 應付帳款：" . Payable::count() . " 筆\n";

echo "\n🌐 請訪問：\n";
echo "  應收帳款：https://abc123.ecount.test/receivables\n";
echo "  應付帳款：https://abc123.ecount.test/payables\n";
echo "  專案管理：https://abc123.ecount.test/projects\n";
