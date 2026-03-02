<?php
/**
 * 匯入應付帳款測試資料
 * 執行: php seed-payables-data.php
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$tenant = App\Models\Tenant::find('abc123');
if (!$tenant) {
    die("❌ 租戶 abc123 不存在\n");
}

tenancy()->initialize($tenant);
echo "✅ 已切換到租戶: {$tenant->id}\n\n";

use App\Models\Company;
use App\Models\Project;
use App\Models\User;
use App\Models\Payable;
use Illuminate\Support\Facades\DB;

// ─── 1. 建立公司 ───────────────────────────────────────────────────────────────
echo "📦 建立公司資料...\n";

$mmr = Company::firstOrCreate(
    ['name' => 'MMR Co.'],
    ['short_name' => 'MMR', 'is_outsource' => true, 'code' => 'MMR001']
);
echo "  MMR Co. → #{$mmr->id}\n";

$gandi = Company::firstOrCreate(
    ['name' => 'Gandi'],
    ['short_name' => 'Gandi', 'is_outsource' => true, 'code' => 'GND001']
);
echo "  Gandi → #{$gandi->id}\n";

$sikao = Company::firstOrCreate(
    ['name' => '仕高利達'],
    ['short_name' => '仕高利達', 'is_outsource' => false, 'code' => 'SKD001']
);
echo "  仕高利達 → #{$sikao->id}\n";

$haodonka = Company::firstOrCreate(
    ['name' => '好動咖'],
    ['short_name' => '好動咖', 'is_outsource' => false, 'code' => 'HDK001']
);
echo "  好動咖 → #{$haodonka->id}\n";

// ─── 2. 建立使用者 ─────────────────────────────────────────────────────────────
echo "\n👤 建立使用者...\n";

$makeUser = function (string $name, string $email) {
    return User::firstOrCreate(
        ['email' => $email],
        ['name' => $name, 'password' => bcrypt('password')]
    );
};

$reira   = $makeUser('Reira',    'reira@example.com');
$xiaohao = $makeUser('小夯',     'xiaohao@example.com');
$saka    = $makeUser('Saka',     'saka@example.com');
$damo    = $makeUser('戴摩',     'damo@example.com');
$eunice  = $makeUser('Eunice怡如','eunice@example.com');
$hubert  = $makeUser('Hubert',   'hubert@example.com');
// 阿百 (advance user for row 4) - use Hubert's colleague or Reira
$abai    = $makeUser('阿百',     'abai@example.com');

foreach ([$reira, $xiaohao, $saka, $damo, $eunice, $hubert, $abai] as $u) {
    echo "  {$u->name} → #{$u->id}\n";
}

// ─── 3. 建立專案 ───────────────────────────────────────────────────────────────
echo "\n📁 建立專案...\n";

$projSikao = Project::firstOrCreate(
    ['name' => '仕高利達全球官網建置'],
    ['company_id' => $sikao->id, 'status' => 'in_progress', 'code' => 'SKD-2026-01']
);
echo "  仕高利達全球官網建置 → #{$projSikao->id}\n";

$projHaodonka = Project::firstOrCreate(
    ['name' => '好動咖'],
    ['company_id' => $haodonka->id, 'status' => 'in_progress', 'code' => 'HDK-2026-01']
);
echo "  好動咖 → #{$projHaodonka->id}\n";

// ─── 4. 清除舊的測試應付資料（只刪除此專案下的資料）─────────────────────────
echo "\n🗑  清除舊資料 (project_id in [{$projSikao->id}, {$projHaodonka->id}])...\n";
Payable::whereIn('project_id', [$projSikao->id, $projHaodonka->id])->delete();

// ─── 5. 匯入應付資料 ───────────────────────────────────────────────────────────
echo "\n💰 匯入應付資料...\n";

// Generate payment_no helper
$nextNo = function () {
    static $counter = null;
    if ($counter === null) {
        $last = Payable::withTrashed()
            ->where('payment_no', 'like', 'PAY-%')
            ->orderByRaw('CAST(SUBSTRING(payment_no, 5) AS UNSIGNED) DESC')
            ->first();
        $counter = $last ? (intval(substr($last->payment_no, 4)) + 1) : 1;
    }
    return 'PAY-' . str_pad($counter++, 3, '0', STR_PAD_LEFT);
};

$rows = [
    // ── 待付 ────────────────────────────────────────────────────────────────────
    [
        'content'           => '仕高利達全球官網建置',
        'payee_type'        => 'expense',
        'expense_company_name' => '戴摩',
        'project_id'        => $projSikao->id,
        'company_id'        => $sikao->id,
        'responsible_user_id' => $reira->id,
        'due_date'          => '2026-02-24',
        'amount'            => 84000,
        'paid_amount'       => 0,
        'status'            => 'unpaid',
        'fiscal_year'       => 2026,
        'type'              => 'expense',
    ],
    [
        'content'           => '官網維護 2026',
        'payee_type'        => 'vendor',
        'payee_company_id'  => $mmr->id,
        'project_id'        => $projSikao->id,
        'company_id'        => $sikao->id,
        'responsible_user_id' => $xiaohao->id,
        'due_date'          => '2026-02-24',
        'amount'            => 2100,
        'paid_amount'       => 0,
        'status'            => 'unpaid',
        'fiscal_year'       => 2026,
        'type'              => 'expense',
    ],
    // ── 已付 ────────────────────────────────────────────────────────────────────
    [
        'content'           => '影片串動與修改',
        'payee_type'        => 'expense',
        'expense_company_name' => 'Eunice怡如',
        'project_id'        => $projSikao->id,
        'company_id'        => $sikao->id,
        'responsible_user_id' => $reira->id,
        'due_date'          => '2026-02-24',
        'amount'            => 25700,
        'paid_amount'       => 25700,
        'status'            => 'paid',
        'paid_date'         => '2026-02-04',
        'fiscal_year'       => 2026,
        'type'              => 'expense',
    ],
    [
        'content'           => 'IT部份網通產品拍攝',
        'payee_type'        => 'member',
        'payee_user_id'     => $hubert->id,
        'advance_user_id'   => $abai->id,
        'project_id'        => $projSikao->id,
        'company_id'        => $sikao->id,
        'responsible_user_id' => $reira->id,
        'due_date'          => '2026-02-01',
        'amount'            => 3349,
        'paid_amount'       => 3349,
        'status'            => 'paid',
        'paid_date'         => '2026-02-04',
        'note'              => '阿百代墊',
        'fiscal_year'       => 2026,
        'type'              => 'expense',
    ],
    [
        'content'           => '網域一年',
        'payee_type'        => 'expense',
        'expense_company_name' => 'Gandi',
        'advance_user_id'   => $saka->id,
        'project_id'        => $projSikao->id,
        'company_id'        => $sikao->id,
        'responsible_user_id' => $saka->id,
        'due_date'          => '2026-02-23',
        'amount'            => 840,
        'paid_amount'       => 840,
        'status'            => 'paid',
        'paid_date'         => '2026-02-01',
        'note'              => 'Saka代墊(結清）',
        'payment_method'    => '匯款',
        'fiscal_year'       => 2026,
        'type'              => 'expense',
    ],
    // ── 歷史已付 (好動咖專案) ─────────────────────────────────────────────────
    [
        'content'           => '設計',
        'payee_type'        => 'expense',
        'expense_company_name' => '好動咖設計師',
        'project_id'        => $projHaodonka->id,
        'company_id'        => $haodonka->id,
        'responsible_user_id' => $reira->id,
        'due_date'          => '2026-01-15',
        'amount'            => 12000,
        'paid_amount'       => 12000,
        'status'            => 'paid',
        'paid_date'         => '2026-01-20',
        'fiscal_year'       => 2026,
        'type'              => 'expense',
    ],
    // ── 歷史已付 (仕高利達專案) ──────────────────────────────────────────────
    [
        'content'           => '金魚腦影片連結icon圖示',
        'payee_type'        => 'expense',
        'expense_company_name' => '設計外包商',
        'project_id'        => $projSikao->id,
        'company_id'        => $sikao->id,
        'responsible_user_id' => $reira->id,
        'due_date'          => '2026-01-10',
        'amount'            => 8500,
        'paid_amount'       => 8500,
        'status'            => 'paid',
        'paid_date'         => '2026-01-15',
        'fiscal_year'       => 2026,
        'type'              => 'expense',
    ],
];

foreach ($rows as $i => $row) {
    $row['payment_no'] = $nextNo();
    // payment_date defaults to due_date if not set
    if (empty($row['payment_date'])) {
        $row['payment_date'] = $row['due_date'];
    }
    $payable = Payable::create($row);
    echo "  [{$payable->status}] {$row['content']} NT\${$row['amount']} #{$payable->payment_no} → id:{$payable->id}\n";
}

echo "\n✅ 完成！共匯入 " . count($rows) . " 筆應付資料。\n";
echo "🔗 查看: https://abc123.ecount.test/payables\n";
