<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyAttributeHistory extends Model
{
    protected $fillable = [
        'company_id',
        'attribute_name',
        'old_value',
        'new_value',
        'changed_by',
        'note',
        'changed_at',
    ];

    protected $casts = [
        'old_value' => 'boolean',
        'new_value' => 'boolean',
        'changed_at' => 'datetime',
    ];

    /**
     * 關聯：所屬公司
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * 關聯：變更人
     */
    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    /**
     * 取得屬性中文名稱
     */
    public function getAttributeDisplayNameAttribute(): string
    {
        return match($this->attribute_name) {
            'is_client' => '客戶',
            'is_outsource' => '外製',
            'is_member' => '成員',
            default => $this->attribute_name,
        };
    }

    /**
     * 取得值的顯示文字
     */
    public function getValueDisplay(bool $value): string
    {
        return $value ? '是' : '否';
    }
}
