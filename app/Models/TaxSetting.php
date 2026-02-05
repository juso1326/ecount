<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxSetting extends Model
{
    protected $fillable = [
        'name',
        'rate',
        'is_default',
        'is_active',
        'description',
        'sort_order',
    ];

    protected $casts = [
        'rate' => 'decimal:2',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Scope: 僅啟用的
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: 排序
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * 取得預設稅率設定
     */
    public static function getDefault()
    {
        return static::where('is_default', true)
            ->where('is_active', true)
            ->first();
    }

    /**
     * 設為預設
     */
    public function setAsDefault(): void
    {
        // 取消其他預設
        static::where('is_default', true)->update(['is_default' => false]);
        
        // 設定當前為預設
        $this->is_default = true;
        $this->save();
    }

    /**
     * 取得格式化的稅率顯示
     */
    public function getFormattedRateAttribute(): string
    {
        return $this->rate . '%';
    }
}
