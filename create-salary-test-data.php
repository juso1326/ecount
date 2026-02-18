<?php

/**
 * 建立薪資系統測試資料
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Tenant;
use App\Models\User;
use App\Models\Payable;
use App\Models\SalaryAdjustment;
use App\Models\Company;
use App\Models\Project;

$tenant = Tenant::find('abc123');
tenancy()->initialize($tenant);

echo "\n" . str_repeat('═', 80) . "\n";
echo "建立薪資系統測試資料\n";
echo str_repeat('═', 80) . "\n\n";

// 取得使用者
$users = User::where('is_active', true)->get();

if ($users->count() == 0) {
    echo "❌ 沒有可用的使用者\n";
    exit(1);
}

// 取得或建立公司（作為薪資支付單位）
$company = Company::first();
if (!$company) {
    $company = Company::create([
        'code' => 'COMP-001',
        'name' => '公司名稱',
        'is_active' => true,
    ]);
}

// 取得或建立專案
$project = Project::first();

// ===== 1. 建立員工基本薪資（應付帳款）=====
echo "【一】建立員工基本薪資\n";
echo str_repeat('-', 80) . "\n";

$baseSalaries = [
    50000, // 員工1
    45000, // 員工2
    48000, // 員工3
    42000, // 員工4
    52000, // 員工5
    46000, // 員工6
];

$salaryCount = 0;
foreach ($users as $index => $user) {
    $salary = $baseSalaries[$index] ?? 45000;
    
    // 建立近3個月的薪資記錄
    for ($i = 0; $i < 3; $i++) {
        $paymentDate = date('Y-m-25', strtotime("-{$i} months")); // 每月25日發薪
        
        Payable::create([
            'payment_no' => 'SAL-' . date('Ym', strtotime($paymentDate)) . '-' . str_pad($user->id, 3, '0', STR_PAD_LEFT),
            'invoice_no' => 'SAL-' . date('Ym', strtotime($paymentDate)) . '-' . str_pad($user->id, 3, '0', STR_PAD_LEFT),
            'company_id' => $company->id,
            'project_id' => $project ? $project->id : null,
            'responsible_user_id' => $user->id,
            'payee_type' => 'user',
            'payee_user_id' => $user->id,
            'type' => '薪資',
            'payment_date' => $paymentDate,
            'due_date' => $paymentDate,
            'amount' => $salary,
            'paid_amount' => $i == 0 ? 0 : $salary, // 當月未付，之前已付
            'status' => $i == 0 ? 'unpaid' : 'paid',
            'content' => $user->name . ' ' . date('Y年m月', strtotime($paymentDate)) . '薪資',
        ]);
        
        $salaryCount++;
    }
    
    echo "  ✓ {$user->name} - 月薪 NT$ " . number_format($salary) . " (建立 3 個月記錄)\n";
}

echo "  總計: {$salaryCount} 筆薪資記錄\n\n";

// ===== 2. 建立薪資調整項（加項）=====
echo "【二】建立薪資調整項 - 加項\n";
echo str_repeat('-', 80) . "\n";

$additions = [
    [
        'user' => $users[0],
        'title' => '全勤獎金',
        'amount' => 2000,
        'recurrence' => 'monthly',
        'start_date' => date('Y-m-01', strtotime('-2 months')),
        'remark' => '全勤者發放',
    ],
    [
        'user' => $users[0],
        'title' => '績效獎金',
        'amount' => 5000,
        'recurrence' => 'once',
        'start_date' => date('Y-m-01', strtotime('-1 month')),
        'end_date' => date('Y-m-t', strtotime('-1 month')),
        'remark' => '上月績效優良',
    ],
    [
        'user' => $users[1],
        'title' => '交通津貼',
        'amount' => 1500,
        'recurrence' => 'monthly',
        'start_date' => date('Y-m-01'),
        'remark' => '每月固定交通補助',
    ],
    [
        'user' => $users[2],
        'title' => '加班費',
        'amount' => 3000,
        'recurrence' => 'once',
        'start_date' => date('Y-m-01', strtotime('-1 month')),
        'end_date' => date('Y-m-t', strtotime('-1 month')),
        'remark' => '上月加班時數',
    ],
    [
        'user' => $users[3],
        'title' => '主管加給',
        'amount' => 8000,
        'recurrence' => 'monthly',
        'start_date' => date('Y-m-01', strtotime('-3 months')),
        'remark' => '主管職務加給',
    ],
];

$additionCount = 0;
foreach ($additions as $data) {
    SalaryAdjustment::create([
        'user_id' => $data['user']->id,
        'type' => 'add',
        'title' => $data['title'],
        'amount' => $data['amount'],
        'start_date' => $data['start_date'],
        'end_date' => $data['end_date'] ?? null,
        'recurrence' => $data['recurrence'],
        'is_active' => true,
        'remark' => $data['remark'],
    ]);
    $additionCount++;
    
    $cycle = $data['recurrence'] == 'monthly' ? '每月' : '單次';
    echo "  ✓ {$data['user']->name} - {$data['title']} NT$ " . number_format($data['amount']) . " ({$cycle})\n";
}

echo "  總計: {$additionCount} 項加項\n\n";

// ===== 3. 建立薪資調整項（扣項）=====
echo "【三】建立薪資調整項 - 扣項\n";
echo str_repeat('-', 80) . "\n";

$deductions = [
    [
        'user' => $users[0],
        'title' => '勞保費',
        'amount' => 1200,
        'recurrence' => 'monthly',
        'start_date' => date('Y-m-01', strtotime('-3 months')),
        'remark' => '員工自付勞保費',
    ],
    [
        'user' => $users[0],
        'title' => '健保費',
        'amount' => 800,
        'recurrence' => 'monthly',
        'start_date' => date('Y-m-01', strtotime('-3 months')),
        'remark' => '員工自付健保費',
    ],
    [
        'user' => $users[1],
        'title' => '勞保費',
        'amount' => 1150,
        'recurrence' => 'monthly',
        'start_date' => date('Y-m-01', strtotime('-3 months')),
        'remark' => '員工自付勞保費',
    ],
    [
        'user' => $users[1],
        'title' => '健保費',
        'amount' => 780,
        'recurrence' => 'monthly',
        'start_date' => date('Y-m-01', strtotime('-3 months')),
        'remark' => '員工自付健保費',
    ],
    [
        'user' => $users[2],
        'title' => '請假扣薪',
        'amount' => 2000,
        'recurrence' => 'once',
        'start_date' => date('Y-m-01', strtotime('-1 month')),
        'end_date' => date('Y-m-t', strtotime('-1 month')),
        'remark' => '事假2天扣薪',
    ],
    [
        'user' => $users[3],
        'title' => '勞保費',
        'amount' => 1100,
        'recurrence' => 'monthly',
        'start_date' => date('Y-m-01', strtotime('-3 months')),
        'remark' => '員工自付勞保費',
    ],
    [
        'user' => $users[4],
        'title' => '借支還款',
        'amount' => 5000,
        'recurrence' => 'monthly',
        'start_date' => date('Y-m-01', strtotime('-2 months')),
        'end_date' => date('Y-m-t', strtotime('+4 months')),
        'remark' => '借支分6期還款',
    ],
];

$deductionCount = 0;
foreach ($deductions as $data) {
    SalaryAdjustment::create([
        'user_id' => $data['user']->id,
        'type' => 'deduct',
        'title' => $data['title'],
        'amount' => $data['amount'],
        'start_date' => $data['start_date'],
        'end_date' => $data['end_date'] ?? null,
        'recurrence' => $data['recurrence'],
        'is_active' => true,
        'remark' => $data['remark'],
    ]);
    $deductionCount++;
    
    $cycle = $data['recurrence'] == 'monthly' ? '每月' : '單次';
    echo "  ✓ {$data['user']->name} - {$data['title']} NT$ " . number_format($data['amount']) . " ({$cycle})\n";
}

echo "  總計: {$deductionCount} 項扣項\n\n";

// ===== 統計資訊 =====
echo str_repeat('═', 80) . "\n";
echo "✨ 薪資系統測試資料建立完成！\n";
echo str_repeat('═', 80) . "\n\n";

echo "📊 資料統計：\n";
echo "  • 員工人數: " . $users->count() . " 位\n";
echo "  • 薪資記錄: " . Payable::where('payee_type', 'user')->count() . " 筆\n";
echo "  • 薪資調整項: " . SalaryAdjustment::count() . " 項\n";
echo "    - 加項: " . SalaryAdjustment::where('type', 'add')->count() . " 項\n";
echo "    - 扣項: " . SalaryAdjustment::where('type', 'deduct')->count() . " 項\n\n";

// 計算本月薪資總額
$currentMonth = date('Y-m');
$totalSalary = Payable::where('payee_type', 'user')
    ->where('payment_date', 'like', $currentMonth . '%')
    ->sum('amount');

echo "💰 本月薪資總額: NT$ " . number_format($totalSalary) . "\n\n";

echo "🌐 請訪問: https://abc123.ecount.test/salaries\n\n";
