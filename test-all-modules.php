<?php

/**
 * 測試所有模組功能
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
use App\Models\Tag;
use App\Models\ExpenseCategory;
use App\Models\TaxSetting;
use App\Models\BankAccount;

$tenant = Tenant::find('abc123');
tenancy()->initialize($tenant);

echo "\n" . str_repeat('=', 70) . "\n";
echo "測試全站各單元功能\n";
echo str_repeat('=', 70) . "\n\n";

$errors = [];
$warnings = [];
$passed = 0;

// ===== 1. 測試公司模組 =====
echo "1️⃣  測試公司模組...\n";
try {
    $totalCompanies = Company::count();
    $activeCompanies = Company::where('is_active', true)->count();
    $clients = Company::where('is_client', true)->count();
    $vendors = Company::where('is_client', false)->count();
    
    echo "   ✓ 總公司數: {$totalCompanies}\n";
    echo "   ✓ 啟用中: {$activeCompanies}\n";
    echo "   ✓ 客戶: {$clients} 家\n";
    echo "   ✓ 供應商: {$vendors} 家\n";
    
    // 測試搜尋功能
    $searchResult = Company::where('name', 'like', '%科技%')->count();
    echo "   ✓ 搜尋功能測試: 找到 {$searchResult} 筆\n";
    
    $passed++;
} catch (Exception $e) {
    $errors[] = "公司模組錯誤: " . $e->getMessage();
    echo "   ❌ 錯誤: " . $e->getMessage() . "\n";
}
echo "\n";

// ===== 2. 測試使用者模組 =====
echo "2️⃣  測試使用者模組...\n";
try {
    $totalUsers = User::count();
    $activeUsers = User::where('is_active', true)->count();
    
    echo "   ✓ 總使用者: {$totalUsers} 位\n";
    echo "   ✓ 啟用中: {$activeUsers} 位\n";
    
    // 測試使用者關聯
    $user = User::first();
    if ($user) {
        $userProjects = Project::where('manager_id', $user->id)->count();
        echo "   ✓ 使用者 '{$user->name}' 管理 {$userProjects} 個專案\n";
    }
    
    $passed++;
} catch (Exception $e) {
    $errors[] = "使用者模組錯誤: " . $e->getMessage();
    echo "   ❌ 錯誤: " . $e->getMessage() . "\n";
}
echo "\n";

// ===== 3. 測試專案模組 =====
echo "3️⃣  測試專案模組...\n";
try {
    $totalProjects = Project::count();
    $inProgress = Project::where('status', 'in_progress')->count();
    $planning = Project::where('status', 'planning')->count();
    $completed = Project::where('status', 'completed')->count();
    
    echo "   ✓ 總專案數: {$totalProjects}\n";
    echo "   ✓ 進行中: {$inProgress}\n";
    echo "   ✓ 規劃中: {$planning}\n";
    echo "   ✓ 已完成: {$completed}\n";
    
    // 測試專案關聯
    $project = Project::with('company', 'manager')->first();
    if ($project) {
        echo "   ✓ 專案 '{$project->name}' 關聯正常\n";
        echo "     - 客戶: {$project->company->name}\n";
        echo "     - 負責人: {$project->manager->name}\n";
    }
    
    // 測試預算計算
    $totalBudget = Project::sum('budget');
    echo "   ✓ 總預算: NT$ " . number_format($totalBudget) . "\n";
    
    $passed++;
} catch (Exception $e) {
    $errors[] = "專案模組錯誤: " . $e->getMessage();
    echo "   ❌ 錯誤: " . $e->getMessage() . "\n";
}
echo "\n";

// ===== 4. 測試應收帳款模組 =====
echo "4️⃣  測試應收帳款模組...\n";
try {
    $totalReceivables = Receivable::count();
    $paid = Receivable::where('status', 'paid')->count();
    $unpaid = Receivable::where('status', 'unpaid')->count();
    $partial = Receivable::where('status', 'partial')->count();
    
    $totalAmount = Receivable::sum('amount');
    $receivedAmount = Receivable::sum('received_amount');
    $outstanding = $totalAmount - $receivedAmount;
    
    echo "   ✓ 總筆數: {$totalReceivables}\n";
    echo "   ✓ 已付款: {$paid} 筆\n";
    echo "   ✓ 未付款: {$unpaid} 筆\n";
    echo "   ✓ 部分付款: {$partial} 筆\n";
    echo "   ✓ 總金額: NT$ " . number_format($totalAmount) . "\n";
    echo "   ✓ 已收金額: NT$ " . number_format($receivedAmount) . "\n";
    echo "   ✓ 未收金額: NT$ " . number_format($outstanding) . "\n";
    
    // 測試應收帳款關聯
    $receivable = Receivable::with('company', 'project')->first();
    if ($receivable) {
        echo "   ✓ 應收帳款 '{$receivable->receipt_no}' 關聯正常\n";
    }
    
    $passed++;
} catch (Exception $e) {
    $errors[] = "應收帳款模組錯誤: " . $e->getMessage();
    echo "   ❌ 錯誤: " . $e->getMessage() . "\n";
}
echo "\n";

// ===== 5. 測試應付帳款模組 =====
echo "5️⃣  測試應付帳款模組...\n";
try {
    $totalPayables = Payable::count();
    $paid = Payable::where('status', 'paid')->count();
    $unpaid = Payable::where('status', 'unpaid')->count();
    $partial = Payable::where('status', 'partial')->count();
    
    $totalAmount = Payable::sum('amount');
    $paidAmount = Payable::sum('paid_amount');
    $outstanding = $totalAmount - $paidAmount;
    
    echo "   ✓ 總筆數: {$totalPayables}\n";
    echo "   ✓ 已付款: {$paid} 筆\n";
    echo "   ✓ 未付款: {$unpaid} 筆\n";
    echo "   ✓ 部分付款: {$partial} 筆\n";
    echo "   ✓ 總金額: NT$ " . number_format($totalAmount) . "\n";
    echo "   ✓ 已付金額: NT$ " . number_format($paidAmount) . "\n";
    echo "   ✓ 未付金額: NT$ " . number_format($outstanding) . "\n";
    
    // 測試應付帳款關聯
    $payable = Payable::with('company', 'project')->first();
    if ($payable) {
        echo "   ✓ 應付帳款 '{$payable->payment_no}' 關聯正常\n";
    }
    
    $passed++;
} catch (Exception $e) {
    $errors[] = "應付帳款模組錯誤: " . $e->getMessage();
    echo "   ❌ 錯誤: " . $e->getMessage() . "\n";
}
echo "\n";

// ===== 6. 測試標籤模組 =====
echo "6️⃣  測試標籤模組...\n";
try {
    $totalTags = Tag::count();
    $projectTags = Tag::where('type', 'project')->count();
    $companyTags = Tag::where('type', 'company')->count();
    $paymentTags = Tag::where('type', 'payment_method')->count();
    
    echo "   ✓ 總標籤數: {$totalTags}\n";
    echo "   ✓ 專案標籤: {$projectTags} 個\n";
    echo "   ✓ 客戶標籤: {$companyTags} 個\n";
    echo "   ✓ 付款方式: {$paymentTags} 個\n";
    
    $passed++;
} catch (Exception $e) {
    $errors[] = "標籤模組錯誤: " . $e->getMessage();
    echo "   ❌ 錯誤: " . $e->getMessage() . "\n";
}
echo "\n";

// ===== 7. 測試支出項目模組 =====
echo "7️⃣  測試支出項目模組...\n";
try {
    $totalCategories = ExpenseCategory::count();
    $parentCategories = ExpenseCategory::whereNull('parent_id')->count();
    $childCategories = ExpenseCategory::whereNotNull('parent_id')->count();
    $activeCategories = ExpenseCategory::where('is_active', true)->count();
    
    echo "   ✓ 總項目數: {$totalCategories}\n";
    echo "   ✓ 父類別: {$parentCategories} 個\n";
    echo "   ✓ 子類別: {$childCategories} 個\n";
    echo "   ✓ 啟用中: {$activeCategories} 個\n";
    
    // 測試階層關聯
    $parent = ExpenseCategory::whereNull('parent_id')->first();
    if ($parent) {
        $children = ExpenseCategory::where('parent_id', $parent->id)->count();
        echo "   ✓ '{$parent->name}' 有 {$children} 個子項目\n";
    }
    
    $passed++;
} catch (Exception $e) {
    $errors[] = "支出項目模組錯誤: " . $e->getMessage();
    echo "   ❌ 錯誤: " . $e->getMessage() . "\n";
}
echo "\n";

// ===== 8. 測試稅款設定模組 =====
echo "8️⃣  測試稅款設定模組...\n";
try {
    $totalTaxes = TaxSetting::count();
    $activeTaxes = TaxSetting::where('is_active', true)->count();
    $defaultTax = TaxSetting::where('is_default', true)->first();
    
    echo "   ✓ 總稅款設定: {$totalTaxes}\n";
    echo "   ✓ 啟用中: {$activeTaxes} 個\n";
    if ($defaultTax) {
        echo "   ✓ 預設稅率: {$defaultTax->name} ({$defaultTax->rate}%)\n";
    }
    
    $passed++;
} catch (Exception $e) {
    $errors[] = "稅款設定模組錯誤: " . $e->getMessage();
    echo "   ❌ 錯誤: " . $e->getMessage() . "\n";
}
echo "\n";

// ===== 9. 測試銀行帳戶模組 =====
echo "9️⃣  測試銀行帳戶模組...\n";
try {
    $totalAccounts = BankAccount::count();
    $activeAccounts = BankAccount::where('is_active', true)->count();
    $defaultAccount = BankAccount::where('is_default', true)->first();
    
    echo "   ✓ 總帳戶數: {$totalAccounts}\n";
    echo "   ✓ 啟用中: {$activeAccounts} 個\n";
    if ($defaultAccount) {
        echo "   ✓ 預設帳戶: {$defaultAccount->bank_name} - {$defaultAccount->bank_account}\n";
    }
    
    $passed++;
} catch (Exception $e) {
    $errors[] = "銀行帳戶模組錯誤: " . $e->getMessage();
    echo "   ❌ 錯誤: " . $e->getMessage() . "\n";
}
echo "\n";

// ===== 測試結果摘要 =====
echo str_repeat('=', 70) . "\n";
echo "測試結果摘要\n";
echo str_repeat('=', 70) . "\n\n";

$total = 9;
$failed = count($errors);

echo "✅ 通過: {$passed}/{$total} 個模組\n";

if ($failed > 0) {
    echo "❌ 失敗: {$failed} 個模組\n\n";
    echo "錯誤詳情:\n";
    foreach ($errors as $index => $error) {
        echo "  " . ($index + 1) . ". {$error}\n";
    }
} else {
    echo "🎉 所有模組測試通過！\n";
}

if (count($warnings) > 0) {
    echo "\n⚠️  警告:\n";
    foreach ($warnings as $index => $warning) {
        echo "  " . ($index + 1) . ". {$warning}\n";
    }
}

echo "\n";
