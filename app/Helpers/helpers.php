<?php

use App\Helpers\CurrencyHelper;

if (!function_exists('format_currency')) {
    /**
     * 格式化貨幣顯示
     * 
     * @param float|int|null $amount
     * @param string|null $currency
     * @param bool $showSymbol
     * @return string
     */
    function format_currency($amount, ?string $currency = null, bool $showSymbol = true): string
    {
        return CurrencyHelper::format($amount, $currency, $showSymbol);
    }
}

if (!function_exists('currency_decimals')) {
    /**
     * 取得貨幣小數位數
     * 
     * @param string|null $currency
     * @return int
     */
    function currency_decimals(?string $currency = null): int
    {
        return CurrencyHelper::getDecimalPlaces($currency ?? 'TWD');
    }
}

if (!function_exists('format_date')) {
    /**
     * 格式化日期顯示（使用系統設定格式）
     * 
     * @param \Carbon\Carbon|\DateTime|string|null $date
     * @param string|null $format
     * @return string
     */
    function format_date($date, ?string $format = null): string
    {
        if (!$date) {
            return '-';
        }

        // 轉換為 Carbon 物件
        if (is_string($date)) {
            try {
                $date = \Carbon\Carbon::parse($date);
            } catch (\Exception $e) {
                return '-';
            }
        }

        // 使用指定格式或系統預設格式
        if ($format === null) {
            $format = 'Y.m.d'; // 預設格式
        }

        return $date->format($format);
    }
}

if (!function_exists('format_datetime')) {
    /**
     * 格式化日期時間顯示（使用系統設定格式）
     * 
     * @param \Carbon\Carbon|\DateTime|string|null $datetime
     * @param string|null $format
     * @return string
     */
    function format_datetime($datetime, ?string $format = null): string
    {
        if (!$datetime) {
            return '-';
        }

        // 轉換為 Carbon 物件
        if (is_string($datetime)) {
            try {
                $datetime = \Carbon\Carbon::parse($datetime);
            } catch (\Exception $e) {
                return '-';
            }
        }

        // 使用指定格式或系統預設格式
        if ($format === null) {
            $format = 'Y.m.d H:i'; // 預設格式
        }

        return $datetime->format($format);
    }
}
