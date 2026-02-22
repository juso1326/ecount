<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class ResetTestDatabase extends Command
{
    protected $signature = 'db:reset-test {--seed : Run seeders after migration}';
    protected $description = '重置測試資料庫 (ecount_test)，僅限 testing 環境使用';

    public function handle(): int
    {
        if (app()->environment('production')) {
            $this->error('禁止在 production 環境執行此指令！');
            return 1;
        }

        $db = config('database.connections.central.database');
        $this->info("重置測試資料庫：{$db}");

        // 確保測試 DB 存在
        try {
            DB::connection('mysql')->statement(
                "CREATE DATABASE IF NOT EXISTS `{$db}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            );
        } catch (\Exception $e) {
            $this->warn('建立 DB 失敗（可能已存在）：' . $e->getMessage());
        }

        Artisan::call('migrate:fresh', ['--force' => true]);
        $this->info(Artisan::output());

        if ($this->option('seed')) {
            Artisan::call('db:seed', ['--force' => true]);
            $this->info(Artisan::output());
        }

        $this->info('✅ 測試資料庫重置完成');
        return 0;
    }
}
