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

if (!function_exists('get_tenant_date_format')) {
    /**
     * 取得租戶的日期格式設定
     */
    function get_tenant_date_format(): string
    {
        static $format = null;
        if ($format === null) {
            try {
                $format = \App\Models\TenantSetting::get('date_format', 'Y-m-d');
            } catch (\Exception $e) {
                $format = 'Y-m-d';
            }
        }
        return $format;
    }
}

if (!function_exists('get_tenant_time_format')) {
    /**
     * 取得租戶的時間格式設定
     */
    function get_tenant_time_format(): string
    {
        static $format = null;
        if ($format === null) {
            try {
                $format = \App\Models\TenantSetting::get('time_format', 'H:i');
            } catch (\Exception $e) {
                $format = 'H:i';
            }
        }
        return $format;
    }
}

if (!function_exists('format_date')) {
    /**
     * 格式化日期顯示（使用系統設定格式）
     */
    function format_date($date, ?string $format = null): string
    {
        if (!$date) {
            return '-';
        }

        if (is_string($date)) {
            try {
                $date = \Carbon\Carbon::parse($date);
            } catch (\Exception $e) {
                return '-';
            }
        }

        if ($format === null) {
            $format = get_tenant_date_format();
        }

        return $date->format($format);
    }
}

if (!function_exists('format_datetime')) {
    /**
     * 格式化日期時間顯示（使用系統設定格式）
     */
    function format_datetime($datetime, ?string $format = null): string
    {
        if (!$datetime) {
            return '-';
        }

        if (is_string($datetime)) {
            try {
                $datetime = \Carbon\Carbon::parse($datetime);
            } catch (\Exception $e) {
                return '-';
            }
        }

        if ($format === null) {
            $dateFormat = get_tenant_date_format();
            $timeFormat = get_tenant_time_format();
            $format = $dateFormat . ' ' . $timeFormat;
        }

        return $datetime->format($format);
    }
}
