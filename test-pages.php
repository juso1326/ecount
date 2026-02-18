<?php

/**
 * 測試頁面路由和基本功能
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "\n" . str_repeat('=', 70) . "\n";
echo "測試系統路由與頁面\n";
echo str_repeat('=', 70) . "\n\n";

// 測試路由
$routes = [
    'GET' => [
        '/projects' => '專案列表',
        '/receivables' => '應收帳款列表',
        '/payables' => '應付帳款列表',
        '/companies' => '客戶廠商列表',
        '/tags' => '標籤管理',
        '/expense-categories' => '支出項目',
        '/tax-settings' => '稅款設定',
        '/bank-accounts' => '銀行帳戶',
    ],
];

$passed = 0;
$failed = 0;

foreach ($routes as $method => $routeList) {
    foreach ($routeList as $uri => $description) {
        try {
            $request = Illuminate\Http\Request::create($uri, $method);
            $request->headers->set('Host', 'abc123.ecount.test');
            
            $response = $kernel->handle($request);
            $status = $response->getStatusCode();
            
            // 302 表示重定向到登入頁，這是正常的
            // 200 表示頁面正常
            if ($status === 200 || $status === 302) {
                echo "✅ {$method} {$uri} - {$description} (Status: {$status})\n";
                $passed++;
            } else {
                echo "⚠️  {$method} {$uri} - {$description} (Status: {$status})\n";
                $failed++;
            }
        } catch (Exception $e) {
            echo "❌ {$method} {$uri} - {$description}\n";
            echo "   錯誤: " . $e->getMessage() . "\n";
            $failed++;
        }
    }
}

echo "\n" . str_repeat('=', 70) . "\n";
echo "路由測試結果\n";
echo str_repeat('=', 70) . "\n";
echo "✅ 通過: {$passed}\n";
echo "❌ 失敗: {$failed}\n\n";

if ($failed === 0) {
    echo "🎉 所有路由測試通過！\n\n";
}
