<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\CompanyContact;
use App\Models\CompanyBankAccount;
use App\Models\User;
use App\Models\Project;
use App\Models\Receivable;
use App\Models\Payable;
use App\Models\Tag;
use App\Models\TenantSetting;
use Illuminate\Support\Facades\Hash;

/**
 * 大量測試資料 Seeder
 * 建立 3個月以上跨度的測試資料，涵蓋公司、用戶、專案、應收、應付帳款
 *
 * 執行方式（指定租戶）：
 *   php artisan tenants:run db:seed --option="class=BulkTestDataSeeder" --tenants=abc123
 */
class BulkTestDataSeeder extends Seeder
{
    /** 專案狀態 tag ID 對照 (執行期動態取得) */
    private array $statusIds = [];

    public function run(): void
    {
        $this->command->info('=== 開始建立大量測試資料 ===');

        $this->setupProjectStatusTags();
        $companies = $this->seedCompanies();
        $users     = $this->seedUsers();
        $projects  = $this->seedProjects($companies, $users);
        $this->seedReceivables($projects);
        $this->seedPayables($projects, $companies);
        $this->seedProjectRoleTags();

        $this->command->info('');
        $this->command->info('=== 完成 ===');
        $this->command->table(
            ['模組', '筆數'],
            [
                ['公司', Company::count()],
                ['用戶', User::count()],
                ['專案', Project::count()],
                ['應收帳款', Receivable::count()],
                ['應付帳款', Payable::count()],
                ['專案職務標籤', Tag::where('type', Tag::TYPE_PROJECT_ROLE)->count()],
            ]
        );
    }

    // ─────────────────────────────────────────────
    // 確保 7 個預設狀態標籤存在
    // ─────────────────────────────────────────────
    private function setupProjectStatusTags(): void
    {
        $statuses = [
            ['name' => '新成立', 'color' => '#94A3B8', 'sort_order' => 1],
            ['name' => '提案',   'color' => '#A855F7', 'sort_order' => 2],
            ['name' => '進行中', 'color' => '#3B82F6', 'sort_order' => 3],
            ['name' => '結案',   'color' => '#22C55E', 'sort_order' => 4],
            ['name' => '請款中', 'color' => '#F97316', 'sort_order' => 5],
            ['name' => '已入帳', 'color' => '#10B981', 'sort_order' => 6],
            ['name' => '待發票', 'color' => '#EAB308', 'sort_order' => 7],
        ];

        foreach ($statuses as $s) {
            $tag = Tag::updateOrCreate(
                ['type' => Tag::TYPE_PROJECT_STATUS, 'name' => $s['name']],
                ['color' => $s['color'], 'sort_order' => $s['sort_order'], 'is_active' => true]
            );
            $this->statusIds[$s['name']] = (string) $tag->id;
        }

        // 設定預設狀態為「請款中」
        TenantSetting::set('default_project_status', $this->statusIds['請款中'], 'project', 'string');

        $this->command->info('✓ 專案狀態標籤：' . count($this->statusIds) . ' 筆');
    }

    // ─────────────────────────────────────────────
    // 公司 (15 筆)
    // ─────────────────────────────────────────────
    private function seedCompanies(): \Illuminate\Support\Collection
    {
        $rows = [
            // 客戶
            ['code'=>'C001','name'=>'台積電股份有限公司','short_name'=>'台積電','is_client'=>true,'is_outsource'=>false,'phone'=>'03-578-0221','address'=>'新竹市科學園區力行六路8號','tax_id'=>'22099130'],
            ['code'=>'C002','name'=>'聯發科技股份有限公司','short_name'=>'聯發科','is_client'=>true,'is_outsource'=>false,'phone'=>'03-567-0888','address'=>'新竹市科學園區力行路1號','tax_id'=>'23456789'],
            ['code'=>'C003','name'=>'華碩電腦股份有限公司','short_name'=>'華碩','is_client'=>true,'is_outsource'=>false,'phone'=>'02-2894-3447','address'=>'台北市北投區立德路15號','tax_id'=>'34567890'],
            ['code'=>'C004','name'=>'鴻海精密工業股份有限公司','short_name'=>'鴻海','is_client'=>true,'is_outsource'=>false,'phone'=>'02-2268-3466','address'=>'新北市土城區自由街2號','tax_id'=>'22099131'],
            ['code'=>'C005','name'=>'台灣大哥大股份有限公司','short_name'=>'台灣大','is_client'=>true,'is_outsource'=>false,'phone'=>'02-6636-0000','address'=>'台北市信義區信義路5段100號','tax_id'=>'97176853'],
            ['code'=>'C006','name'=>'永豐金融控股股份有限公司','short_name'=>'永豐金','is_client'=>true,'is_outsource'=>false,'phone'=>'02-2312-6688','address'=>'台北市中正區羅斯福路2段29號','tax_id'=>'16644534'],
            ['code'=>'C007','name'=>'奇美電子股份有限公司','short_name'=>'奇美','is_client'=>true,'is_outsource'=>false,'phone'=>'06-505-5121','address'=>'台南市善化區鳳凰路1號','tax_id'=>'84149822'],
            ['code'=>'C008','name'=>'富邦金融控股股份有限公司','short_name'=>'富邦','is_client'=>true,'is_outsource'=>false,'phone'=>'02-2706-7890','address'=>'台北市信義區仁愛路4段151號','tax_id'=>'55317421'],
            ['code'=>'C009','name'=>'遠傳電信股份有限公司','short_name'=>'遠傳','is_client'=>true,'is_outsource'=>false,'phone'=>'02-2718-1888','address'=>'台北市內湖區瑞光路543號','tax_id'=>'97676464'],
            // 外製商/供應商
            ['code'=>'V001','name'=>'大聯大控股股份有限公司','short_name'=>'大聯大','is_client'=>false,'is_outsource'=>true,'phone'=>'02-2788-5200','address'=>'台北市內湖區','tax_id'=>'45678901'],
            ['code'=>'V002','name'=>'欣技資訊股份有限公司','short_name'=>'欣技','is_client'=>false,'is_outsource'=>true,'phone'=>'02-8797-8123','address'=>'台北市內湖區','tax_id'=>'56789012'],
            ['code'=>'V003','name'=>'偉聯科技有限公司','short_name'=>'偉聯','is_client'=>false,'is_outsource'=>true,'phone'=>'02-2999-1234','address'=>'新北市板橋區','tax_id'=>'28456789'],
            ['code'=>'V004','name'=>'翔龍資訊股份有限公司','short_name'=>'翔龍','is_client'=>false,'is_outsource'=>true,'phone'=>'04-2323-5678','address'=>'台中市西屯區','tax_id'=>'38901234'],
            ['code'=>'V005','name'=>'佳能資訊技術有限公司','short_name'=>'佳能','is_client'=>false,'is_outsource'=>true,'phone'=>'02-2601-9988','address'=>'台北市內湖區','tax_id'=>'45678912'],
            ['code'=>'V006','name'=>'智源科技股份有限公司','short_name'=>'智源','is_client'=>false,'is_outsource'=>true,'phone'=>'03-378-8899','address'=>'桃園市中壢區','tax_id'=>'56789013'],
        ];

        $contacts = [
            ['name' => '業務聯絡人一', 'phone' => '02-1234-5678', 'mobile' => '0912-345-678'],
            ['name' => '採購聯絡人',   'phone' => '02-8765-4321', 'mobile' => '0987-654-321'],
        ];

        foreach ($rows as $data) {
            if (Company::where('code', $data['code'])->exists()) continue;

            $company = Company::create(array_merge($data, ['type' => 'company', 'is_active' => true]));

            // 聯絡人
            foreach ($contacts as $i => $c) {
                CompanyContact::create(array_merge($c, [
                    'company_id' => $company->id,
                    'email'      => 'contact' . ($i + 1) . '@' . strtolower(preg_replace('/[^a-z]/i', '', $data['short_name'])) . '.com',
                    'sort_order' => $i + 1,
                ]));
            }

            // 銀行帳戶
            CompanyBankAccount::create([
                'company_id'       => $company->id,
                'bank_name'        => collect(['台灣銀行', '第一銀行', '合作金庫', '國泰世華'])->random(),
                'bank_branch'      => '總行',
                'bank_account'     => '0' . rand(10000000000, 99999999999),
                'bank_account_name'=> $data['name'],
            ]);
        }

        $this->command->info('✓ 公司：' . Company::count() . ' 筆');
        return Company::all();
    }

    // ─────────────────────────────────────────────
    // 用戶 (8 筆)
    // ─────────────────────────────────────────────
    private function seedUsers(): \Illuminate\Support\Collection
    {
        $rows = [
            ['name'=>'系統管理員','email'=>'admin@ecount.test','employee_no'=>'E001','position'=>'系統管理員','role'=>'admin'],
            ['name'=>'張經理',    'email'=>'manager@ecount.test','employee_no'=>'E002','position'=>'專案經理','role'=>'manager'],
            ['name'=>'王會計',    'email'=>'accountant@ecount.test','employee_no'=>'E003','position'=>'財務會計','role'=>'accountant'],
            ['name'=>'李工程師',  'email'=>'employee@ecount.test','employee_no'=>'E004','position'=>'軟體工程師','role'=>'employee'],
            ['name'=>'陳前端',    'email'=>'chen.frontend@ecount.test','employee_no'=>'E005','position'=>'前端工程師','role'=>'employee','hire_date'=>'2024-03-01'],
            ['name'=>'林設計師',  'email'=>'lin.designer@ecount.test','employee_no'=>'E006','position'=>'UI設計師','role'=>'employee','hire_date'=>'2024-05-15'],
            ['name'=>'黃後端',    'email'=>'huang.backend@ecount.test','employee_no'=>'E007','position'=>'後端工程師','role'=>'employee','hire_date'=>'2024-07-01'],
            ['name'=>'吳助理',    'email'=>'wu.assistant@ecount.test','employee_no'=>'E008','position'=>'專案助理','role'=>'employee','hire_date'=>'2024-09-01'],
        ];

        foreach ($rows as $ud) {
            $role = $ud['role'];
            unset($ud['role']);

            $user = User::updateOrCreate(
                ['email' => $ud['email']],
                array_merge($ud, [
                    'password'  => Hash::make('password'),
                    'is_active' => true,
                    'hire_date' => $ud['hire_date'] ?? '2023-01-01',
                ])
            );

            if (!$user->hasRole($role)) {
                $user->assignRole($role);
            }
        }

        $this->command->info('✓ 用戶：' . User::count() . ' 筆');
        return User::where('is_active', true)->get();
    }

    // ─────────────────────────────────────────────
    // 專案 (30 筆，跨 2024-11 ~ 2026-02)
    // ─────────────────────────────────────────────
    private function seedProjects(
        \Illuminate\Support\Collection $companies,
        \Illuminate\Support\Collection $users
    ): \Illuminate\Support\Collection {
        $clients  = $companies->where('is_client', true)->values();
        $userIds  = $users->pluck('id')->toArray();
        $managers = $users->whereIn('position', ['專案經理', '系統管理員'])->pluck('id')->toArray()
                    ?: $userIds;

        $s = $this->statusIds;
        $templates = [
            // 已結案 (2024-11 ~ 2025-06)
            ['name'=>'%s ERP系統導入專案',    'type'=>'系統整合',  'months_ago'=>14,'status'=>$s['已入帳'],'budget'=>1800000,'rcv_ratio'=>[0.3,0.4,0.3]],
            ['name'=>'%s 官網改版設計',        'type'=>'網頁設計',  'months_ago'=>12,'status'=>$s['已入帳'],'budget'=>350000, 'rcv_ratio'=>[0.5,0.5]],
            ['name'=>'%s 行動應用開發',        'type'=>'APP開發',   'months_ago'=>11,'status'=>$s['已入帳'],'budget'=>980000, 'rcv_ratio'=>[0.3,0.4,0.3]],
            ['name'=>'%s 數據分析平台',        'type'=>'數據工程',  'months_ago'=>10,'status'=>$s['已入帳'],'budget'=>650000, 'rcv_ratio'=>[0.4,0.6]],
            ['name'=>'%s 內部管理系統',        'type'=>'系統開發',  'months_ago'=>9, 'status'=>$s['已入帳'],'budget'=>750000, 'rcv_ratio'=>[0.3,0.3,0.4]],
            ['name'=>'%s UI/UX 設計專案',     'type'=>'設計',      'months_ago'=>9, 'status'=>$s['結案'],  'budget'=>280000, 'rcv_ratio'=>[0.5,0.5]],
            ['name'=>'%s API 整合開發',        'type'=>'後端開發',  'months_ago'=>8, 'status'=>$s['結案'],  'budget'=>420000, 'rcv_ratio'=>[0.4,0.6]],
            ['name'=>'%s 雲端遷移專案',        'type'=>'雲端服務',  'months_ago'=>7, 'status'=>$s['結案'],  'budget'=>560000, 'rcv_ratio'=>[0.3,0.4,0.3]],
            ['name'=>'%s 資安檢測服務',        'type'=>'資安',      'months_ago'=>6, 'status'=>$s['結案'],  'budget'=>320000, 'rcv_ratio'=>[0.5,0.5]],
            ['name'=>'%s 數位轉型顧問',        'type'=>'顧問服務',  'months_ago'=>6, 'status'=>$s['結案'],  'budget'=>890000, 'rcv_ratio'=>[0.2,0.3,0.5]],
            // 請款中 (2025-07 ~ 2025-11)
            ['name'=>'%s 電商平台建置',        'type'=>'電商',      'months_ago'=>5, 'status'=>$s['請款中'],'budget'=>1200000,'rcv_ratio'=>[0.3,0.4,0.3]],
            ['name'=>'%s BI 報表系統',         'type'=>'數據工程',  'months_ago'=>5, 'status'=>$s['請款中'],'budget'=>480000, 'rcv_ratio'=>[0.5,0.5]],
            ['name'=>'%s 客服系統升級',        'type'=>'系統開發',  'months_ago'=>4, 'status'=>$s['請款中'],'budget'=>350000, 'rcv_ratio'=>[0.4,0.6]],
            ['name'=>'%s 微服務架構重構',      'type'=>'後端開發',  'months_ago'=>4, 'status'=>$s['請款中'],'budget'=>720000, 'rcv_ratio'=>[0.3,0.3,0.4]],
            ['name'=>'%s 自動化測試建置',      'type'=>'QA工程',    'months_ago'=>4, 'status'=>$s['請款中'],'budget'=>280000, 'rcv_ratio'=>[0.5,0.5]],
            // 進行中 (2025-10 ~ 2025-12)
            ['name'=>'%s 資料湖泊建置',        'type'=>'數據工程',  'months_ago'=>3, 'status'=>$s['進行中'],'budget'=>950000, 'rcv_ratio'=>[0.3,0.7]],
            ['name'=>'%s 智慧客服導入',        'type'=>'AI應用',    'months_ago'=>3, 'status'=>$s['進行中'],'budget'=>680000, 'rcv_ratio'=>[0.5,0.5]],
            ['name'=>'%s DevOps 建置',         'type'=>'DevOps',    'months_ago'=>3, 'status'=>$s['進行中'],'budget'=>420000, 'rcv_ratio'=>[0.4,0.6]],
            ['name'=>'%s 線上教育平台',        'type'=>'電商',      'months_ago'=>2, 'status'=>$s['進行中'],'budget'=>1100000,'rcv_ratio'=>[0.3,0.7]],
            ['name'=>'%s 供應鏈管理系統',      'type'=>'系統整合',  'months_ago'=>2, 'status'=>$s['進行中'],'budget'=>880000, 'rcv_ratio'=>[0.4,0.6]],
            ['name'=>'%s 人資管理平台',        'type'=>'系統開發',  'months_ago'=>2, 'status'=>$s['進行中'],'budget'=>560000, 'rcv_ratio'=>[0.5,0.5]],
            // 待發票 (2025-12 ~ 2026-01)
            ['name'=>'%s 會員系統重建',        'type'=>'系統開發',  'months_ago'=>2, 'status'=>$s['待發票'],'budget'=>490000, 'rcv_ratio'=>[0.5,0.5]],
            ['name'=>'%s 雲端儲存方案',        'type'=>'雲端服務',  'months_ago'=>1, 'status'=>$s['待發票'],'budget'=>320000, 'rcv_ratio'=>[0.6,0.4]],
            ['name'=>'%s 資安合規稽核',        'type'=>'資安',      'months_ago'=>1, 'status'=>$s['待發票'],'budget'=>280000, 'rcv_ratio'=>[0.5,0.5]],
            ['name'=>'%s 多語系介面開發',      'type'=>'網頁設計',  'months_ago'=>1, 'status'=>$s['待發票'],'budget'=>350000, 'rcv_ratio'=>[0.5,0.5]],
            // 提案/新成立 (2026-01 ~ 2026-02)
            ['name'=>'%s 區塊鏈存證系統',      'type'=>'區塊鏈',    'months_ago'=>1, 'status'=>$s['提案'],  'budget'=>750000, 'rcv_ratio'=>[0.5,0.5]],
            ['name'=>'%s 物聯網監控平台',      'type'=>'IoT',       'months_ago'=>1, 'status'=>$s['提案'],  'budget'=>920000, 'rcv_ratio'=>[0.5,0.5]],
            ['name'=>'%s 數位行銷系統',        'type'=>'行銷科技',  'months_ago'=>0, 'status'=>$s['提案'],  'budget'=>430000, 'rcv_ratio'=>[0.5,0.5]],
            ['name'=>'%s 企業知識管理庫',      'type'=>'系統開發',  'months_ago'=>0, 'status'=>$s['新成立'],'budget'=>580000, 'rcv_ratio'=>[0.5,0.5]],
            ['name'=>'%s 自動化報表系統',      'type'=>'數據工程',  'months_ago'=>0, 'status'=>$s['新成立'],'budget'=>360000, 'rcv_ratio'=>[0.5,0.5]],
        ];

        // Auto-increment project code counter
        $codeSeq = (int) Project::max(\DB::raw("CAST(SUBSTRING(code, 2) AS UNSIGNED)"));

        $created = 0;
        foreach ($templates as $i => $tpl) {
            $company = $clients->get($i % $clients->count());
            $name    = sprintf($tpl['name'], $company->short_name ?? $company->name);

            if (Project::where('name', $name)->exists()) continue;

            $codeSeq++;
            $startDate = Carbon::now()->subMonths($tpl['months_ago'])->startOfMonth()->addDays(rand(0, 10));
            $isFinished = in_array($tpl['status'], [$s['結案'], $s['已入帳']]);
            $endDate    = $isFinished ? $startDate->copy()->addMonths(rand(2, 5)) : null;

            $project = Project::create([
                'code'         => 'P' . str_pad($codeSeq, 4, '0', STR_PAD_LEFT),
                'name'         => $name,
                'project_type' => $tpl['type'],
                'company_id'   => $company->id,
                'manager_id'   => $managers[array_rand($managers)],
                'status'       => $tpl['status'],
                'start_date'   => $startDate->toDateString(),
                'end_date'     => $endDate?->toDateString(),
                'budget'       => $tpl['budget'],
            ]);

            // 加入 1-2 名成員
            $memberIds = (array) array_rand(array_flip($userIds), min(2, count($userIds)));
            foreach ($memberIds as $uid) {
                if (!$project->members()->where('user_id', $uid)->exists()) {
                    $project->members()->attach($uid, ['joined_at' => $startDate->toDateTimeString()]);
                }
            }

            // 儲存 rcv_ratio 在 project 上供後續使用
            $project->_rcv_ratio = $tpl['rcv_ratio'];
            $created++;
        }

        $this->command->info("✓ 專案：" . Project::count() . " 筆（新建 {$created} 筆）");
        return Project::with('company')->get();
    }

    // ─────────────────────────────────────────────
    // 應收帳款
    // ─────────────────────────────────────────────
    private function seedReceivables(\Illuminate\Support\Collection $projects): void
    {
        $s = $this->statusIds;
        $contents = ['訂金', '第一期款', '第二期款', '第三期款', '尾款', '驗收款', '完工款'];

        $seq = Receivable::where('receipt_no', 'REGEXP', '^R[0-9]+$')->count();

        foreach ($projects as $project) {
            if (Receivable::where('project_id', $project->id)->count() >= 2) continue;

            $startDate = Carbon::parse($project->start_date);
            $ratio     = $project->_rcv_ratio ?? [0.5, 0.5];
            $isPaid    = in_array($project->status, [$s['已入帳']]);
            $isFinished = in_array($project->status, [$s['結案'], $s['已入帳'], $s['請款中'], $s['待發票']]);

            foreach ($ratio as $idx => $pct) {
                $seq++;
                $rcvDate = $startDate->copy()->addMonths($idx)->addDays(rand(5, 20));
                $amount  = (int) round($project->budget * $pct);
                $amtBefore = (int) round($amount / 1.05);
                $taxAmt  = $amount - $amtBefore;
                $dueDate = $rcvDate->copy()->addDays(30);

                $rcvStatus    = 'unpaid';
                $receivedAmt  = 0;
                $paidDate     = null;
                $paymentMethod = collect(['bank_transfer', 'check', 'cash'])->random();

                if ($isPaid) {
                    $rcvStatus   = 'paid';
                    $receivedAmt = $amount;
                    $paidDate    = $rcvDate->copy()->addDays(rand(5, 25))->toDateString();
                } elseif ($isFinished && $idx === 0) {
                    $rcvStatus   = 'paid';
                    $receivedAmt = $amount;
                    $paidDate    = $rcvDate->copy()->addDays(rand(5, 25))->toDateString();
                } elseif ($rcvDate->lt(Carbon::now()->subDays(30))) {
                    $rcvStatus = 'overdue';
                }

                Receivable::create([
                    'receipt_no'          => 'R' . str_pad($seq, 5, '0', STR_PAD_LEFT),
                    'project_id'          => $project->id,
                    'company_id'          => $project->company_id,
                    'responsible_user_id' => $project->manager_id,
                    'receipt_date'        => $rcvDate->toDateString(),
                    'fiscal_year'         => $rcvDate->year,
                    'due_date'            => $dueDate->toDateString(),
                    'amount'              => $amount,
                    'amount_before_tax'   => $amtBefore,
                    'tax_rate'            => 5,
                    'tax_amount'          => $taxAmt,
                    'received_amount'     => $receivedAmt,
                    'withholding_tax'     => 0,
                    'status'              => $rcvStatus,
                    'payment_method'      => $paymentMethod,
                    'paid_date'           => $paidDate,
                    'invoice_no'          => 'INV-' . $rcvDate->format('Ym') . '-' . str_pad($seq, 3, '0', STR_PAD_LEFT),
                    'content'             => $contents[$idx % count($contents)],
                ]);
            }
        }

        $this->command->info('✓ 應收帳款：' . Receivable::count() . ' 筆');
    }

    // ─────────────────────────────────────────────
    // 應付帳款
    // ─────────────────────────────────────────────
    private function seedPayables(
        \Illuminate\Support\Collection $projects,
        \Illuminate\Support\Collection $companies
    ): void {
        $s       = $this->statusIds;
        $vendors = $companies->where('is_outsource', true)->values();
        $contents = ['外包開發費用', '設計費用', '軟體授權費', '主機代管費', '顧問費', '測試費用'];
        $types    = ['outsource', 'expense', 'software'];

        $seq = Payable::where('payment_no', 'REGEXP', '^PAY[0-9]+$')->count();

        foreach ($projects as $i => $project) {
            if (Payable::where('project_id', $project->id)->count() >= 2) continue;

            $startDate  = Carbon::parse($project->start_date);
            $isPaid     = in_array($project->status, [$s['已入帳']]);
            $isFinished = in_array($project->status, [$s['結案'], $s['已入帳'], $s['請款中']]);
            $numPay     = rand(1, 2);

            for ($j = 0; $j < $numPay; $j++) {
                $seq++;
                $vendor  = $vendors->get(($i + $j) % $vendors->count());
                $payDate = $startDate->copy()->addMonths($j)->addDays(rand(10, 25));
                $dueDate = $payDate->copy()->addDays(30);
                $amount  = (int) round($project->budget * [0.15, 0.10][$j] ?? 0.12);

                $payStatus = 'unpaid';
                $paidAmt   = 0;
                $paidDate  = null;

                if ($isPaid || ($isFinished && $j === 0)) {
                    $payStatus = 'paid';
                    $paidAmt   = $amount;
                    $paidDate  = $payDate->copy()->addDays(rand(5, 20))->toDateString();
                } elseif ($payDate->lt(Carbon::now()->subDays(30))) {
                    $payStatus = 'overdue';
                }

                Payable::create([
                    'payment_no'          => 'PAY' . str_pad($seq, 5, '0', STR_PAD_LEFT),
                    'type'                => $types[$j % 3],
                    'project_id'          => $project->id,
                    'company_id'          => $project->company_id,
                    'payee_type'          => 'company',
                    'payee_company_id'    => $vendor->id,
                    'responsible_user_id' => $project->manager_id,
                    'payment_date'        => $payDate->toDateString(),
                    'fiscal_year'         => $payDate->year,
                    'due_date'            => $dueDate->toDateString(),
                    'amount'              => $amount,
                    'deduction'           => 0,
                    'paid_amount'         => $paidAmt,
                    'status'              => $payStatus,
                    'payment_method'      => 'bank_transfer',
                    'paid_date'           => $paidDate,
                    'invoice_no'          => 'VINV-' . $payDate->format('Ym') . '-' . str_pad($seq, 3, '0', STR_PAD_LEFT),
                    'content'             => $contents[$j % count($contents)],
                ]);
            }
        }

        $this->command->info('✓ 應付帳款：' . Payable::count() . ' 筆');
    }

    // ─────────────────────────────────────────────
    // 專案職務標籤 (project_role)
    // ─────────────────────────────────────────────
    private function seedProjectRoleTags(): void
    {
        $roles = [
            ['name' => '前端工程師',   'color' => '#3B82F6', 'sort_order' => 1],
            ['name' => '後端工程師',   'color' => '#8B5CF6', 'sort_order' => 2],
            ['name' => '全端工程師',   'color' => '#6366F1', 'sort_order' => 3],
            ['name' => 'UI 設計師',    'color' => '#EC4899', 'sort_order' => 4],
            ['name' => 'UX 研究員',    'color' => '#F43F5E', 'sort_order' => 5],
            ['name' => '專案經理',     'color' => '#F97316', 'sort_order' => 6],
            ['name' => 'QA 工程師',    'color' => '#EAB308', 'sort_order' => 7],
            ['name' => 'DevOps 工程師','color' => '#22C55E', 'sort_order' => 8],
            ['name' => '資料庫管理員', 'color' => '#14B8A6', 'sort_order' => 9],
            ['name' => '系統分析師',   'color' => '#0EA5E9', 'sort_order' => 10],
            ['name' => '技術顧問',     'color' => '#94A3B8', 'sort_order' => 11],
        ];

        foreach ($roles as $r) {
            Tag::firstOrCreate(
                ['type' => Tag::TYPE_PROJECT_ROLE, 'name' => $r['name']],
                ['color' => $r['color'], 'sort_order' => $r['sort_order'], 'is_active' => true]
            );
        }

        $this->command->info('✓ 專案職務標籤：' . Tag::where('type', Tag::TYPE_PROJECT_ROLE)->count() . ' 筆');
    }
}
