<?php
/**
 * 大量測試資料建立腳本
 * 執行: php artisan tinker --execute="require 'create-bulk-test-data.php';"
 * 或: php create-bulk-test-data.php (需在 Laravel 環境)
 */

use App\Models\Company;
use App\Models\CompanyContact;
use App\Models\CompanyBankAccount;
use App\Models\User;
use App\Models\Project;
use App\Models\Receivable;
use App\Models\Payable;
use App\Models\Tag;
use Carbon\Carbon;

tenancy()->initialize(\App\Models\Tenant::find('abc123'));

echo "=== 開始建立大量測試資料 ===\n";

// ─────────────────────────────────────────────
// 1. 公司 (補充至 15 筆)
// ─────────────────────────────────────────────
$newCompanies = [
    // 客戶
    ['code'=>'C006','name'=>'鴻海精密工業股份有限公司','short_name'=>'鴻海','type'=>'company','is_client'=>true,'is_outsource'=>false,'phone'=>'02-2268-3466','address'=>'新北市土城區自由街2號','tax_id'=>'22099131','is_active'=>true],
    ['code'=>'C007','name'=>'台灣大哥大股份有限公司','short_name'=>'台灣大','type'=>'company','is_client'=>true,'is_outsource'=>false,'phone'=>'02-6636-0000','address'=>'台北市信義區信義路5段100號','tax_id'=>'97176853','is_active'=>true],
    ['code'=>'C008','name'=>'永豐金融控股股份有限公司','short_name'=>'永豐金','type'=>'company','is_client'=>true,'is_outsource'=>false,'phone'=>'02-2312-6688','address'=>'台北市中正區羅斯福路2段29號','tax_id'=>'16644534','is_active'=>true],
    ['code'=>'C009','name'=>'奇美電子股份有限公司','short_name'=>'奇美','type'=>'company','is_client'=>true,'is_outsource'=>false,'phone'=>'06-505-5121','address'=>'台南市善化區鳳凰路1號','tax_id'=>'84149822','is_active'=>true],
    ['code'=>'C010','name'=>'富邦金融控股股份有限公司','short_name'=>'富邦','type'=>'company','is_client'=>true,'is_outsource'=>false,'phone'=>'02-2706-7890','address'=>'台北市信義區仁愛路4段151號','tax_id'=>'55317421','is_active'=>true],
    ['code'=>'C011','name'=>'遠傳電信股份有限公司','short_name'=>'遠傳','type'=>'company','is_client'=>true,'is_outsource'=>false,'phone'=>'02-2718-1888','address'=>'台北市內湖區瑞光路543號','tax_id'=>'97676464','is_active'=>true],
    // 供應商
    ['code'=>'V003','name'=>'偉聯科技有限公司','short_name'=>'偉聯','type'=>'company','is_client'=>false,'is_outsource'=>true,'phone'=>'02-2999-1234','address'=>'新北市板橋區南雅南路2段47號','tax_id'=>'28456789','is_active'=>true],
    ['code'=>'V004','name'=>'翔龍資訊股份有限公司','short_name'=>'翔龍','type'=>'company','is_client'=>false,'is_outsource'=>true,'phone'=>'04-2323-5678','address'=>'台中市西屯區工業區28路18號','tax_id'=>'38901234','is_active'=>true],
    ['code'=>'V005','name'=>'佳能資訊技術有限公司','short_name'=>'佳能','type'=>'company','is_client'=>false,'is_outsource'=>true,'phone'=>'02-2601-9988','address'=>'台北市內湖區內湖路1段94號','tax_id'=>'45678912','is_active'=>true],
    ['code'=>'V006','name'=>'智源科技股份有限公司','short_name'=>'智源','type'=>'company','is_client'=>false,'is_outsource'=>true,'phone'=>'03-378-8899','address'=>'桃園市中壢區中山路235號','tax_id'=>'56789013','is_active'=>true],
];

foreach ($newCompanies as $data) {
    if (!Company::where('code', $data['code'])->exists()) {
        $company = Company::create($data);
        // Add contact
        CompanyContact::create([
            'company_id' => $company->id,
            'name' => '業務聯絡人',
            'phone' => $data['phone'],
            'email' => strtolower(str_replace(['股份有限公司','有限公司'], '', $data['short_name'])) . '@example.com',
        ]);
        // Add bank account
        CompanyBankAccount::create([
            'company_id' => $company->id,
            'bank_name' => '台灣銀行',
            'bank_branch' => '總行',
            'bank_account' => '0' . rand(10000000000, 99999999999),
            'bank_account_name' => $data['name'],
        ]);
    }
}
echo "公司: " . Company::count() . " 筆\n";

// ─────────────────────────────────────────────
// 2. 新增用戶 (補充至 8 筆)
// ─────────────────────────────────────────────
$newUsers = [
    ['name'=>'李大明','email'=>'li.daming@ecount.test','position'=>'前端工程師','employee_no'=>'E005','hire_date'=>'2024-03-01'],
    ['name'=>'陳美玲','email'=>'chen.meiling@ecount.test','position'=>'UI設計師','employee_no'=>'E006','hire_date'=>'2024-05-15'],
    ['name'=>'王志遠','email'=>'wang.zhiyuan@ecount.test','position'=>'後端工程師','employee_no'=>'E007','hire_date'=>'2024-07-01'],
    ['name'=>'林佳慧','email'=>'lin.jiahui@ecount.test','position'=>'專案助理','employee_no'=>'E008','hire_date'=>'2024-09-01'],
];

foreach ($newUsers as $ud) {
    if (!User::where('email', $ud['email'])->exists()) {
        $user = User::create(array_merge($ud, [
            'password' => bcrypt('password'),
            'is_active' => true,
        ]));
        $user->assignRole('employee');
    }
}
$users = User::where('is_active', true)->get();
echo "用戶: " . $users->count() . " 筆\n";

// ─────────────────────────────────────────────
// 3. 取得狀態 tag IDs
// ─────────────────────────────────────────────
$statusTags = Tag::where('type', Tag::TYPE_PROJECT_STATUS)->pluck('id', 'name');
$statusPlanning   = (string)($statusTags['規劃中'] ?? 1);
$statusInProgress = (string)($statusTags['進行中'] ?? 2);
$statusOnHold     = (string)($statusTags['暫停'] ?? 3);
$statusCompleted  = (string)($statusTags['已完成'] ?? 4);
$statusCancelled  = (string)($statusTags['已取消'] ?? 5);

// ─────────────────────────────────────────────
// 4. 專案 (建立至 30 筆)
// ─────────────────────────────────────────────
$companies = Company::where('type', 'client')->get();
$allUsers  = User::where('is_active', true)->get();

$projectTemplates = [
    ['name_tpl'=>'%s ERP系統導入','type'=>'系統整合','months_ago'=>14,'status_key'=>$statusCompleted,'budget'=>1800000],
    ['name_tpl'=>'%s 官網改版','type'=>'網頁設計','months_ago'=>12,'status_key'=>$statusCompleted,'budget'=>350000],
    ['name_tpl'=>'%s 行動應用開發','type'=>'APP開發','months_ago'=>10,'status_key'=>$statusCompleted,'budget'=>980000],
    ['name_tpl'=>'%s 數據分析平台','type'=>'數據工程','months_ago'=>8,'status_key'=>$statusCompleted,'budget'=>650000],
    ['name_tpl'=>'%s 內部管理系統','type'=>'系統開發','months_ago'=>6,'status_key'=>$statusInProgress,'budget'=>750000],
    ['name_tpl'=>'%s UI/UX 重新設計','type'=>'設計','months_ago'=>5,'status_key'=>$statusInProgress,'budget'=>280000],
    ['name_tpl'=>'%s API整合開發','type'=>'後端開發','months_ago'=>4,'status_key'=>$statusInProgress,'budget'=>420000],
    ['name_tpl'=>'%s 雲端遷移專案','type'=>'雲端服務','months_ago'=>4,'status_key'=>$statusInProgress,'budget'=>560000],
    ['name_tpl'=>'%s 資安檢測服務','type'=>'資安','months_ago'=>3,'status_key'=>$statusInProgress,'budget'=>320000],
    ['name_tpl'=>'%s 數位轉型顧問','type'=>'顧問服務','months_ago'=>3,'status_key'=>$statusInProgress,'budget'=>890000],
    ['name_tpl'=>'%s 電商平台建置','type'=>'電商','months_ago'=>2,'status_key'=>$statusInProgress,'budget'=>1200000],
    ['name_tpl'=>'%s BI報表系統','type'=>'數據工程','months_ago'=>2,'status_key'=>$statusPlanning,'budget'=>480000],
    ['name_tpl'=>'%s 客服系統升級','type'=>'系統開發','months_ago'=>1,'status_key'=>$statusPlanning,'budget'=>350000],
    ['name_tpl'=>'%s 微服務架構重構','type'=>'後端開發','months_ago'=>1,'status_key'=>$statusPlanning,'budget'=>720000],
    ['name_tpl'=>'%s 自動化測試建置','type'=>'QA工程','months_ago'=>0,'status_key'=>$statusPlanning,'budget'=>280000],
];

$projectCodes = [];
$projectCount = Project::count();
$companyIdx = 0;
$userIds = $allUsers->pluck('id')->toArray();

foreach ($projectTemplates as $pt) {
    $company = $companies[$companyIdx % $companies->count()];
    $companyIdx++;
    $projectName = sprintf($pt['name_tpl'], $company->short_name ?? $company->name);

    if (Project::where('name', $projectName)->exists()) continue;

    $startDate = Carbon::now()->subMonths($pt['months_ago'])->startOfMonth();
    $endDate   = $pt['status_key'] === $statusCompleted
        ? $startDate->copy()->addMonths(rand(2, 4))
        : null;

    $projectCount++;
    $code = 'P' . str_pad($projectCount, 4, '0', STR_PAD_LEFT);

    $project = Project::create([
        'code'         => $code,
        'name'         => $projectName,
        'project_type' => $pt['type'],
        'company_id'   => $company->id,
        'manager_id'   => $userIds[array_rand($userIds)],
        'status'       => $pt['status_key'],
        'start_date'   => $startDate->format('Y-m-d'),
        'end_date'     => $endDate?->format('Y-m-d'),
        'budget'       => $pt['budget'],
    ]);
    $projectCodes[] = $project->id;

    // Add 1-2 members
    $memberIds = array_rand(array_flip($userIds), min(2, count($userIds)));
    if (!is_array($memberIds)) $memberIds = [$memberIds];
    foreach ($memberIds as $mid) {
        if (!$project->members()->where('user_id', $mid)->exists()) {
            $project->members()->attach($mid, ['joined_at' => $startDate->format('Y-m-d H:i:s')]);
        }
    }
}
$allProjects = Project::all();
echo "專案: " . $allProjects->count() . " 筆\n";

// ─────────────────────────────────────────────
// 5. 應收帳款 (每個專案 2-3 筆)
// ─────────────────────────────────────────────
$receiptCount = Receivable::max('id') ?? 0;
$rcvStatuses  = ['unpaid', 'partial', 'paid', 'overdue'];

foreach ($allProjects as $project) {
    $existingRcv = Receivable::where('project_id', $project->id)->count();
    if ($existingRcv >= 2) continue;

    $numRcv = rand(2, 3);
    $startDate = Carbon::parse($project->start_date ?? now()->subMonths(3));

    for ($i = 0; $i < $numRcv; $i++) {
        $receiptCount++;
        $rcvDate    = $startDate->copy()->addMonths($i)->addDays(rand(5, 20));
        $dueDate    = $rcvDate->copy()->addDays(30);
        $amount     = round($project->budget * [0.3, 0.4, 0.3][$i] / 1.05 * 1.05);
        $amtBeforeTax = round($amount / 1.05);
        $taxAmt     = $amount - $amtBeforeTax;
        $isPaid     = $project->status === $statusCompleted;
        $rcvStatus  = $isPaid ? 'paid' : ($rcvDate < now()->subDays(30) ? 'overdue' : 'unpaid');
        $receivedAmt = $isPaid ? $amount : ($rcvStatus === 'overdue' ? 0 : 0);

        Receivable::create([
            'receipt_no'        => 'R' . str_pad($receiptCount, 5, '0', STR_PAD_LEFT),
            'project_id'        => $project->id,
            'company_id'        => $project->company_id,
            'responsible_user_id' => $project->manager_id,
            'receipt_date'      => $rcvDate->format('Y-m-d'),
            'fiscal_year'       => $rcvDate->year,
            'due_date'          => $dueDate->format('Y-m-d'),
            'amount'            => $amount,
            'amount_before_tax' => $amtBeforeTax,
            'tax_rate'          => 5,
            'tax_amount'        => $taxAmt,
            'received_amount'   => $receivedAmt,
            'withholding_tax'   => 0,
            'status'            => $rcvStatus,
            'payment_method'    => ['bank_transfer','check','cash'][rand(0,2)],
            'paid_date'         => $isPaid ? $rcvDate->copy()->addDays(rand(5,25))->format('Y-m-d') : null,
            'invoice_no'        => 'INV-' . date('Ym', $rcvDate->timestamp) . '-' . str_pad($receiptCount, 3, '0', STR_PAD_LEFT),
            'content'           => ['第一期款', '第二期款', '尾款', '訂金', '驗收款'][$i % 5],
            'note'              => null,
        ]);
    }
}
echo "應收帳款: " . Receivable::count() . " 筆\n";

// ─────────────────────────────────────────────
// 6. 應付帳款 (每個專案 1-2 筆外包費用)
// ─────────────────────────────────────────────
$payCount  = Payable::max('id') ?? 0;
$vendors   = Company::where('type', 'vendor')->get();
$payTypes  = ['outsource', 'expense', 'software'];

foreach ($allProjects as $project) {
    $existingPay = Payable::where('project_id', $project->id)->count();
    if ($existingPay >= 2) continue;

    $numPay    = rand(1, 2);
    $startDate = Carbon::parse($project->start_date ?? now()->subMonths(3));

    for ($i = 0; $i < $numPay; $i++) {
        $payCount++;
        $vendor   = $vendors[$payCount % $vendors->count()];
        $payDate  = $startDate->copy()->addMonths($i)->addDays(rand(10, 25));
        $dueDate  = $payDate->copy()->addDays(30);
        $amount   = round($project->budget * [0.15, 0.10][$i] ?? 0.12);
        $isPaid   = $project->status === $statusCompleted;
        $payStatus = $isPaid ? 'paid' : ($payDate < now()->subDays(30) ? 'overdue' : 'unpaid');

        Payable::create([
            'payment_no'        => 'PAY' . str_pad($payCount, 5, '0', STR_PAD_LEFT),
            'type'              => $payTypes[$i % 3],
            'project_id'        => $project->id,
            'company_id'        => $project->company_id,
            'payee_type'        => 'company',
            'payee_company_id'  => $vendor->id,
            'responsible_user_id' => $project->manager_id,
            'payment_date'      => $payDate->format('Y-m-d'),
            'fiscal_year'       => $payDate->year,
            'due_date'          => $dueDate->format('Y-m-d'),
            'amount'            => $amount,
            'deduction'         => 0,
            'paid_amount'       => $isPaid ? $amount : 0,
            'status'            => $payStatus,
            'payment_method'    => 'bank_transfer',
            'paid_date'         => $isPaid ? $payDate->copy()->addDays(rand(5,20))->format('Y-m-d') : null,
            'invoice_no'        => 'VINV-' . date('Ym', $payDate->timestamp) . '-' . str_pad($payCount, 3, '0', STR_PAD_LEFT),
            'content'           => ['外包開發費用', '設計費用', '軟體授權費', '主機費用', '顧問費'][$i % 5],
            'note'              => null,
        ]);
    }
}
echo "應付帳款: " . Payable::count() . " 筆\n";

// ─────────────────────────────────────────────
// 7. 專案職務標籤
// ─────────────────────────────────────────────
$roles = ['前端工程師','後端工程師','全端工程師','UI設計師','UX研究員','專案經理','QA工程師','DevOps工程師','資料庫管理員','系統分析師'];
foreach ($roles as $i => $roleName) {
    Tag::firstOrCreate(
        ['type' => Tag::TYPE_PROJECT_ROLE, 'name' => $roleName],
        ['color' => '#6B7280', 'sort_order' => $i + 1, 'is_active' => true]
    );
}
echo "職務標籤: " . Tag::where('type', Tag::TYPE_PROJECT_ROLE)->count() . " 筆\n";

echo "\n=== 完成 ===\n";
echo "公司: "   . Company::count()    . "\n";
echo "用戶: "   . User::count()       . "\n";
echo "專案: "   . Project::count()    . "\n";
echo "應收: "   . Receivable::count() . "\n";
echo "應付: "   . Payable::count()    . "\n";
