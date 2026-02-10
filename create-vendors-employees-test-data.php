<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Tenant;
use App\Models\Company;
use App\Models\CompanyBankAccount;

$tenant = Tenant::find('abc123');
$tenant->run(function() {
    echo "=== 建立廠商與員工測試資料 ===" . PHP_EOL . PHP_EOL;
    
    // 1. 建立廠商（外製）資料
    echo "建立廠商資料..." . PHP_EOL;
    $vendors = [
        [
            'code' => 'V001',
            'name' => '巨匠電腦股份有限公司',
            'short_name' => '巨匠電腦',
            'type' => 'company',
            'is_client' => false,
            'is_outsource' => true,
            'is_member' => false,
            'is_active' => true,
            'tax_id' => '12345678',
            'phone' => '02-2311-4537',
            'email' => 'service@gjun.com.tw',
            'address' => '台北市中正區公園路30號3樓',
            'bank_accounts' => [
                ['bank_name' => '台灣銀行', 'branch_name' => '台北分行', 'account_number' => '001234567890', 'is_default' => true],
            ],
        ],
        [
            'code' => 'V002',
            'name' => '資拓宏宇國際股份有限公司',
            'short_name' => '資拓宏宇',
            'type' => 'company',
            'is_client' => false,
            'is_outsource' => true,
            'is_member' => false,
            'is_active' => true,
            'tax_id' => '23456789',
            'phone' => '02-6631-6888',
            'email' => 'contact@iisigroup.com',
            'address' => '新北市汐止區新台五路一段79號2樓',
            'bank_accounts' => [
                ['bank_name' => '第一銀行', 'branch_name' => '汐止分行', 'account_number' => '111234567890', 'is_default' => true],
            ],
        ],
        [
            'code' => 'V003',
            'name' => '精誠資訊股份有限公司',
            'short_name' => '精誠資訊',
            'type' => 'company',
            'is_client' => false,
            'is_outsource' => true,
            'is_member' => false,
            'is_active' => true,
            'tax_id' => '34567890',
            'phone' => '02-7720-1888',
            'email' => 'info@systex.com',
            'address' => '台北市內湖區堤頂大道二段407巷22號',
            'bank_accounts' => [
                ['bank_name' => '華南銀行', 'branch_name' => '內湖分行', 'account_number' => '222234567890', 'is_default' => true],
            ],
        ],
        [
            'code' => 'V004',
            'name' => '叡揚資訊股份有限公司',
            'short_name' => '叡揚資訊',
            'type' => 'company',
            'is_client' => false,
            'is_outsource' => true,
            'is_member' => false,
            'is_active' => true,
            'tax_id' => '45678901',
            'phone' => '02-2522-1351',
            'email' => 'service@gss.com.tw',
            'address' => '台北市中山區南京東路三段168號12樓',
            'bank_accounts' => [
                ['bank_name' => '彰化銀行', 'branch_name' => '南京東路分行', 'account_number' => '333234567890', 'is_default' => true],
            ],
        ],
        [
            'code' => 'V005',
            'name' => '鼎新電腦股份有限公司',
            'short_name' => '鼎新電腦',
            'type' => 'company',
            'is_client' => false,
            'is_outsource' => true,
            'is_member' => false,
            'is_active' => true,
            'tax_id' => '56789012',
            'phone' => '02-8911-1688',
            'email' => 'service@digiwin.com',
            'address' => '新北市新店區中興路三段219號',
            'bank_accounts' => [
                ['bank_name' => '合作金庫', 'branch_name' => '新店分行', 'account_number' => '444234567890', 'is_default' => true],
            ],
        ],
    ];
    
    foreach($vendors as $vendorData) {
        if(!Company::where('code', $vendorData['code'])->exists()) {
            $bankAccounts = $vendorData['bank_accounts'];
            unset($vendorData['bank_accounts']);
            
            $vendor = Company::create($vendorData);
            
            // 建立銀行帳號
            foreach($bankAccounts as $account) {
                CompanyBankAccount::create([
                    'company_id' => $vendor->id,
                    'bank_name' => $account['bank_name'],
                    'branch_name' => $account['branch_name'],
                    'account_number' => $account['account_number'],
                    'is_default' => $account['is_default'],
                ]);
            }
            
            echo "  ✓ 建立廠商: {$vendor->name} ({$vendor->code})" . PHP_EOL;
        } else {
            echo "  - 廠商已存在: {$vendorData['code']}" . PHP_EOL;
        }
    }
    
    echo PHP_EOL;
    
    // 2. 建立員工資料
    echo "建立員工資料..." . PHP_EOL;
    $employees = [
        [
            'code' => 'E001',
            'name' => '王小明',
            'short_name' => '王小明',
            'type' => 'individual',
            'is_client' => false,
            'is_outsource' => false,
            'is_member' => true,
            'is_active' => true,
            'hire_date' => '2022-01-10',
            'id_number' => 'A123456789',
            'birth_date' => '1990-05-15',
            'phone' => '02-2345-6789',
            'mobile' => '0912-345-678',
            'email' => 'wang.xiaoming@company.com',
            'address' => '台北市信義區信義路五段7號',
            'emergency_contact' => '王媽媽',
            'emergency_phone' => '0922-111-222',
            'bank_accounts' => [
                ['bank_name' => '台灣銀行', 'branch_name' => '信義分行', 'account_number' => '098765432100', 'is_default' => true],
            ],
        ],
        [
            'code' => 'E002',
            'name' => '李美麗',
            'short_name' => '李美麗',
            'type' => 'individual',
            'is_client' => false,
            'is_outsource' => false,
            'is_member' => true,
            'is_active' => true,
            'hire_date' => '2021-03-15',
            'id_number' => 'B234567890',
            'birth_date' => '1988-08-20',
            'phone' => '02-3456-7890',
            'mobile' => '0923-456-789',
            'email' => 'li.meili@company.com',
            'address' => '新北市板橋區中山路一段161號',
            'emergency_contact' => '李爸爸',
            'emergency_phone' => '0933-222-333',
            'bank_accounts' => [
                ['bank_name' => '第一銀行', 'branch_name' => '板橋分行', 'account_number' => '098765432101', 'is_default' => true],
            ],
        ],
        [
            'code' => 'E003',
            'name' => '張志明',
            'short_name' => '張志明',
            'type' => 'individual',
            'is_client' => false,
            'is_outsource' => false,
            'is_member' => true,
            'is_active' => true,
            'hire_date' => '2020-06-01',
            'id_number' => 'C345678901',
            'birth_date' => '1985-12-10',
            'phone' => '02-4567-8901',
            'mobile' => '0934-567-890',
            'email' => 'zhang.zhiming@company.com',
            'address' => '台北市大安區忠孝東路三段1號',
            'emergency_contact' => '張太太',
            'emergency_phone' => '0944-333-444',
            'bank_accounts' => [
                ['bank_name' => '華南銀行', 'branch_name' => '大安分行', 'account_number' => '098765432102', 'is_default' => true],
            ],
        ],
        [
            'code' => 'E004',
            'name' => '陳雅婷',
            'short_name' => '陳雅婷',
            'type' => 'individual',
            'is_client' => false,
            'is_outsource' => false,
            'is_member' => true,
            'is_active' => true,
            'hire_date' => '2023-02-20',
            'id_number' => 'D456789012',
            'birth_date' => '1992-03-25',
            'phone' => '02-5678-9012',
            'mobile' => '0945-678-901',
            'email' => 'chen.yating@company.com',
            'address' => '台北市中山區南京東路二段100號',
            'emergency_contact' => '陳媽媽',
            'emergency_phone' => '0955-444-555',
            'bank_accounts' => [
                ['bank_name' => '彰化銀行', 'branch_name' => '中山分行', 'account_number' => '098765432103', 'is_default' => true],
            ],
        ],
        [
            'code' => 'E005',
            'name' => '林俊傑',
            'short_name' => '林俊傑',
            'type' => 'individual',
            'is_client' => false,
            'is_outsource' => false,
            'is_member' => true,
            'is_active' => true,
            'hire_date' => '2019-09-01',
            'id_number' => 'E567890123',
            'birth_date' => '1987-11-18',
            'phone' => '02-6789-0123',
            'mobile' => '0956-789-012',
            'email' => 'lin.junjie@company.com',
            'address' => '新北市中和區中和路100號',
            'emergency_contact' => '林爸爸',
            'emergency_phone' => '0966-555-666',
            'bank_accounts' => [
                ['bank_name' => '合作金庫', 'branch_name' => '中和分行', 'account_number' => '098765432104', 'is_default' => true],
            ],
        ],
        [
            'code' => 'E006',
            'name' => '黃淑芬',
            'short_name' => '黃淑芬',
            'type' => 'individual',
            'is_client' => false,
            'is_outsource' => false,
            'is_member' => true,
            'is_active' => false,
            'hire_date' => '2018-04-15',
            'leave_date' => '2024-12-31',
            'id_number' => 'F678901234',
            'birth_date' => '1986-07-22',
            'phone' => '02-7890-1234',
            'mobile' => '0967-890-123',
            'email' => 'huang.shufen@company.com',
            'address' => '台北市松山區南京東路四段2號',
            'emergency_contact' => '黃先生',
            'emergency_phone' => '0977-666-777',
            'bank_accounts' => [
                ['bank_name' => '台北富邦銀行', 'branch_name' => '松山分行', 'account_number' => '098765432105', 'is_default' => true],
            ],
        ],
    ];
    
    foreach($employees as $employeeData) {
        if(!Company::where('code', $employeeData['code'])->exists()) {
            $bankAccounts = $employeeData['bank_accounts'];
            unset($employeeData['bank_accounts']);
            
            $employee = Company::create($employeeData);
            
            // 建立銀行帳號
            foreach($bankAccounts as $account) {
                CompanyBankAccount::create([
                    'company_id' => $employee->id,
                    'bank_name' => $account['bank_name'],
                    'branch_name' => $account['branch_name'],
                    'account_number' => $account['account_number'],
                    'is_default' => $account['is_default'],
                ]);
            }
            
            $status = $employee->is_active ? '在職' : '離職';
            echo "  ✓ 建立員工: {$employee->name} ({$employee->code}) - {$status}" . PHP_EOL;
        } else {
            echo "  - 員工已存在: {$employeeData['code']}" . PHP_EOL;
        }
    }
    
    echo PHP_EOL;
    echo "=== 統計資訊 ===" . PHP_EOL;
    echo "廠商總數: " . Company::where('is_outsource', true)->count() . " 筆" . PHP_EOL;
    echo "員工總數: " . Company::where('is_member', true)->count() . " 筆" . PHP_EOL;
    echo "  - 在職: " . Company::where('is_member', true)->where('is_active', true)->count() . " 筆" . PHP_EOL;
    echo "  - 離職: " . Company::where('is_member', true)->where('is_active', false)->count() . " 筆" . PHP_EOL;
    echo "銀行帳號總數: " . CompanyBankAccount::count() . " 筆" . PHP_EOL;
    echo PHP_EOL . "✅ 廠商與員工測試資料建立完成！" . PHP_EOL;
});
