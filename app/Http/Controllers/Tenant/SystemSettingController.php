<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SystemSettingController extends Controller
{
    /**
     * 顯示系統設定頁面
     */
    public function index()
    {
        $settings = $this->getSystemSettings();
        
        $timezones = \DateTimeZone::listIdentifiers();
        $currencies = config('currencies', [
            'TWD' => '新台幣 (TWD)',
            'USD' => '美元 (USD)',
            'CNY' => '人民幣 (CNY)',
            'JPY' => '日圓 (JPY)',
            'EUR' => '歐元 (EUR)',
        ]);

        // 展開設定供視圖使用
        $dateFormat = $settings['date_format'] ?? 'Y-m-d';
        $timeFormat = $settings['time_format'] ?? 'H:i';
        $timezone = $settings['timezone'] ?? 'Asia/Taipei';
        $locale = $settings['locale'] ?? 'zh_TW';
        $currency = $settings['currency'] ?? 'TWD';
        $fiscalYearStart = $settings['fiscal_year_start'] ?? 1;

        return view('tenant.settings.system', compact(
            'settings', 
            'timezones', 
            'currencies',
            'dateFormat',
            'timeFormat',
            'timezone',
            'locale',
            'currency',
            'fiscalYearStart'
        ));
    }

    /**
     * 更新系統設定
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'locale' => 'required|in:zh_TW,en,zh_CN',
            'timezone' => 'required|string',
            'currency' => 'required|string|max:3',
            'date_format' => 'required|in:Y-m-d,Y/m/d,Y.m.d,m/d/Y,d/m/Y,Ymd',
            'time_format' => 'required|in:H:i:s,H:i,h:i:s A,h:i A',
            'fiscal_year_start' => 'required|integer|min:1|max:12',
        ], [
            'locale.required' => '請選擇語言',
            'timezone.required' => '請選擇時區',
            'currency.required' => '請選擇貨幣',
            'date_format.required' => '請選擇日期格式',
            'time_format.required' => '請選擇時間格式',
            'fiscal_year_start.required' => '請選擇會計年度開始月份',
        ]);

        // 儲存到快取或資料庫
        Cache::put('tenant_settings_' . tenant('id'), $validated, now()->addYear());

        return back()->with('success', '系統設定已更新');
    }

    /**
     * 獲取系統設定
     */
    private function getSystemSettings()
    {
        return Cache::get('tenant_settings_' . tenant('id'), [
            'locale' => 'zh_TW',
            'timezone' => 'Asia/Taipei',
            'currency' => 'TWD',
            'date_format' => 'Y-m-d',
            'time_format' => 'H:i',
            'fiscal_year_start' => 1,
        ]);
    }
}
