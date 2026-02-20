<?php
require __DIR__.'/vendor/autoload.php';

echo "=== 全站表單完整審查 ===\n\n";

$modules = [
    [
        'name' => 'Projects',
        'controller' => 'app/Http/Controllers/Tenant/ProjectController.php',
        'form' => 'resources/views/tenant/projects/form.blade.php',
        'model' => 'app/Models/Project.php'
    ],
    [
        'name' => 'Companies', 
        'controller' => 'app/Http/Controllers/Tenant/CompanyController.php',
        'form' => 'resources/views/tenant/companies/create.blade.php',
        'model' => 'app/Models/Company.php'
    ],
    [
        'name' => 'Receivables',
        'controller' => 'app/Http/Controllers/Tenant/ReceivableController.php',
        'form' => 'resources/views/tenant/receivables/form.blade.php',
        'model' => 'app/Models/Receivable.php'
    ],
    [
        'name' => 'Payables',
        'controller' => 'app/Http/Controllers/Tenant/PayableController.php',
        'form' => 'resources/views/tenant/payables/form.blade.php',
        'model' => 'app/Models/Payable.php'
    ],
    [
        'name' => 'Roles',
        'controller' => 'app/Http/Controllers/Tenant/RoleController.php',
        'form' => 'resources/views/tenant/roles/create.blade.php',
        'model' => null
    ],
    [
        'name' => 'Tags',
        'controller' => 'app/Http/Controllers/Tenant/TagController.php',
        'form' => 'resources/views/tenant/tags/create.blade.php',
        'model' => 'app/Models/Tag.php'
    ],
    [
        'name' => 'ExpenseCategories',
        'controller' => 'app/Http/Controllers/Tenant/ExpenseCategoryController.php',
        'form' => 'resources/views/tenant/expense-categories/create.blade.php',
        'model' => 'app/Models/ExpenseCategory.php'
    ],
    [
        'name' => 'TaxSettings',
        'controller' => 'app/Http/Controllers/Tenant/TaxSettingController.php',
        'form' => 'resources/views/tenant/tax-settings/create.blade.php',
        'model' => 'app/Models/TaxSetting.php'
    ],
];

$issues = [];

foreach ($modules as $module) {
    echo "檢查 {$module['name']}...\n";
    
    if (!file_exists($module['controller'])) {
        echo "  ✗ 控制器不存在\n\n";
        continue;
    }
    
    if (!file_exists($module['form'])) {
        echo "  ✗ 表單不存在\n\n";
        continue;
    }
    
    $controllerContent = file_get_contents($module['controller']);
    $formContent = file_get_contents($module['form']);
    
    // 提取表單欄位
    preg_match_all('/name=["\']([^"\']+)["\']/', $formContent, $matches);
    $formFields = array_unique($matches[1]);
    $formFields = array_filter($formFields, function($field) {
        return !in_array($field, ['_token', '_method']) && 
               !preg_match('/\[|\]/', $field);
    });
    
    // 檢查日期欄位
    preg_match_all('/type=["\']date["\'].*?name=["\']([^"\']+)["\']/', $formContent, $dateMatches);
    $dateFields = $dateMatches[1] ?? [];
    
    // 檢查 store 方法驗證
    $storeValidation = [];
    if (preg_match('/function store.*?\{(.*?)function\s+\w+/s', $controllerContent, $storeMatch)) {
        if (preg_match('/Validator::make\([^,]+,\s*\[(.*?)\]/s', $storeMatch[1], $validMatch)) {
            preg_match_all('/[\'"]([a-z_]+)[\'"]\s*=>/i', $validMatch[1], $fieldMatches);
            $storeValidation = array_unique($fieldMatches[1]);
        }
    }
    
    // 檢查 update 方法驗證
    $updateValidation = [];
    if (preg_match('/function update.*?\{(.*?)function\s+\w+/s', $controllerContent, $updateMatch)) {
        if (preg_match('/Validator::make\([^,]+,\s*\[(.*?)\]/s', $updateMatch[1], $validMatch)) {
            preg_match_all('/[\'"]([a-z_]+)[\'"]\s*=>/i', $validMatch[1], $fieldMatches);
            $updateValidation = array_unique($fieldMatches[1]);
        }
    }
    
    // 檢查空字串轉null處理
    $hasEmptyStringHandling = preg_match('/dateFields.*?foreach.*?===\s*[\'"][\'"]/s', $controllerContent);
    
    echo "  表單欄位: " . count($formFields) . " 個\n";
    echo "  日期欄位: " . count($dateFields) . " 個\n";
    echo "  Store驗證: " . count($storeValidation) . " 個\n";
    echo "  Update驗證: " . count($updateValidation) . " 個\n";
    echo "  空字串處理: " . ($hasEmptyStringHandling ? '✓' : '✗') . "\n";
    
    // 記錄問題
    $moduleIssues = [];
    
    if (count($formFields) > 0 && count($storeValidation) === 0) {
        $moduleIssues[] = '缺少 store 驗證';
    }
    
    if (count($formFields) > 0 && count($updateValidation) === 0) {
        $moduleIssues[] = '缺少 update 驗證';
    }
    
    if (count($storeValidation) > 0 && count($updateValidation) > 0) {
        $storeOnly = array_diff($storeValidation, $updateValidation);
        if (count($storeOnly) > 0) {
            $moduleIssues[] = 'Store比Update多 ' . count($storeOnly) . ' 個欄位';
        }
    }
    
    if (count($dateFields) > 0 && !$hasEmptyStringHandling) {
        $moduleIssues[] = '有日期欄位但缺少空字串處理';
    }
    
    if (count($moduleIssues) > 0) {
        $issues[$module['name']] = $moduleIssues;
        echo "  🔴 問題: " . implode('; ', $moduleIssues) . "\n";
    } else {
        echo "  ✓ 正常\n";
    }
    
    echo "\n";
}

echo "=== 問題總結 ===\n\n";
if (count($issues) > 0) {
    foreach ($issues as $module => $problems) {
        echo "{$module}:\n";
        foreach ($problems as $problem) {
            echo "  - {$problem}\n";
        }
    }
    echo "\n需要修復的模組: " . count($issues) . " 個\n";
} else {
    echo "✓ 所有模組都正常\n";
}
