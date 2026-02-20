<?php

namespace App\Helpers;

class CurrencyHelper
{
    /**
     * 格式化金額顯示（根據貨幣自動決定小數位數）
     * 
     * @param float|int|null $amount 金額
     * @param string|null $currency 貨幣代碼 (TWD, USD, EUR等)
     * @param bool $showSymbol 是否顯示貨幣符號
     * @return string
     */
    public static function format($amount, ?string $currency = null, bool $showSymbol = true): string
    {
        // 預設使用系統設定的貨幣
        if ($currency === null) {
            $currency = self::getSystemCurrency();
        }

        // 金額為空時返回 0
        if ($amount === null) {
            $amount = 0;
        }

        // 根據貨幣決定小數位數
        $decimals = self::getDecimalPlaces($currency);

        // 格式化金額
        $formatted = number_format((float)$amount, $decimals);

        // 添加貨幣符號
        if ($showSymbol) {
            $symbol = self::getCurrencySymbol($currency);
            return $symbol . ' ' . $formatted;
        }

        return $formatted;
    }

    /**
     * 取得貨幣的小數位數
     * 
     * @param string $currency
     * @return int
     */
    public static function getDecimalPlaces(string $currency): int
    {
        // 不使用小數點的貨幣
        $zeroDecimalCurrencies = [
            'TWD', // 新台幣
            'JPY', // 日圓
            'KRW', // 韓圜
            'VND', // 越南盾
            'IDR', // 印尼盾
        ];

        return in_array(strtoupper($currency), $zeroDecimalCurrencies) ? 0 : 2;
    }

    /**
     * 取得貨幣符號
     * 
     * @param string $currency
     * @return string
     */
    public static function getCurrencySymbol(string $currency): string
    {
        $symbols = [
            'TWD' => 'NT$',
            'USD' => 'US$',
            'EUR' => '€',
            'GBP' => '£',
            'JPY' => '¥',
            'KRW' => '₩',
            'CNY' => 'CN¥',
            'HKD' => 'HK$',
        ];

        return $symbols[strtoupper($currency)] ?? strtoupper($currency);
    }

    /**
     * 取得系統設定的貨幣
     * 
     * @return string
     */
    private static function getSystemCurrency(): string
    {
        // 預設使用台幣，未來可從系統設定擴展
        return 'TWD';
    }

    /**
     * JavaScript 格式化函數（用於前端）
     * 
     * @param string|null $currency
     * @return string
     */
    public static function jsFormatter(?string $currency = null): string
    {
        if ($currency === null) {
            $currency = self::getSystemCurrency();
        }

        $decimals = self::getDecimalPlaces($currency);
        $symbol = self::getCurrencySymbol($currency);

        return <<<JS
function formatCurrency(amount) {
    if (amount === null || amount === undefined) {
        amount = 0;
    }
    return '{$symbol} ' + parseFloat(amount).toLocaleString('zh-TW', {
        minimumFractionDigits: {$decimals},
        maximumFractionDigits: {$decimals}
    });
}
JS;
    }
}
