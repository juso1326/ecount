<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Tenant;
use App\Models\Company;
use App\Models\Department;
use App\Models\Project;
use App\Models\Receivable;
use App\Models\Payable;

$tenant = Tenant::find('abc123');
$tenant->run(function() {
    echo "=== 建立測試資料 ===" . PHP_EOL;
    
    // 1. 建立公司
    $companies = [
        ['code' => 'C002', 'name' => '聯發科技股份有限公司', 'short_name' => '聯發科', 'type' => 'company', 'status' => 'active', 'phone' => '03-5670-8888', 'email' => 'info@mediatek.com'],
        ['code' => 'C003', 'name' => '華碩電腦股份有限公司', 'short_name' => '華碩', 'type' => 'company', 'status' => 'active', 'phone' => '02-2894-3447', 'email' => 'service@asus.com'],
        ['code' => 'C004', 'name' => '台積電股份有限公司', 'short_name' => '台積電', 'type' => 'company', 'status' => 'active', 'phone' => '03-5636-8888', 'email' => 'contact@tsmc.com'],
    ];
    
    foreach($companies as $c) {
        if(!Company::where('code', $c['code'])->exists()) {
            Company::create($c);
        }
    }
    echo "✓ 公司資料建立完成 (" . Company::count() . " 筆)" . PHP_EOL;
    
    // 2. 建立部門
    $departments = [
        ['code' => 'D02', 'name' => '技術部', 'status' => 'active', 'description' => '負責技術開發與專案執行'],
        ['code' => 'D03', 'name' => '設計部', 'status' => 'active', 'description' => '負責平面設計與UI設計'],
    ];
    
    foreach($departments as $d) {
        if(!Department::where('code', $d['code'])->exists()) {
            Department::create($d);
        }
    }
    echo "✓ 部門資料建立完成 (" . Department::count() . " 筆)" . PHP_EOL;
    
    // 3. 建立專案
    $company1 = Company::where('code', 'C0001')->first();
    $company2 = Company::where('code', 'C002')->first();
    $company3 = Company::where('code', 'C003')->first();
    $company4 = Company::where('code', 'C004')->first();
    $dept1 = Department::where('code', 'D001')->first();
    $dept2 = Department::where('code', 'D02')->first();
    $dept3 = Department::where('code', 'D03')->first();
    
    $projects = [
        ['code' => 'PJ2024002', 'name' => '聯發科官網改版', 'company_id' => $company2->id, 'department_id' => $dept3->id, 'budget' => 800000, 'start_date' => '2024-03-01', 'end_date' => '2024-06-30', 'status' => 'completed'],
        ['code' => 'PJ2024003', 'name' => '華碩APP開發專案', 'company_id' => $company3->id, 'department_id' => $dept2->id, 'budget' => 1200000, 'start_date' => '2024-05-10', 'end_date' => '2024-12-31', 'status' => 'in_progress'],
        ['code' => 'PJ2025001', 'name' => '台積電ERP系統', 'company_id' => $company4->id, 'department_id' => $dept2->id, 'budget' => 5000000, 'start_date' => '2025-01-01', 'end_date' => '2025-12-31', 'status' => 'in_progress'],
    ];
    
    foreach($projects as $p) {
        if(!Project::where('code', $p['code'])->exists()) {
            Project::create($p);
        }
    }
    echo "✓ 專案資料建立完成 (" . Project::count() . " 筆)" . PHP_EOL;
    
    // 4. 建立應收帳款
    $project1 = Project::where('code', 'P0001')->first();
    $project2 = Project::where('code', 'PJ2024002')->first();
    $project3 = Project::where('code', 'PJ2024003')->first();
    $project4 = Project::where('code', 'PJ2025001')->first();
    
    $receivables = [
        [
            'project_id' => $project2->id,
            'company_id' => $company2->id,
            'receipt_no' => 'R202405001',
            'invoice_no' => 'AB12345678',
            'receipt_date' => '2024-06-10',
            'due_date' => '2024-07-10',
            'amount' => 800000,
            'amount_before_tax' => 761904.76,
            'tax_amount' => 38095.24,
            'withholding_tax' => 10000,
            'received_amount' => 790000,
            'status' => 'paid',
            'content' => '官網改版驗收款',
            'payment_method' => 'transfer',
            'note' => '專案已結案',
        ],
        [
            'project_id' => $project3->id,
            'company_id' => $company3->id,
            'receipt_no' => 'R202407001',
            'invoice_no' => 'AC12345678',
            'receipt_date' => '2024-08-15',
            'due_date' => '2024-09-15',
            'amount' => 600000,
            'amount_before_tax' => 571428.57,
            'tax_amount' => 28571.43,
            'withholding_tax' => 0,
            'received_amount' => 300000,
            'status' => 'partial',
            'content' => '第一期開發款',
            'payment_method' => 'transfer',
            'note' => '已收 50%',
        ],
        [
            'project_id' => $project4->id,
            'company_id' => $company4->id,
            'receipt_no' => 'R202501001',
            'invoice_no' => 'AD12345678',
            'receipt_date' => '2025-03-01',
            'due_date' => '2025-04-01',
            'amount' => 1500000,
            'amount_before_tax' => 1428571.43,
            'tax_amount' => 71428.57,
            'withholding_tax' => 20000,
            'received_amount' => 0,
            'status' => 'unpaid',
            'content' => 'ERP系統第一期款',
            'payment_method' => 'transfer',
            'note' => '待收款',
        ],
    ];
    
    foreach($receivables as $r) {
        if(!Receivable::where('receipt_no', $r['receipt_no'])->exists()) {
            Receivable::create($r);
        }
    }
    echo "✓ 應收帳款建立完成 (" . Receivable::count() . " 筆)" . PHP_EOL;
    
    // 5. 建立應付帳款
    $payables = [
        [
            'project_id' => $project2->id,
            'vendor_name' => '設計工作室A',
            'payment_date' => '2024-04-01',
            'due_date' => '2024-04-30',
            'amount' => 120000,
            'paid_amount' => 120000,
            'status' => 'paid',
            'content' => 'UI/UX 設計費',
            'payment_method' => 'transfer',
            'note' => '已付款',
        ],
        [
            'project_id' => $project3->id,
            'vendor_name' => '大聯大控股',
            'payment_date' => '2024-06-10',
            'due_date' => '2024-07-10',
            'amount' => 180000,
            'paid_amount' => 90000,
            'status' => 'partial',
            'content' => '伺服器採購',
            'payment_method' => 'transfer',
            'note' => '已付 50%',
        ],
        [
            'project_id' => $project4->id,
            'vendor_name' => '欣技資訊',
            'payment_date' => '2025-02-01',
            'due_date' => '2025-03-01',
            'amount' => 200000,
            'paid_amount' => 0,
            'status' => 'unpaid',
            'content' => 'ERP軟體授權',
            'payment_method' => 'transfer',
            'note' => '待付款',
        ],
    ];
    
    foreach($payables as $p) {
        Payable::create($p);
    }
    echo "✓ 應付帳款建立完成 (" . Payable::count() . " 筆)" . PHP_EOL;
    
    echo PHP_EOL . "=== 最終統計 ===" . PHP_EOL;
    echo "公司: " . Company::count() . " 筆" . PHP_EOL;
    echo "部門: " . Department::count() . " 筆" . PHP_EOL;
    echo "專案: " . Project::count() . " 筆" . PHP_EOL;
    echo "應收: " . Receivable::count() . " 筆" . PHP_EOL;
    echo "應付: " . Payable::count() . " 筆" . PHP_EOL;
    echo PHP_EOL . "✅ 所有測試資料建立完成！" . PHP_EOL;
});
