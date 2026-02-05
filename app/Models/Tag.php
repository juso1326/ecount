<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Tag extends Model
{
    protected $fillable = [
        'type',
        'name',
        'color',
        'description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * 標籤類型常數
     */
    const TYPE_PROJECT = 'project';
    const TYPE_COMPANY = 'company';
    const TYPE_USER = 'user';
    const TYPE_PAYMENT_METHOD = 'payment_method';

    /**
     * 取得所有可用的標籤類型
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_PROJECT => '專案標籤',
            self::TYPE_COMPANY => '客戶廠商標籤',
            self::TYPE_USER => '團隊成員標籤',
            self::TYPE_PAYMENT_METHOD => '付款方式',
        ];
    }

    /**
     * 關聯：專案
     */
    public function projects(): MorphToMany
    {
        return $this->morphedByMany(Project::class, 'taggable');
    }

    /**
     * 關聯：公司
     */
    public function companies(): MorphToMany
    {
        return $this->morphedByMany(Company::class, 'taggable');
    }

    /**
     * 關聯：使用者
     */
    public function users(): MorphToMany
    {
        return $this->morphedByMany(User::class, 'taggable');
    }

    /**
     * Scope: 依類型篩選
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

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
}
