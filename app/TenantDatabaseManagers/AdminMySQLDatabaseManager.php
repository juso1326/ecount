<?php

namespace App\TenantDatabaseManagers;

use Illuminate\Support\Facades\DB;
use Stancl\Tenancy\Contracts\TenantDatabaseManager;
use Stancl\Tenancy\Contracts\TenantWithDatabase;

/**
 * 使用 admin 連線建立/刪除租戶資料庫。
 * 不實作 ManagesDatabaseUsers，租戶連線直接沿用 template connection 的 root 憑證。
 */
class AdminMySQLDatabaseManager implements TenantDatabaseManager
{
    public function createDatabase(TenantWithDatabase $tenant): bool
    {
        $dbName = $tenant->database()->getName();

        DB::connection('mysql')->statement(
            "CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
        );

        return true;
    }

    public function deleteDatabase(TenantWithDatabase $tenant): bool
    {
        $dbName = $tenant->database()->getName();

        DB::connection('mysql')->statement("DROP DATABASE IF EXISTS `{$dbName}`");

        return true;
    }

    public function databaseExists(string $name): bool
    {
        try {
            $result = DB::connection('mysql')
                ->select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?", [$name]);
            return count($result) > 0;
        } catch (\Exception) {
            return false;
        }
    }

    public function makeConnectionConfig(array $baseConfig, string $databaseName): array
    {
        $baseConfig['database'] = $databaseName;

        // If tenancy stored null/empty per-tenant credentials (from old ManagesDatabaseUsers setup),
        // fall back to the central connection's root credentials so tenant DB is accessible.
        if (empty($baseConfig['username'])) {
            $central = config('database.connections.' . config('tenancy.database.central_connection', 'central'));
            $baseConfig['username'] = $central['username'] ?? $baseConfig['username'];
            $baseConfig['password'] = $central['password'] ?? $baseConfig['password'];
        }

        return $baseConfig;
    }

    public function setConnection(string $connection): void
    {
        // Uses fixed 'admin' connection; no-op.
    }
}
