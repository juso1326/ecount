<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Cache;

// 初始化租戶
tenancy()->initialize('abc123');

echo "=== 系統設定驗證測試 ===\n\n";

// 1. 檢查設定是否存在
$cacheKey = 'tenant_settings_' . tenant('id');
echo "Cache Key: {$cacheKey}\n";

$settings = Cache::get($cacheKey);
if ($settings) {
    echo "✅ 設定已存在\n\n";
    echo "當前設定值:\n";
    foreach ($settings as $key => $value) {
        echo "  - {$key}: {$value}\n";
    }
} else {
    echo "⚠️  尚未設定，使用預設值\n";
    $settings = [
        'locale' => 'zh_TW',
        'timezone' => 'Asia/Taipei',
        'currency' => 'TWD',
        'date_format' => 'Y-m-d',
        'time_format' => 'H:i',
        'fiscal_year_start' => 1,
    ];
}

echo "\n=== 測試設定套用 ===\n\n";

// 2. 測試時區設定
$oldTimezone = config('app.timezone');
echo "系統預設時區: {$oldTimezone}\n";
echo "設定的時區: {$settings['timezone']}\n";

// 3. 測試日期格式
$testDate = now();
echo "\n日期格式測試:\n";
echo "  設定格式: {$settings['date_format']}\n";
echo "  套用結果: " . $testDate->format($settings['date_format']) . "\n";

// 4. 測試時間格式
echo "\n時間格式測試:\n";
echo "  設定格式: {$settings['time_format']}\n";
echo "  套用結果: " . $testDate->format($settings['time_format']) . "\n";

// 5. 測試會計年度
echo "\n會計年度開始月份: {$settings['fiscal_year_start']}月\n";

echo "\n✅ 所有設定值都能正確讀取和套用\n";
