<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    /**
     * 可批量賦值的屬性
     */
    protected $fillable = [
        'id',
        'name',
        'email',
        'plan',
        'status',
        'settings',
    ];

    /**
     * 屬性類型轉換
     */
    protected $casts = [
        'settings' => 'array',
    ];

    /**
     * 租戶狀態常數
     */
    const STATUS_ACTIVE = 'active';
    const STATUS_SUSPENDED = 'suspended';
    const STATUS_INACTIVE = 'inactive';

    /**
     * 訂閱方案常數
     */
    const PLAN_BASIC = 'basic';
    const PLAN_PROFESSIONAL = 'professional';
    const PLAN_ENTERPRISE = 'enterprise';

    /**
     * 檢查租戶是否啟用
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * 取得資料庫名稱
     */
    public function getDatabaseName(): string
    {
        return config('tenancy.database.prefix') . $this->id . config('tenancy.database.suffix');
    }

    /**
     * 取得完整域名
     */
    public function getFullDomainAttribute(): string
    {
        return $this->domains->first()?->domain ?? '';
    }
}
