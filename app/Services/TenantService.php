<?php

namespace App\Services;

use App\Jobs\CreateTenantJob;
use App\Models\Tenant;
use Illuminate\Support\Str;

class TenantService
{
    /**
     * 快速建立新租戶（非同步）
     */
    public function createTenantAsync(array $data): object
    {
        // 生成租戶 ID
        $tenantId = $data['tenant_id'] ?? Str::slug($data['name']);
        
        // 生成子域名
        $domain = $data['domain'] ?? $tenantId . '.' . config('app.domain', 'localhost');
        
        // 生成預設密碼（如未提供）
        $password = $data['password'] ?? Str::random(12);
        
        // 驗證租戶是否已存在
        if (Tenant::find($tenantId)) {
            throw new \Exception("租戶 ID '{$tenantId}' 已存在");
        }
        
        // 派發非同步任務
        CreateTenantJob::dispatch(
            tenantId: $tenantId,
            tenantName: $data['name'],
            domain: $domain,
            adminEmail: $data['email'],
            adminPassword: $password,
            plan: $data['plan'] ?? 'basic'
        );
        
        // 回傳租戶資訊（此時資料庫可能尚未建立）
        return (object) [
            'id' => $tenantId,
            'name' => $data['name'],
            'domain' => $domain,
            'email' => $data['email'],
            'password' => $password, // 僅在開發環境返回
            'status' => 'creating',
        ];
    }
    
    /**
     * 同步建立新租戶（用於測試或小量建立）
     */
    public function createTenantSync(array $data): Tenant
    {
        $tenantId = $data['tenant_id'] ?? Str::slug($data['name']);
        $domain = $data['domain'] ?? $tenantId . '.' . config('app.domain', 'localhost');
        $password = $data['password'] ?? Str::random(12);
        
        if (Tenant::find($tenantId)) {
            throw new \Exception("租戶 ID '{$tenantId}' 已存在");
        }
        
        // 直接執行 Job 的邏輯
        $job = new CreateTenantJob(
            tenantId: $tenantId,
            tenantName: $data['name'],
            domain: $domain,
            adminEmail: $data['email'],
            adminPassword: $password,
            plan: $data['plan'] ?? 'basic'
        );
        
        $job->handle();
        
        return Tenant::find($tenantId);
    }
    
    /**
     * 取得租戶資訊
     */
    public function getTenant(string $tenantId): ?Tenant
    {
        return Tenant::find($tenantId);
    }
    
    /**
     * 停用租戶
     */
    public function suspendTenant(string $tenantId): bool
    {
        $tenant = Tenant::find($tenantId);
        
        if (!$tenant) {
            return false;
        }
        
        $tenant->update(['status' => 'suspended']);
        return true;
    }
    
    /**
     * 啟用租戶
     */
    public function activateTenant(string $tenantId): bool
    {
        $tenant = Tenant::find($tenantId);
        
        if (!$tenant) {
            return false;
        }
        
        $tenant->update(['status' => 'active']);
        return true;
    }
    
    /**
     * 刪除租戶（包含資料庫）
     */
    public function deleteTenant(string $tenantId): bool
    {
        $tenant = Tenant::find($tenantId);
        
        if (!$tenant) {
            return false;
        }
        
        // 刪除租戶會自動觸發資料庫刪除（由 stancl/tenancy 處理）
        $tenant->delete();
        
        return true;
    }
}
