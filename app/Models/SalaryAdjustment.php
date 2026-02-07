<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalaryAdjustment extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'amount',
        'start_date',
        'end_date',
        'recurrence',
        'is_active',
        'remark',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * 關聯：員工
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 查詢範圍：啟用的調整項
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * 查詢範圍：加項
     */
    public function scopeAdditions($query)
    {
        return $query->where('type', 'add');
    }

    /**
     * 查詢範圍：扣項
     */
    public function scopeDeductions($query)
    {
        return $query->where('type', 'deduct');
    }

    /**
     * 查詢範圍：在指定期間內有效的調整項
     */
    public function scopeInPeriod($query, $startDate, $endDate)
    {
        return $query->where(function($q) use ($startDate, $endDate) {
            $q->whereBetween('start_date', [$startDate, $endDate])
              ->orWhere(function($q2) use ($startDate) {
                  $q2->where('start_date', '<=', $startDate)
                     ->where(function($q3) use ($startDate) {
                         $q3->whereNull('end_date')
                            ->orWhere('end_date', '>=', $startDate);
                     });
              });
        });
    }

    /**
     * 是否為加項
     */
    public function isAddition(): bool
    {
        return $this->type === 'add';
    }

    /**
     * 是否為扣項
     */
    public function isDeduction(): bool
    {
        return $this->type === 'deduct';
    }

    /**
     * 取得帶正負號的金額
     */
    public function getSignedAmount(): float
    {
        return $this->isAddition() ? (float)$this->amount : -(float)$this->amount;
    }
}
