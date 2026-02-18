<?php

/**
 * 建立完整的其他測試資料
 * 包含：支付記錄、公告、標籤關聯、專案成員等
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
use App\Models\ReceivablePayment;
use App\Models\PayablePayment;
use App\Models\Announcement;
use App\Models\Tag;

$tenant = Tenant::find('abc123');
tenancy()->initialize($tenant);

echo "\n" . str_repeat('═', 80) . "\n";
echo "建立完整的其他測試資料\n";
echo str_repeat('═', 80) . "\n\n";

// ===== 階段一：建立應收款項支付記錄 =====
echo "【階段一】建立應收款項支付記錄\n";
echo str_repeat('-', 80) . "\n";

$receivables = Receivable::all();
$paymentMethods = ['轉帳匯款', '現金', '支票', '信用卡'];
$receivablePaymentCount = 0;

foreach ($receivables as $receivable) {
    $remainingAmount = $receivable->received_amount;
    
    if ($remainingAmount > 0) {
        // 已收金額，建立支付記錄
        if ($receivable->status === 'paid') {
            // 全額付款，可能分 1-2 次
            $paymentCount = rand(1, 2);
            $amountPerPayment = $remainingAmount / $paymentCount;
            
            for ($i = 0; $i < $paymentCount; $i++) {
                $payment = ReceivablePayment::create([
                    'receivable_id' => $receivable->id,
                    'payment_date' => date('Y-m-d', strtotime($receivable->receipt_date . ' +' . ($i * 15) . ' days')),
                    'amount' => $i === $paymentCount - 1 ? $remainingAmount : round($amountPerPayment, 2),
                    'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                    'note' => '第 ' . ($i + 1) . ' 期付款',
                ]);
                $remainingAmount -= $payment->amount;
                $receivablePaymentCount++;
                echo "  ✓ 應收 {$receivable->receipt_no} - NT$ " . number_format($payment->amount) . " ({$payment->payment_method})\n";
            }
        } elseif ($receivable->status === 'partial') {
            // 部分付款，建立 1-2 筆記錄
            $paymentCount = rand(1, 2);
            $amountPerPayment = $remainingAmount / $paymentCount;
            
            for ($i = 0; $i < $paymentCount; $i++) {
                $payment = ReceivablePayment::create([
                    'receivable_id' => $receivable->id,
                    'payment_date' => date('Y-m-d', strtotime($receivable->receipt_date . ' +' . ($i * 10) . ' days')),
                    'amount' => $i === $paymentCount - 1 ? $remainingAmount : round($amountPerPayment, 2),
                    'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                    'note' => '部分付款 - 第 ' . ($i + 1) . ' 筆',
                ]);
                $remainingAmount -= $payment->amount;
                $receivablePaymentCount++;
                echo "  ✓ 應收 {$receivable->receipt_no} - NT$ " . number_format($payment->amount) . " ({$payment->payment_method})\n";
            }
        }
    }
}

echo "  總計: {$receivablePaymentCount} 筆應收支付記錄\n\n";

// ===== 階段二：建立應付款項支付記錄 =====
echo "【階段二】建立應付款項支付記錄\n";
echo str_repeat('-', 80) . "\n";

$payables = Payable::all();
$payablePaymentCount = 0;

foreach ($payables as $payable) {
    $remainingAmount = $payable->paid_amount;
    
    if ($remainingAmount > 0) {
        if ($payable->status === 'paid') {
            // 全額付款
            $paymentCount = rand(1, 2);
            $amountPerPayment = $remainingAmount / $paymentCount;
            
            for ($i = 0; $i < $paymentCount; $i++) {
                $payment = PayablePayment::create([
                    'payable_id' => $payable->id,
                    'payment_date' => date('Y-m-d', strtotime($payable->payment_date . ' +' . ($i * 10) . ' days')),
                    'amount' => $i === $paymentCount - 1 ? $remainingAmount : round($amountPerPayment, 2),
                    'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                    'note' => '第 ' . ($i + 1) . ' 期付款',
                ]);
                $remainingAmount -= $payment->amount;
                $payablePaymentCount++;
                echo "  ✓ 應付 {$payable->payment_no} - NT$ " . number_format($payment->amount) . " ({$payment->payment_method})\n";
            }
        } elseif ($payable->status === 'partial') {
            // 部分付款
            $paymentCount = rand(1, 2);
            $amountPerPayment = $remainingAmount / $paymentCount;
            
            for ($i = 0; $i < $paymentCount; $i++) {
                $payment = PayablePayment::create([
                    'payable_id' => $payable->id,
                    'payment_date' => date('Y-m-d', strtotime($payable->payment_date . ' +' . ($i * 7) . ' days')),
                    'amount' => $i === $paymentCount - 1 ? $remainingAmount : round($amountPerPayment, 2),
                    'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                    'note' => '部分付款 - 第 ' . ($i + 1) . ' 筆',
                ]);
                $remainingAmount -= $payment->amount;
                $payablePaymentCount++;
                echo "  ✓ 應付 {$payable->payment_no} - NT$ " . number_format($payment->amount) . " ({$payment->payment_method})\n";
            }
        }
    }
}

echo "  總計: {$payablePaymentCount} 筆應付支付記錄\n\n";

// ===== 階段三：建立公告資料 =====
echo "【階段三】建立公告資料\n";
echo str_repeat('-', 80) . "\n";

$admin = User::first();
$announcements = [
    [
        'content' => '系統將於本週六凌晨 2:00 - 4:00 進行維護，屆時將暫時無法使用，請提前安排工作。',
        'is_active' => true,
    ],
    [
        'content' => '【重要】本月 25 日為關帳日，請各位同仁務必在此之前完成所有帳款登記作業。',
        'is_active' => true,
    ],
    [
        'content' => '財務系統新增銀行帳戶管理功能，請至個人設定頁面新增您的銀行資訊。',
        'is_active' => true,
    ],
    [
        'content' => '【會議通知】本週五下午 3:00 召開專案進度檢討會議，請相關人員準時參加。',
        'is_active' => true,
    ],
    [
        'content' => '歡迎使用 ecount 財務管理系統！如有任何問題，請聯絡系統管理員。',
        'is_active' => false,
    ],
];

foreach ($announcements as $data) {
    $announcement = Announcement::create([
        'content' => $data['content'],
        'is_active' => $data['is_active'],
        'created_by' => $admin->id,
        'updated_by' => $admin->id,
    ]);
    $status = $announcement->is_active ? '啟用' : '停用';
    echo "  ✓ {$status} - " . mb_substr($announcement->content, 0, 30) . "...\n";
}

echo "  總計: " . count($announcements) . " 筆公告\n\n";

// ===== 階段四：建立標籤關聯 =====
echo "【階段四】建立標籤關聯\n";
echo str_repeat('-', 80) . "\n";

$projectTags = Tag::where('type', 'project')->get();
$companyTags = Tag::where('type', 'company')->get();
$taggableCount = 0;

// 為專案加上標籤
$projects = Project::all();
foreach ($projects as $project) {
    $tagsToAttach = $projectTags->random(rand(1, 2))->pluck('id')->toArray();
    $project->tags()->sync($tagsToAttach);
    $taggableCount += count($tagsToAttach);
    $tagNames = $projectTags->whereIn('id', $tagsToAttach)->pluck('name')->join(', ');
    echo "  ✓ 專案 '{$project->name}' - 標籤: {$tagNames}\n";
}

// 為公司加上標籤
$companies = Company::all();
foreach ($companies as $company) {
    if ($companyTags->count() > 0) {
        $tagsToAttach = $companyTags->random(rand(1, 2))->pluck('id')->toArray();
        $company->tags()->sync($tagsToAttach);
        $taggableCount += count($tagsToAttach);
        $tagNames = $companyTags->whereIn('id', $tagsToAttach)->pluck('name')->join(', ');
        echo "  ✓ 公司 '{$company->name}' - 標籤: {$tagNames}\n";
    }
}

echo "  總計: {$taggableCount} 個標籤關聯\n\n";

// ===== 階段五：建立專案成員關聯 =====
echo "【階段五】建立專案成員關聯\n";
echo str_repeat('-', 80) . "\n";

$users = User::where('is_active', true)->get();
$roles = ['專案經理', '開發人員', '設計人員', '測試人員', '顧問'];
$memberCount = 0;

foreach ($projects as $project) {
    // 每個專案 2-4 名成員
    $memberNum = rand(2, min(4, $users->count()));
    $selectedUsers = $users->random($memberNum);
    
    foreach ($selectedUsers as $user) {
        $joinDate = date('Y-m-d', strtotime($project->start_date . ' +' . rand(0, 30) . ' days'));
        
        $project->members()->attach($user->id, [
            'role' => $roles[array_rand($roles)],
            'joined_at' => $joinDate,
        ]);
        $memberCount++;
    }
    
    echo "  ✓ 專案 '{$project->name}' - {$memberNum} 名成員\n";
}

echo "  總計: {$memberCount} 個專案成員關聯\n\n";

// ===== 階段六：補充客戶公司資料 =====
echo "【階段六】補充客戶公司資料\n";
echo str_repeat('-', 80) . "\n";

// 將部分現有公司改為客戶
$companiesToUpdate = Company::where('is_client', false)->limit(3)->get();
foreach ($companiesToUpdate as $company) {
    $company->update(['is_client' => true]);
    echo "  ✓ 將 '{$company->name}' 設為客戶\n";
}

// 新增額外的客戶公司
$newClients = [
    [
        'code' => 'C004',
        'name' => '智慧科技股份有限公司',
        'tax_id' => '67890123',
        'contact_person' => '劉總經理',
        'phone' => '02-9999-8888',
        'email' => 'info@smarttech.com',
        'address' => '新北市板橋區文化路二段100號',
        'is_active' => true,
        'is_client' => true,
    ],
    [
        'code' => 'C005',
        'name' => '綠能環保企業',
        'tax_id' => '78901234',
        'contact_person' => '黃經理',
        'phone' => '03-8888-7777',
        'email' => 'contact@greeneco.com',
        'address' => '桃園市桃園區中正路50號',
        'is_active' => true,
        'is_client' => true,
    ],
];

foreach ($newClients as $data) {
    $client = Company::firstOrCreate(
        ['code' => $data['code']],
        $data
    );
    echo "  ✓ 新增客戶 '{$client->name}'\n";
}

$totalClients = Company::where('is_client', true)->count();
echo "  總計: {$totalClients} 家客戶公司\n\n";

// ===== 階段七：建立進階測試場景 =====
echo "【階段七】建立進階測試場景\n";
echo str_repeat('-', 80) . "\n";

// 建立逾期應收帳款
$overdueReceivable = Receivable::create([
    'receipt_no' => 'RCV-OVERDUE-001',
    'invoice_no' => 'INV-OVERDUE-001',
    'company_id' => Company::where('is_client', true)->first()->id,
    'project_id' => Project::first()->id,
    'responsible_user_id' => User::first()->id,
    'receipt_date' => date('Y-m-d', strtotime('-90 days')),
    'due_date' => date('Y-m-d', strtotime('-60 days')),
    'amount' => 200000,
    'received_amount' => 0,
    'status' => 'overdue',
    'content' => '測試逾期應收帳款',
]);
echo "  ✓ 建立逾期應收帳款 - {$overdueReceivable->receipt_no}\n";

// 建立逾期應付帳款
$overduePayable = Payable::create([
    'payment_no' => 'PAY-OVERDUE-001',
    'invoice_no' => 'BILL-OVERDUE-001',
    'company_id' => Company::where('is_client', false)->first()->id,
    'project_id' => Project::first()->id,
    'responsible_user_id' => User::first()->id,
    'payment_date' => date('Y-m-d', strtotime('-45 days')),
    'due_date' => date('Y-m-d', strtotime('-15 days')),
    'amount' => 80000,
    'paid_amount' => 0,
    'status' => 'overdue',
    'type' => '外包費用',
    'content' => '測試逾期應付帳款',
]);
echo "  ✓ 建立逾期應付帳款 - {$overduePayable->payment_no}\n";

// 建立已完成的專案
$completedProject = Project::create([
    'code' => 'PRJ-COMPLETED-001',
    'name' => '已完成測試專案',
    'project_type' => '系統開發',
    'company_id' => Company::where('is_client', true)->first()->id,
    'manager_id' => User::first()->id,
    'status' => 'completed',
    'start_date' => date('Y-m-d', strtotime('-180 days')),
    'end_date' => date('Y-m-d', strtotime('-30 days')),
    'budget' => 1000000,
    'actual_cost' => 950000,
    'description' => '測試已完成專案',
]);
echo "  ✓ 建立已完成專案 - {$completedProject->name}\n";

// 建立暫停的專案
$onHoldProject = Project::create([
    'code' => 'PRJ-ONHOLD-001',
    'name' => '暫停測試專案',
    'project_type' => '網站開發',
    'company_id' => Company::where('is_client', true)->first()->id,
    'manager_id' => User::first()->id,
    'status' => 'on_hold',
    'start_date' => date('Y-m-d', strtotime('-60 days')),
    'end_date' => date('Y-m-d', strtotime('+90 days')),
    'budget' => 500000,
    'description' => '測試暫停專案',
]);
echo "  ✓ 建立暫停專案 - {$onHoldProject->name}\n\n";

// ===== 最終統計 =====
echo str_repeat('═', 80) . "\n";
echo "✨ 資料建立完成！\n";
echo str_repeat('═', 80) . "\n\n";

echo "📊 新建立的資料統計：\n";
echo "  • 應收支付記錄: {$receivablePaymentCount} 筆\n";
echo "  • 應付支付記錄: {$payablePaymentCount} 筆\n";
echo "  • 公告: " . count($announcements) . " 筆\n";
echo "  • 標籤關聯: {$taggableCount} 個\n";
echo "  • 專案成員: {$memberCount} 個\n";
echo "  • 客戶公司: {$totalClients} 家\n";
echo "  • 進階測試場景: 4 個\n\n";

echo "📊 全系統資料統計：\n";
echo "  • 公司/客戶: " . Company::count() . " 筆\n";
echo "  • 使用者: " . User::count() . " 位\n";
echo "  • 專案: " . Project::count() . " 個\n";
echo "  • 應收帳款: " . Receivable::count() . " 筆\n";
echo "  • 應付帳款: " . Payable::count() . " 筆\n";
echo "  • 應收支付記錄: " . ReceivablePayment::count() . " 筆\n";
echo "  • 應付支付記錄: " . PayablePayment::count() . " 筆\n";
echo "  • 公告: " . Announcement::count() . " 筆\n";
echo "  • 標籤: " . Tag::count() . " 個\n";
echo "  • 標籤關聯: " . DB::table('taggables')->count() . " 個\n";
echo "  • 專案成員: " . DB::table('project_members')->count() . " 個\n";
echo "  • 銀行帳戶: " . App\Models\BankAccount::count() . " 個\n\n";

echo "✅ 所有測試資料建立完成！\n\n";
