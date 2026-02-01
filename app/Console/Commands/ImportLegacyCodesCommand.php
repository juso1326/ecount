<?php

namespace App\Console\Commands;

use App\Models\Code;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportLegacyCodesCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'legacy:import-codes 
                            {--legacy-db=legacy : Legacy database connection name}
                            {--dry-run : Run without actually importing}';

    /**
     * The console command description.
     */
    protected $description = '從舊系統的 code_c01~c04 匯入資料到 codes 表';

    /**
     * 分類映射表
     */
    protected array $categoryMapping = [
        'code_c01' => [
            'category' => 'project_type',
            'name' => '專案類型',
            'id_field' => 'C01_no',
            'name_field' => 'C01_nm',
        ],
        'code_c02' => [
            'category' => 'department_category',
            'name' => '部門類別',
            'id_field' => 'C02_no',
            'name_field' => 'C02_nm',
        ],
        'code_c03' => [
            'category' => 'expense_type',
            'name' => '費用類別',
            'id_field' => 'C03_no',
            'name_field' => 'C03_nm',
        ],
        'code_c04' => [
            'category' => 'status_code',
            'name' => '狀態代碼',
            'id_field' => 'C04_no',
            'name_field' => 'C04_nm',
        ],
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $legacyDb = $this->option('legacy-db');
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->warn('🔍 Dry-run 模式：不會實際匯入資料');
        }

        $this->info('開始匯入舊代碼資料...');
        $this->newLine();

        // 檢查舊資料庫連線
        try {
            DB::connection($legacyDb)->getPdo();
        } catch (\Exception $e) {
            $this->error("❌ 無法連接到舊資料庫 [{$legacyDb}]");
            $this->error("請在 config/database.php 中設定 '{$legacyDb}' 連線");
            $this->newLine();
            $this->line("範例設定：");
            $this->line("'{$legacyDb}' => [");
            $this->line("    'driver' => 'mysql',");
            $this->line("    'host' => '127.0.0.1',");
            $this->line("    'port' => '3306',");
            $this->line("    'database' => 'old_database_name',");
            $this->line("    'username' => 'root',");
            $this->line("    'password' => '',");
            $this->line("],");
            return 1;
        }

        $totalImported = 0;
        $totalErrors = 0;

        // 逐一處理每個舊表
        foreach ($this->categoryMapping as $tableName => $config) {
            $this->info("📦 處理 {$tableName} ({$config['name']})...");

            try {
                // 檢查舊表是否存在
                $exists = DB::connection($legacyDb)
                    ->select("SHOW TABLES LIKE '{$tableName}'");

                if (empty($exists)) {
                    $this->warn("  ⚠️  表 {$tableName} 不存在，跳過");
                    continue;
                }

                // 讀取舊資料
                $legacyData = DB::connection($legacyDb)
                    ->table($tableName)
                    ->select(
                        $config['id_field'],
                        $config['name_field'],
                        'ADD_ID',
                        'ADD_DATE',
                        'ADD_TIME',
                        'ALTER_ID',
                        'ALTER_DATE',
                        'ALTER_TIME'
                    )
                    ->get();

                $count = $legacyData->count();
                $this->line("  找到 {$count} 筆資料");

                if ($count === 0) {
                    continue;
                }

                // 轉換並匯入
                $imported = 0;
                $skipped = 0;

                foreach ($legacyData as $index => $row) {
                    $code = $row->{$config['id_field']};
                    $name = $row->{$config['name_field']};

                    // 檢查是否已存在
                    $exists = Code::where('category', $config['category'])
                        ->where('code', $code)
                        ->exists();

                    if ($exists) {
                        $skipped++;
                        continue;
                    }

                    // 準備資料
                    $data = [
                        'category' => $config['category'],
                        'code' => $code,
                        'name' => $name,
                        'sort_order' => $index + 1,
                        'is_active' => true,
                        'description' => null,
                        'created_by' => $this->getUserIdFromLegacy($row->ADD_ID ?? null),
                        'updated_by' => $this->getUserIdFromLegacy($row->ALTER_ID ?? null),
                        'created_at' => $this->parseDateTime($row->ADD_DATE ?? null, $row->ADD_TIME ?? null),
                        'updated_at' => $this->parseDateTime($row->ALTER_DATE ?? null, $row->ALTER_TIME ?? null),
                    ];

                    if (!$isDryRun) {
                        Code::create($data);
                    }

                    $imported++;
                }

                $this->info("  ✓ 匯入 {$imported} 筆，跳過 {$skipped} 筆（已存在）");
                $totalImported += $imported;

            } catch (\Exception $e) {
                $this->error("  ❌ 處理 {$tableName} 時發生錯誤：" . $e->getMessage());
                $totalErrors++;
            }

            $this->newLine();
        }

        // 總結
        $this->newLine();
        if ($isDryRun) {
            $this->info("🔍 Dry-run 完成！預計會匯入 {$totalImported} 筆資料");
        } else {
            $this->info("✅ 匯入完成！");
            $this->info("   成功匯入：{$totalImported} 筆");
            if ($totalErrors > 0) {
                $this->warn("   發生錯誤：{$totalErrors} 個分類");
            }
        }

        return 0;
    }

    /**
     * 從舊系統的使用者ID對應到新系統
     */
    protected function getUserIdFromLegacy(?string $legacyUserId): ?int
    {
        if (empty($legacyUserId)) {
            return null;
        }

        // TODO: 實作使用者ID對應邏輯
        // 暫時返回 null，或對應到管理員帳號
        // 可以建立一個 legacy_user_mapping 表來儲存對應關係
        return null;
    }

    /**
     * 將舊系統的日期時間格式轉換為 Carbon
     */
    protected function parseDateTime(?string $date, ?string $time): ?\Illuminate\Support\Carbon
    {
        if (empty($date)) {
            return now();
        }

        try {
            // 舊系統使用台灣年份 (例如: 1041125 = 2015-11-25)
            // 或西元年份 (例如: 20151125)
            $dateStr = trim($date);
            $timeStr = trim($time ?? '000000');

            // 判斷是民國年還是西元年
            if (strlen($dateStr) === 7) {
                // 民國年 (1041125)
                $year = (int)substr($dateStr, 0, 3) + 1911;
                $month = substr($dateStr, 3, 2);
                $day = substr($dateStr, 5, 2);
            } else {
                // 西元年 (20151125)
                $year = substr($dateStr, 0, 4);
                $month = substr($dateStr, 4, 2);
                $day = substr($dateStr, 6, 2);
            }

            $hour = substr($timeStr, 0, 2);
            $minute = substr($timeStr, 2, 2);
            $second = substr($timeStr, 4, 2);

            return \Illuminate\Support\Carbon::create(
                $year, $month, $day, $hour, $minute, $second
            );
        } catch (\Exception $e) {
            return now();
        }
    }
}
