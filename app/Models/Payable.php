<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 應付帳款 Model
 */
class Payable extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'payment_no',
        'project_id',
        'company_id',
        'responsible_user_id',
        'type',
        'content',
        'payment_date',
        'invoice_date',
        'due_date',
        'amount',
        'deduction',
        'paid_amount',
        'status',
        'payment_method',
        'paid_date',
        'invoice_no',
        'note',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'invoice_date' => 'date',
        'due_date' => 'date',
        'paid_date' => 'date',
        'amount' => 'decimal:2',
        'deduction' => 'decimal:2',
        'paid_amount' => 'decimal:2',
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
     * 關聯：供應商公司
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
     * 計算屬性：未付金額
     */
    public function getRemainingAmountAttribute(): float
    {
        return round($this->amount - $this->paid_amount, 2);
    }

    /**
     * 計算屬性：實際支付金額
     */
    public function getNetAmountAttribute(): float
    {
        return round($this->paid_amount - $this->deduction, 2);
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

        return round(($this->paid_amount / $this->amount) * 100, 2);
    }
}
