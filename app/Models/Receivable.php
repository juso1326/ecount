<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 應收帳款 Model
 */
class Receivable extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'receipt_no',
        'project_id',
        'company_id',
        'responsible_user_id',
        'receipt_date',
        'fiscal_year',
        'due_date',
        'amount',
        'amount_before_tax',
        'tax_rate',
        'tax_amount',
        'withholding_tax',
        'received_amount',
        'status',
        'payment_method',
        'paid_date',
        'invoice_no',
        'content',
        'quote_no',
        'note',
    ];

    protected $casts = [
        'receipt_date' => 'date',
        'due_date' => 'date',
        'paid_date' => 'date',
        'amount' => 'decimal:2',
        'amount_before_tax' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'withholding_tax' => 'decimal:2',
        'received_amount' => 'decimal:2',
    ];

    /**
     * 付款狀態常數
     */
    const STATUS_UNPAID = 'unpaid';           // 未付款
    const STATUS_PARTIAL = 'partial';         // 部分付款
    const STATUS_PAID = 'paid';               // 已付款
    const STATUS_OVERDUE = 'overdue';         // 逾期
    const STATUS_CANCELLED = 'cancelled';     // 已取消

    /**
     * 關聯：所屬專案
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * 關聯：客戶公司
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * 關聯：負責人
     */
    public function responsibleUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_user_id');
    }
    
    /**
     * 關聯：入帳記錄
     */
    public function payments()
    {
        return $this->hasMany(ReceivablePayment::class);
    }

    /**
     * 關聯：標籤
     */
    public function tags(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    /**
     * 計算屬性：未收金額
     */
    public function getRemainingAmountAttribute(): float
    {
        return round($this->amount - $this->received_amount, 2);
    }

    /**
     * 計算屬性：實際入帳金額
     */
    public function getNetAmountAttribute(): float
    {
        return round($this->received_amount - $this->withholding_tax, 2);
    }

    /**
     * 檢查是否已付清
     */
    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    /**
     * 檢查是否逾期
     */
    public function isOverdue(): bool
    {
        return $this->due_date && 
               $this->due_date->lt(now()) && 
               !$this->isPaid();
    }

    /**
     * 計算付款進度百分比
     */
    public function getPaymentProgressAttribute(): float
    {
        if ($this->amount <= 0) {
            return 0;
        }

        return round(($this->received_amount / $this->amount) * 100, 2);
    }

    /**
     * 智能搜尋 Scope
     */
    public function scopeSmartSearch($query, $keyword)
    {
        if (empty($keyword)) {
            return $query;
        }

        return $query->where(function($q) use ($keyword) {
            // 搜尋專案名稱、代碼
            $q->orWhereHas('project', function($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('code', 'like', "%{$keyword}%");
            })
            // 搜尋專案成員
            ->orWhereHas('project.members', function($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%");
            })
            // 搜尋負責人
            ->orWhereHas('responsibleUser', function($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%");
            })
            // 搜尋發票號碼
            ->orWhere('invoice_no', 'like', "%{$keyword}%")
            // 搜尋報價單號
            ->orWhere('quote_no', 'like', "%{$keyword}%")
            // 搜尋收款單號
            ->orWhere('receipt_no', 'like', "%{$keyword}%")
            // 搜尋內容
            ->orWhere('content', 'like', "%{$keyword}%");
        });
    }
}
