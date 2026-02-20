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
