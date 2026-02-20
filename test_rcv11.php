<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

tenancy()->initialize('abc123');

echo "=== 應收帳款 ID 11 詳細資料 ===\n\n";

$receivable = App\Models\Receivable::with(['company', 'project', 'responsibleUser', 'payments'])->find(11);

if (!$receivable) {
    echo "找不到應收帳款 ID 11\n";
    exit;
}

echo "【基本資料】\n";
echo "單號: {$receivable->receipt_no}\n";
echo "客戶: " . ($receivable->company->name ?? '-') . "\n";
echo "專案: " . ($receivable->project ? $receivable->project->code . ' - ' . $receivable->project->name : '-') . "\n";
echo "負責人: " . ($receivable->responsibleUser->name ?? '-') . "\n";
echo "收款日期: " . ($receivable->receipt_date ? $receivable->receipt_date->format('Y-m-d') : '-') . "\n";
echo "到期日: " . ($receivable->due_date ? $receivable->due_date->format('Y-m-d') : '-') . "\n";

echo "\n【金額資訊】\n";
echo "稅前金額: NT$ " . number_format($receivable->amount_before_tax ?? 0) . "\n";
echo "稅額: NT$ " . number_format($receivable->tax_amount ?? 0) . "\n";
echo "總金額: NT$ " . number_format($receivable->amount) . "\n";
echo "已收金額: NT$ " . number_format($receivable->received_amount ?? 0) . "\n";
echo "未收金額: NT$ " . number_format($receivable->amount - ($receivable->received_amount ?? 0)) . "\n";

echo "\n【其他資訊】\n";
echo "狀態: {$receivable->status}\n";
echo "發票號碼: " . ($receivable->invoice_no ?? '-') . "\n";
echo "內容: " . ($receivable->content ?? '-') . "\n";
echo "備註: " . ($receivable->note ?? '-') . "\n";
echo "帳務年度: " . ($receivable->fiscal_year ?? '-') . "\n";

echo "\n【入帳記錄】(" . $receivable->payments->count() . "筆)\n";
if ($receivable->payments->count() > 0) {
    foreach ($receivable->payments as $payment) {
        echo "  - " . $payment->payment_date->format('Y-m-d') . " | NT$ " . number_format($payment->amount) . " | " . ($payment->payment_method ?? '-') . "\n";
    }
} else {
    echo "  (無入帳記錄)\n";
}

echo "\n=== 測試編輯功能 ===\n";
$originalNote = $receivable->note;
$receivable->update([
    'note' => '測試編輯功能 - ' . date('Y-m-d H:i:s')
]);
$receivable->refresh();

echo "✅ 修改成功\n";
echo "原備註: " . ($originalNote ?? '(空)') . "\n";
echo "新備註: {$receivable->note}\n";

// 恢復原備註
$receivable->update(['note' => $originalNote]);
echo "✅ 已恢復原備註\n";
