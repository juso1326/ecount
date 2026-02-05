<?php
/**
 * 建立完整的測試資料
 * 使用方法: php artisan tinker < create-comprehensive-test-data.php
 */

use App\Models\Company;
use App\Models\Department;
use App\Models\User;
use App\Models\Project;
use App\Models\Receivable;
use App\Models\Payable;

echo "=== 開始建立測試資料 ===\n\n";

// 1. 建立公司
echo "1. 建立公司...\n";
$companies = [];
$companyData = [
    ['code' => 'C001', 'name' => '台灣科技股份有限公司', 'tax_id' => '12345678', 'phone' => '02-2345-6789'],
    ['code' => 'C002', 'name' => '創新軟體有限公司', 'tax_id' => '23456789', 'phone' => '02-3456-7890'],
    ['code' => 'C003', 'name' => '數位行銷公司', 'tax_id' => '34567890', 'phone' => '02-4567-8901'],
    ['code' => 'C004', 'name' => '建築設計事務所', 'tax_id' => '45678901', 'phone' => '02-5678-9012'],
    ['code' => 'C005', 'name' => '製造工業股份有限公司', 'tax_id' => '56789012', 'phone' => '02-6789-0123'],
];

foreach ($companyData as $data) {
    $companies[] = Company::create([
        'code' => $data['code'],
        'name' => $data['name'],
        'tax_id' => $data['tax_id'],
        'phone' => $data['phone'],
        'address' => '台北市信義區信義路五段7號',
        'is_active' => true,
    ]);
}
echo "   ✓ 已建立 " . count($companies) . " 家公司\n\n";

// 2. 建立部門
echo "2. 建立部門...\n";
$departments = [];
$deptData = [
    ['code' => 'D001', 'name' => '業務部'],
    ['code' => 'D002', 'name' => '研發部'],
    ['code' => 'D003', 'name' => '行銷部'],
    ['code' => 'D004', 'name' => '財務部'],
    ['code' => 'D005', 'name' => '人資部'],
];

foreach ($deptData as $data) {
    $departments[] = Department::create([
        'code' => $data['code'],
        'name' => $data['name'],
        'is_active' => true,
    ]);
}
echo "   ✓ 已建立 " . count($departments) . " 個部門\n\n";

// 3. 建立使用者
echo "3. 建立使用者...\n";
$users = [];
$userData = [
    ['name' => '王大明', 'email' => 'wang@example.com'],
    ['name' => '李小華', 'email' => 'lee@example.com'],
    ['name' => '陳美玲', 'email' => 'chen@example.com'],
    ['name' => '林志明', 'email' => 'lin@example.com'],
    ['name' => '張雅婷', 'email' => 'chang@example.com'],
];

foreach ($userData as $data) {
    $users[] = User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => bcrypt('password'),
        'is_active' => true,
    ]);
}
echo "   ✓ 已建立 " . count($users) . " 位使用者\n\n";

// 4. 建立專案
echo "4. 建立專案...\n";
$projects = [];
$projectData = [
    [
        'name' => 'ERP系統開發專案',
        'project_type' => '軟體開發',
        'status' => 'in_progress',
        'budget' => 5000000,
        'start_date' => '2025-01-15',
        'end_date' => '2025-12-31',
        'description' => '開發全功能ERP系統',
    ],
    [
        'name' => '官網改版專案',
        'project_type' => '網站建置',
        'status' => 'in_progress',
        'budget' => 800000,
        'start_date' => '2025-02-01',
        'end_date' => '2025-06-30',
        'description' => '官方網站全面改版升級',
    ],
    [
        'name' => '行銷活動專案',
        'project_type' => '行銷企劃',
        'status' => 'planning',
        'budget' => 1200000,
        'start_date' => '2025-03-01',
        'end_date' => '2025-08-31',
        'description' => '年度品牌行銷活動',
    ],
    [
        'name' => '辦公室裝修專案',
        'project_type' => '工程建設',
        'status' => 'completed',
        'budget' => 3000000,
        'start_date' => '2024-10-01',
        'end_date' => '2025-01-31',
        'description' => '總部辦公室裝修工程',
    ],
    [
        'name' => '生產線優化專案',
        'project_type' => '製造改善',
        'status' => 'on_hold',
        'budget' => 2500000,
        'start_date' => '2025-01-01',
        'end_date' => '2025-09-30',
        'description' => '產線自動化升級',
    ],
];

foreach ($projectData as $index => $data) {
    $projects[] = Project::create([
        'code' => 'PRJ-' . str_pad($index + 1, 5, '0', STR_PAD_LEFT),
        'name' => $data['name'],
        'project_type' => $data['project_type'],
        'company_id' => $companies[array_rand($companies)]->id,
        'department_id' => $departments[array_rand($departments)]->id,
        'manager_id' => $users[array_rand($users)]->id,
        'status' => $data['status'],
        'start_date' => $data['start_date'],
        'end_date' => $data['end_date'],
        'budget' => $data['budget'],
        'description' => $data['description'],
        'is_active' => true,
    ]);
}
echo "   ✓ 已建立 " . count($projects) . " 個專案\n\n";

// 5. 建立應收帳款
echo "5. 建立應收帳款...\n";
$receivables = [];
$receivableData = [
    [
        'amount' => 1500000,
        'received_amount' => 1500000,
        'status' => 'paid',
        'receipt_date' => '2025-01-20',
        'paid_date' => '2025-02-20',
        'content' => 'ERP系統第一期款項',
    ],
    [
        'amount' => 400000,
        'received_amount' => 400000,
        'status' => 'paid',
        'receipt_date' => '2025-02-10',
        'paid_date' => '2025-02-25',
        'content' => '官網改版首期款',
    ],
    [
        'amount' => 600000,
        'received_amount' => 300000,
        'status' => 'partial',
        'receipt_date' => '2025-03-01',
        'paid_date' => null,
        'content' => '行銷活動定金',
    ],
    [
        'amount' => 2000000,
        'received_amount' => 0,
        'status' => 'unpaid',
        'receipt_date' => '2025-01-15',
        'paid_date' => null,
        'content' => 'ERP系統第二期款項',
    ],
    [
        'amount' => 500000,
        'received_amount' => 0,
        'status' => 'overdue',
        'receipt_date' => '2024-12-01',
        'due_date' => '2025-01-01',
        'paid_date' => null,
        'content' => '辦公室裝修尾款',
    ],
];

foreach ($receivableData as $index => $data) {
    $project = $projects[array_rand($projects)];
    $receivables[] = Receivable::create([
        'receipt_no' => 'RCV-' . str_pad($index + 1, 5, '0', STR_PAD_LEFT),
        'project_id' => $project->id,
        'company_id' => $project->company_id,
        'responsible_user_id' => $users[array_rand($users)]->id,
        'receipt_date' => $data['receipt_date'],
        'due_date' => $data['due_date'] ?? date('Y-m-d', strtotime($data['receipt_date'] . ' +30 days')),
        'amount' => $data['amount'],
        'received_amount' => $data['received_amount'],
        'status' => $data['status'],
        'paid_date' => $data['paid_date'],
        'content' => $data['content'],
        'invoice_no' => 'INV-' . date('Ymd') . '-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
    ]);
}
echo "   ✓ 已建立 " . count($receivables) . " 筆應收帳款\n\n";

// 6. 建立應付帳款
echo "6. 建立應付帳款...\n";
$payables = [];
$payableData = [
    [
        'amount' => 800000,
        'paid_amount' => 800000,
        'status' => 'paid',
        'payment_date' => '2025-01-10',
        'paid_date' => '2025-01-15',
        'type' => '專案費用',
        'content' => '軟體開發外包費用',
    ],
    [
        'amount' => 300000,
        'paid_amount' => 300000,
        'status' => 'paid',
        'payment_date' => '2025-02-05',
        'paid_date' => '2025-02-10',
        'type' => '設計費用',
        'content' => '網站設計費用',
    ],
    [
        'amount' => 500000,
        'paid_amount' => 250000,
        'status' => 'partial',
        'payment_date' => '2025-03-01',
        'paid_date' => null,
        'type' => '行銷費用',
        'content' => '廣告投放費用',
    ],
    [
        'amount' => 1200000,
        'paid_amount' => 0,
        'status' => 'unpaid',
        'payment_date' => '2025-01-20',
        'paid_date' => null,
        'type' => '工程款',
        'content' => '裝修工程費用',
    ],
    [
        'amount' => 600000,
        'paid_amount' => 0,
        'status' => 'unpaid',
        'payment_date' => '2025-02-15',
        'paid_date' => null,
        'type' => '設備採購',
        'content' => '產線設備採購',
    ],
];

foreach ($payableData as $index => $data) {
    $project = $projects[array_rand($projects)];
    $payables[] = Payable::create([
        'payment_no' => 'PAY-' . str_pad($index + 1, 5, '0', STR_PAD_LEFT),
        'project_id' => $project->id,
        'company_id' => $companies[array_rand($companies)]->id,
        'responsible_user_id' => $users[array_rand($users)]->id,
        'type' => $data['type'],
        'payment_date' => $data['payment_date'],
        'due_date' => date('Y-m-d', strtotime($data['payment_date'] . ' +30 days')),
        'amount' => $data['amount'],
        'paid_amount' => $data['paid_amount'],
        'status' => $data['status'],
        'paid_date' => $data['paid_date'],
        'content' => $data['content'],
        'invoice_no' => 'PI-' . date('Ymd') . '-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
    ]);
}
echo "   ✓ 已建立 " . count($payables) . " 筆應付帳款\n\n";

// 7. 統計結果
echo "=== 測試資料建立完成 ===\n\n";
echo "📊 資料統計:\n";
echo "   公司: " . Company::count() . " 筆\n";
echo "   部門: " . Department::count() . " 筆\n";
echo "   使用者: " . User::count() . " 筆\n";
echo "   專案: " . Project::count() . " 筆\n";
echo "   應收帳款: " . Receivable::count() . " 筆\n";
echo "   應付帳款: " . Payable::count() . " 筆\n\n";

echo "✅ 所有測試資料已成功建立！\n";
echo "\n可以開始測試系統功能了：\n";
echo "- 專案列表: https://abc123.ecount.test/projects\n";
echo "- 應收帳款: https://abc123.ecount.test/receivables\n";
echo "- 應付帳款: https://abc123.ecount.test/payables\n";
