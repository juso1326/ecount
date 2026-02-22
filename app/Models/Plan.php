<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model
{
    use SoftDeletes;

    protected $connection = 'central';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'annual_price',
        'max_users',
        'max_companies',
        'max_projects',
        'storage_limit',
        'features',
        'is_active',
        'is_featured',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'annual_price' => 'decimal:2',
        'features' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    /**
     * 與租戶的關聯
     */
    public function tenants()
    {
        return $this->hasMany(Tenant::class, 'plan', 'slug');
    }

    /**
     * 取得方案的年度節省金額
     */
    public function getAnnualSavingsAttribute()
    {
        if (!$this->annual_price) {
            return 0;
        }
        return ($this->price * 12) - $this->annual_price;
    }

    /**
     * 取得方案的年度折扣百分比
     */
    public function getAnnualDiscountPercentageAttribute()
    {
        $savings = $this->annual_savings;
        if ($savings <= 0 || $this->price <= 0) {
            return 0;
        }
        return round(($savings / ($this->price * 12)) * 100);
    }

    /**
     * 取得功能列表（格式化）
     */
    public function getFormattedFeaturesAttribute()
    {
        if (!$this->features) {
            return [];
        }
        return $this->features;
    }

    /**
     * Scope: 只查詢啟用的方案
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: 依排序順序
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('price');
    }
}
