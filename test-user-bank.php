<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Tenant;
use App\Models\User;

$tenant = Tenant::find('abc123');
tenancy()->initialize($tenant);

// 測試新增銀行帳戶
$user = User::find(8);
if (!$user) {
    $user = User::first();
    echo '使用第一個使用者測試，ID: ' . $user->id . "\n";
}

echo "使用者: {$user->name}\n";
echo "現有銀行帳戶數: " . $user->bankAccounts->count() . "\n\n";

// 新增測試銀行帳戶
$account1 = $user->bankAccounts()->create([
    'bank_name' => '台灣銀行',
    'bank_branch' => '信義分行',
    'bank_account' => '123-456-789',
    'account_name' => $user->name,
    'is_default' => true,
    'note' => '薪資帳戶',
]);
echo "✅ 新增銀行帳戶 1: {$account1->bank_name} - {$account1->bank_account}\n";

$account2 = $user->bankAccounts()->create([
    'bank_name' => '國泰世華銀行',
    'bank_branch' => '敦南分行',
    'bank_account' => '987-654-321',
    'account_name' => $user->name,
    'is_default' => false,
]);
echo "✅ 新增銀行帳戶 2: {$account2->bank_name} - {$account2->bank_account}\n\n";

// 顯示所有銀行帳戶
$accounts = $user->bankAccounts()->get();
echo "所有銀行帳戶:\n";
foreach ($accounts as $acc) {
    echo "  - {$acc->bank_name} - {$acc->bank_account}";
    if ($acc->is_default) echo ' [預設]';
    echo "\n";
}

echo "\n✅ 測試完成！請訪問 https://abc123.ecount.test/users/{$user->id}/edit\n";
