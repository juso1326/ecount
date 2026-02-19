<?php
/**
 * 全站頁面測試腳本
 * 測試所有頁面是否正常訪問，收集錯誤
 */

$baseUrl = 'https://abc123.ecount.test';

// 測試頁面列表
$pages = [
    // Dashboard
    ['name' => 'Dashboard', 'url' => '/'],
    
    // 用戶管理
    ['name' => '用戶列表', 'url' => '/users'],
    ['name' => '新增用戶', 'url' => '/users/create'],
    ['name' => '用戶詳情', 'url' => '/users/5'], // 管理員 ID
    ['name' => '編輯用戶', 'url' => '/users/5/edit'],
    
    // 公司管理
    ['name' => '公司列表', 'url' => '/companies'],
    ['name' => '新增公司', 'url' => '/companies/create'],
    ['name' => '公司詳情', 'url' => '/companies/1'],
    ['name' => '編輯公司', 'url' => '/companies/1/edit'],
    
    // 專案管理
    ['name' => '專案列表', 'url' => '/projects'],
    ['name' => '新增專案', 'url' => '/projects/create'],
    ['name' => '專案詳情', 'url' => '/projects/1'],
    ['name' => '編輯專案', 'url' => '/projects/1/edit'],
    
    // 應收帳款
    ['name' => '應收列表', 'url' => '/receivables'],
    ['name' => '新增應收', 'url' => '/receivables/create'],
    ['name' => '編輯應收', 'url' => '/receivables/1/edit'],
    ['name' => '快速收款', 'url' => '/receivables/quick-receive'],
    
    // 應付帳款
    ['name' => '應付列表', 'url' => '/payables'],
    ['name' => '新增應付', 'url' => '/payables/create'],
    ['name' => '編輯應付', 'url' => '/payables/1/edit'],
    
    // 薪資管理
    ['name' => '薪資列表', 'url' => '/salaries'],
    ['name' => '薪資詳情', 'url' => '/salaries/1'],
    
    // 標籤管理
    ['name' => '標籤列表', 'url' => '/tags'],
    ['name' => '新增標籤', 'url' => '/tags/create'],
    ['name' => '編輯標籤', 'url' => '/tags/1/edit'],
    
    // 費用類別
    ['name' => '費用類別列表', 'url' => '/expense-categories'],
    ['name' => '新增費用類別', 'url' => '/expense-categories/create'],
    ['name' => '編輯費用類別', 'url' => '/expense-categories/1/edit'],
    
    // 稅務設定
    ['name' => '稅務設定列表', 'url' => '/tax-settings'],
    ['name' => '新增稅務設定', 'url' => '/tax-settings/create'],
    
    // 角色權限
    ['name' => '角色列表', 'url' => '/roles'],
    ['name' => '新增角色', 'url' => '/roles/create'],
    ['name' => '角色詳情', 'url' => '/roles/1'],
    ['name' => '編輯角色', 'url' => '/roles/1/edit'],
    
    // 設定
    ['name' => '銀行帳戶設定', 'url' => '/settings/bank-accounts'],
    
    // 財務報表
    ['name' => '財務總覽', 'url' => '/reports/financial-overview?fiscal_year=2025'],
    ['name' => '應收應付分析', 'url' => '/reports/ar-ap-analysis'],
    ['name' => '專案損益', 'url' => '/reports/project-profit-loss'],
    ['name' => '薪資人力成本', 'url' => '/reports/payroll-labor?year=2025'],
];

echo "🧪 開始測試全站頁面...\n";
echo str_repeat("=", 80) . "\n\n";

$results = [
    'success' => [],
    'error' => [],
    'total' => count($pages)
];

foreach ($pages as $page) {
    $url = $baseUrl . $page['url'];
    
    // 使用 curl 測試頁面
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_NOBODY, true); // 只取 header
    
    curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $status = ($httpCode >= 200 && $httpCode < 400) ? '✓' : '✗';
    $color = ($httpCode >= 200 && $httpCode < 400) ? "\033[32m" : "\033[31m";
    $reset = "\033[0m";
    
    printf(
        "%s %s %-30s %s[%d]%s\n",
        $status,
        $page['name'],
        str_pad('', 30 - mb_strlen($page['name']), '.'),
        $color,
        $httpCode,
        $reset
    );
    
    if ($httpCode >= 200 && $httpCode < 400) {
        $results['success'][] = $page['name'];
    } else {
        $results['error'][] = [
            'name' => $page['name'],
            'url' => $page['url'],
            'code' => $httpCode
        ];
    }
}

echo "\n" . str_repeat("=", 80) . "\n";
echo "📊 測試統計\n";
echo str_repeat("=", 80) . "\n";
echo "總頁面數: " . $results['total'] . "\n";
echo "成功: \033[32m" . count($results['success']) . "\033[0m\n";
echo "失敗: \033[31m" . count($results['error']) . "\033[0m\n";

if (!empty($results['error'])) {
    echo "\n❌ 錯誤詳情:\n";
    foreach ($results['error'] as $error) {
        echo "  • {$error['name']} ({$error['url']}) - HTTP {$error['code']}\n";
    }
}

echo "\n";
