<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 程式碼表 Model
 * 用於系統中各種分類碼、下拉選單等
 */
class Code extends Model
{
    protected $fillable = [
        'category',
        'code',
        'name',
        'sort_order',
        'is_active',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * 取得指定分類的所有程式碼
     */
    public static function getByCategory(string $category, bool $activeOnly = true)
    {
        $query = self::where('category', $category)->orderBy('sort_order');
        
        if ($activeOnly) {
            $query->where('is_active', true);
        }
        
        return $query->get();
    }

    /**
     * 取得程式碼選項（用於下拉選單）
     */
    public static function getOptions(string $category): array
    {
        return self::getByCategory($category)
            ->pluck('name', 'code')
            ->toArray();
    }
}
