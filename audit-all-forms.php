<?php
require __DIR__.'/vendor/autoload.php';

echo "=== 全站表單欄位儲存檢查 ===\n\n";

// 定義需要檢查的控制器和對應的表單
$controllers = [
    'UserController' => 'users/_form.blade.php',
    'ProjectController' => 'projects/form.blade.php',
    'CompanyController' => 'companies/_form.blade.php',
    'ReceivableController' => 'receivables/create.blade.php',
    'PayableController' => 'payables/create.blade.php',
    'SalaryController' => 'salaries/create.blade.php',
    'RoleController' => 'roles/form.blade.php',
    'TagController' => 'tags/form.blade.php',
    'ExpenseCategoryController' => 'expense-categories/form.blade.php',
    'TaxSettingController' => 'tax-settings/form.blade.php',
];

echo "檢查範圍: " . count($controllers) . " 個控制器\n\n";

foreach ($controllers as $controller => $formPath) {
    echo "=== {$controller} ===\n";
    
    $controllerFile = "app/Http/Controllers/Tenant/{$controller}.php";
    $viewFile = "resources/views/tenant/{$formPath}";
    
    // 檢查檔案是否存在
    if (!file_exists($controllerFile)) {
        echo "  ⚠ 控制器檔案不存在\n\n";
        continue;
    }
    
    if (!file_exists($viewFile)) {
        echo "  ⚠ 表單檔案不存在: {$viewFile}\n\n";
        continue;
    }
    
    // 讀取控制器內容
    $controllerContent = file_get_contents($controllerFile);
    
    // 檢查是否有 store 和 update 方法
    $hasStore = preg_match('/public function store\(/', $controllerContent);
    $hasUpdate = preg_match('/public function update\(/', $controllerContent);
    
    echo "  Store方法: " . ($hasStore ? '✓' : '✗') . "\n";
    echo "  Update方法: " . ($hasUpdate ? '✓' : '✗') . "\n";
    
    // 讀取表單內容並提取 name 屬性
    $formContent = file_get_contents($viewFile);
    preg_match_all('/name=["\']([^"\']+)["\']/', $formContent, $matches);
    $formFields = array_unique($matches[1]);
    $formFields = array_filter($formFields, function($field) {
        // 排除特殊欄位
        return !in_array($field, ['_token', '_method']) && !preg_match('/\[|\]/', $field);
    });
    
    echo "  表單欄位數: " . count($formFields) . "\n";
    
    if (count($formFields) > 0) {
        echo "  表單欄位: " . implode(', ', array_slice($formFields, 0, 10));
        if (count($formFields) > 10) {
            echo " ... (+" . (count($formFields) - 10) . "個)";
        }
        echo "\n";
    }
    
    // 檢查驗證規則
    if (preg_match('/Validator::make\([^,]+,\s*\[(.*?)\]/s', $controllerContent, $validationMatch)) {
        preg_match_all('/[\'"]([a-z_]+)[\'"]\s*=>/i', $validationMatch[1], $validationFields);
        $validatedFields = array_unique($validationFields[1]);
        echo "  驗證欄位數: " . count($validatedFields) . "\n";
        
        // 比對差異
        $missingInValidation = array_diff($formFields, $validatedFields);
        if (count($missingInValidation) > 0) {
            echo "  🔴 表單有但驗證缺少: " . implode(', ', array_slice($missingInValidation, 0, 5));
            if (count($missingInValidation) > 5) {
                echo " ... (+" . (count($missingInValidation) - 5) . "個)";
            }
            echo "\n";
        }
    } else {
        echo "  ⚠ 未找到驗證規則\n";
    }
    
    echo "\n";
}

echo "=== 檢查完成 ===\n";
echo "\n建議:\n";
echo "1. 檢查有 🔴 標記的控制器\n";
echo "2. 確認缺少的欄位是否需要驗證\n";
echo "3. 檢查是否有空字串日期欄位問題\n";
echo "4. 驗證關聯欄位是否正確處理（如角色、標籤等）\n";
