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
        'payee_type',
        'payee_user_id',
        'payee_company_id',
        'responsible_user_id',
        'type',
        'content',
        'payment_date',
        'fiscal_year',
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
        'is_salary_paid',
        'salary_paid_at',
        'salary_paid_amount',
        'salary_paid_remark',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'invoice_date' => 'date',
        'due_date' => 'date',
        'paid_date' => 'date',
        'amount' => 'decimal:2',
        'deduction' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'is_salary_paid' => 'boolean',
        'salary_paid_at' => 'datetime',
        'salary_paid_amount' => 'decimal:2',
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
     * 關聯：給付對象廠商
     */
    public function payeeCompany(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'payee_company_id');
    }

    /**
     * 關聯：負責人
     */
    public function responsibleUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_user_id');
    }

    /**
     * 關聯：收款員工
     */
    public function payeeUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payee_user_id');
    }
    
    /**
     * 關聯：出款記錄
     */
    public function payments()
    {
        return $this->hasMany(PayablePayment::class);
    }

    /**
     * 關聯：標籤
     */
    public function tags(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    /**
     * 取得收款對象名稱
     */
    public function getPayeeNameAttribute(): string
    {
        if ($this->payee_type === 'user' && $this->payeeUser) {
            return $this->payeeUser->name;
        } elseif ($this->payee_type === 'company' && $this->company) {
            return $this->company->name;
        }
        return '-';
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

    /**
     * 檢查是否為薪資類型
     */
    public function isSalary(): bool
    {
        return $this->payee_type === 'user';
    }

    /**
     * 檢查薪資是否已撥款
     */
    public function isSalaryPaid(): bool
    {
        return $this->is_salary_paid;
    }

    /**
     * 查詢範圍：未撥款的薪資
     */
    public function scopeUnpaidSalaries($query)
    {
        return $query->where('payee_type', 'user')
                    ->where('is_salary_paid', false);
    }

    /**
     * 查詢範圍：已撥款的薪資
     */
    public function scopePaidSalaries($query)
    {
        return $query->where('payee_type', 'user')
                    ->where('is_salary_paid', true);
    }

    /**
     * 查詢範圍：指定員工的薪資
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('payee_type', 'user')
                    ->where('payee_user_id', $userId);
    }
}
