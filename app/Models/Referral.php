<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Referral extends Model
{
    protected $fillable = [
        'referrer_id',
        'referred_id',
        'referral_code',
        'referred_email',
        'status',
        'registered_at',
        'subscribed_at',
        'reward_given',
        'reward_days',
    ];

    protected $casts = [
        'registered_at' => 'datetime',
        'subscribed_at' => 'datetime',
        'reward_given' => 'boolean',
        'reward_days' => 'integer',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_REGISTERED = 'registered';
    const STATUS_SUBSCRIBED = 'subscribed';

    /**
     * 推薦人
     */
    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    /**
     * 被推薦人
     */
    public function referred(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_id');
    }

    /**
     * 產生唯一推薦碼
     */
    public static function generateUniqueCode(): string
    {
        do {
            $code = strtoupper(substr(md5(uniqid()), 0, 8));
        } while (self::where('referral_code', $code)->exists());

        return $code;
    }

    /**
     * 檢查是否可以給予獎勵
     */
    public function canGiveReward(): bool
    {
        return $this->status === self::STATUS_SUBSCRIBED 
            && !$this->reward_given
            && $this->referred_id !== null;
    }

    /**
     * 標記獎勵已發放並給予雙方延展
     */
    public function markRewardGiven(): void
    {
        $this->update([
            'reward_given' => true,
        ]);
    }
    
    /**
     * 發放推薦獎勵給雙方
     */
    public function giveRewardToBoth(): bool
    {
        if (!$this->canGiveReward()) {
            return false;
        }
        
        // 給推薦人延展
        if ($this->referrer && $this->referrer->tenant) {
            $this->extendTenantTrial($this->referrer->tenant, $this->reward_days, '推薦獎勵');
        }
        
        // 給被推薦人延展
        if ($this->referred && $this->referred->tenant) {
            $this->extendTenantTrial($this->referred->tenant, $this->reward_days, '註冊訂閱獎勵');
        }
        
        $this->markRewardGiven();
        
        return true;
    }
    
    /**
     * 延長租戶試用期
     */
    protected function extendTenantTrial($tenant, int $days, string $reason): void
    {
        $currentTrialEnd = $tenant->trial_ends_at ?? now();
        $newTrialEnd = $currentTrialEnd->addDays($days);
        
        $tenant->update([
            'trial_ends_at' => $newTrialEnd,
            'trial_extension_days' => ($tenant->trial_extension_days ?? 0) + $days,
            'extended_at' => now(),
            'extension_reason' => $reason,
        ]);
    }
}
