<?php

namespace App\TenantDatabaseManagers;

use Illuminate\Support\Facades\DB;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\DatabaseConfig;

/**
 * 使用 admin 連線建立/刪除租戶資料庫，
 * 解決 tenant_admin 用戶缺乏 CREATE DATABASE 權限的問題。
 */
class AdminMySQLDatabaseManager implements \Stancl\Tenancy\Contracts\ManagesDatabaseUsers
{
    public function createDatabase(TenantWithDatabase $tenant): bool
    {
        $dbName = $tenant->database()->getName();

        DB::connection('admin')->statement(
            "CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
        );

        return true;
    }

    public function deleteDatabase(TenantWithDatabase $tenant): bool
    {
        $dbName = $tenant->database()->getName();

        DB::connection('admin')->statement("DROP DATABASE IF EXISTS `{$dbName}`");

        return true;
    }

    public function databaseExists(string $name): bool
    {
        $result = DB::connection('admin')
            ->select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?", [$name]);

        return count($result) > 0;
    }

    public function makeConnectionConfig(array $baseConfig, string $databaseName): array
    {
        $baseConfig['database'] = $databaseName;
        return $baseConfig;
    }

    public function setConnection(string $connection): void
    {
        // no-op
    }

    public function createUser(DatabaseConfig $databaseConfig): bool
    {
        return true;
    }

    public function deleteUser(DatabaseConfig $databaseConfig): bool
    {
        return true;
    }

    public function userExists(string $username): bool
    {
        return true;
    }
}
