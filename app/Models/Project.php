<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * 專案資料 Model
 */
class Project extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'project_type',
        'company_id',
        'manager_id',
        'responsible_user_id',
        'status',
        'start_date',
        'end_date',
        'budget',
        'actual_cost',
        'description',
        'content',
        'quote_no',
        'note',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'budget' => 'integer',
        'actual_cost' => 'integer',
    ];

    /**
     * 專案狀態常數
     */
    const STATUS_PLANNING = 'planning';      // 規劃中
    const STATUS_IN_PROGRESS = 'in_progress'; // 進行中
    const STATUS_ON_HOLD = 'on_hold';        // 暫停
    const STATUS_COMPLETED = 'completed';    // 已完成
    const STATUS_CANCELLED = 'cancelled';    // 已取消

    /**
     * 關聯：所屬公司
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * 關聯：專案經理
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * 關聯：專案成員
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_members')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    /**
     * 關聯：應收帳款
     */
    public function receivables(): HasMany
    {
        return $this->hasMany(Receivable::class)->orderBy('receipt_date', 'desc');
    }

    /**
     * 關聯：應付帳款
     */
    public function payables(): HasMany
    {
        return $this->hasMany(Payable::class)->orderBy('payment_date', 'desc');
    }

    /**
     * 關聯：標籤
     */
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    /**
     * 取得預算使用率
     */
    public function getBudgetUsagePercentageAttribute(): float
    {
        if ($this->budget <= 0) {
            return 0;
        }

        return round(($this->actual_cost / $this->budget) * 100, 2);
    }

    /**
     * 取得剩餘預算
     */
    public function getRemainingBudgetAttribute(): float
    {
        return $this->budget - $this->actual_cost;
    }

    /**
     * 檢查專案是否進行中
     */
    public function isInProgress(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    /**
     * 檢查專案是否已完成
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Scope: 智能搜尋（專案名稱、代碼、成員、負責人、發票號、報價單號）
     */
    public function scopeSmartSearch($query, $keyword)
    {
        if (empty($keyword)) {
            return $query;
        }

        return $query->where(function($q) use ($keyword) {
            // 搜尋專案名稱或代碼
            $q->where('name', 'like', "%{$keyword}%")
              ->orWhere('code', 'like', "%{$keyword}%")
              ->orWhere('quote_no', 'like', "%{$keyword}%")
              // 搜尋負責人
              ->orWhereHas('manager', function($q2) use ($keyword) {
                  $q2->where('name', 'like', "%{$keyword}%");
              })
              // 搜尋專案成員
              ->orWhereHas('members', function($q2) use ($keyword) {
                  $q2->where('name', 'like', "%{$keyword}%");
              })
              // 搜尋應收帳款的發票號
              ->orWhereHas('receivables', function($q2) use ($keyword) {
                  $q2->where('invoice_no', 'like', "%{$keyword}%");
              });
        });
    }

    /**
     * Scope: 依開案日期範圍搜尋
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        if ($startDate && $endDate) {
            return $query->whereBetween('start_date', [$startDate, $endDate]);
        } elseif ($startDate) {
            return $query->where('start_date', '>=', $startDate);
        } elseif ($endDate) {
            return $query->where('start_date', '<=', $endDate);
        }
        
        return $query;
    }
}
