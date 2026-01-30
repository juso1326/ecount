<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantSubscription extends Model
{
    protected $fillable = [
        'tenant_id',
        'plan',
        'price',
        'started_at',
        'ends_at',
        'status',
        'auto_renew',
        'notes',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ends_at' => 'datetime',
        'auto_renew' => 'boolean',
        'price' => 'decimal:2',
    ];

    /**
     * 租戶關聯
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * 是否已過期
     */
    public function isExpired(): bool
    {
        return $this->ends_at < now();
    }

    /**
     * 是否即將到期（7天內）
     */
    public function isExpiringSoon(): bool
    {
        return $this->ends_at->isBetween(now(), now()->addDays(7));
    }

    /**
     * 剩餘天數
     */
    public function daysRemaining(): int
    {
        return max(0, now()->diffInDays($this->ends_at, false));
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

    /**
     * 狀態名稱
     */
    public function getStatusNameAttribute(): string
    {
        return match($this->status) {
            'active' => '使用中',
            'expired' => '已過期',
            'cancelled' => '已取消',
            default => $this->status,
        };
    }
}
