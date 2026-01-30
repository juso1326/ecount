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
        'plan_started_at',
        'plan_ends_at',
        'auto_renew',
        'status',
        'settings',
    ];

    /**
     * 屬性類型轉換
     */
    protected $casts = [
        'settings' => 'array',
        'plan_started_at' => 'datetime',
        'plan_ends_at' => 'datetime',
        'auto_renew' => 'boolean',
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

    /**
     * 訂閱記錄關聯
     */
    public function subscriptions()
    {
        return $this->hasMany(TenantSubscription::class);
    }

    /**
     * 當前訂閱
     */
    public function currentSubscription()
    {
        return $this->hasOne(TenantSubscription::class)->where('status', 'active')->latest('ends_at');
    }

    /**
     * 檢查方案是否過期
     */
    public function isPlanExpired(): bool
    {
        if (!$this->plan_ends_at) {
            return false;
        }
        return $this->plan_ends_at < now();
    }

    /**
     * 檢查方案是否即將到期（7天內）
     */
    public function isPlanExpiringSoon(): bool
    {
        if (!$this->plan_ends_at) {
            return false;
        }
        return $this->plan_ends_at->isBetween(now(), now()->addDays(7));
    }

    /**
     * 方案剩餘天數
     */
    public function planDaysRemaining(): int
    {
        if (!$this->plan_ends_at) {
            return 999;
        }
        return max(0, now()->diffInDays($this->plan_ends_at, false));
    }

    /**
     * 方案名稱
     */
    public function getPlanNameAttribute(): string
    {
        return match($this->plan) {
            'basic' => '基礎方案',
            'professional' => '專業方案',
            'enterprise' => '企業方案',
            default => $this->plan,
        };
    }
}
