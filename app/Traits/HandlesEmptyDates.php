<?php

namespace App\Traits;

/**
 * 處理空字串日期欄位
 * 
 * 自動將空字串 ('') 轉換為 null，避免 MySQL 日期欄位錯誤
 * 適用於所有標記為 date 或 datetime 的欄位
 */
trait HandlesEmptyDates
{
    protected static function bootHandlesEmptyDates()
    {
        static::saving(function ($model) {
            // 取得所有已變更的屬性
            foreach ($model->getDirty() as $field => $value) {
                // 如果值為空字串，檢查是否為日期欄位
                if ($value === '') {
                    $casts = $model->getCasts();
                    // 檢查該欄位是否為日期類型
                    if (isset($casts[$field]) && in_array($casts[$field], ['date', 'datetime', 'timestamp'])) {
                        $model->$field = null;
                    }
                }
            }
        });
    }
}
