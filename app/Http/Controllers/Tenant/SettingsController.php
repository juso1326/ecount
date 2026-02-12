<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\TenantSetting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Display settings page
     */
    public function index()
    {
        $settings = TenantSetting::orderBy('group')->orderBy('label')->get()->groupBy('group');
        
        return view('tenant.settings.index', compact('settings'));
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        $this->authorize('settings.manage');

        foreach ($request->except('_token', '_method') as $key => $value) {
            TenantSetting::set($key, $value ?? '');
        }

        return redirect()->route('tenant.settings.index')
            ->with('success', '設定已更新');
    }

    /**
     * Display company settings
     */
    public function company()
    {
        $settings = TenantSetting::where('group', 'company')->orderBy('label')->get();
        
        return view('tenant.settings.company', compact('settings'));
    }

    /**
     * Update company settings
     */
    public function updateCompany(Request $request)
    {
        foreach ($request->except('_token', '_method') as $key => $value) {
            TenantSetting::set($key, $value ?? '');
        }

        return redirect()->route('tenant.settings.company')
            ->with('success', '公司設定已更新');
    }

    /**
     * Display system settings
     */
    public function system()
    {
        $dateFormat = TenantSetting::get('date_format', 'Y-m-d');
        $timeFormat = TenantSetting::get('time_format', 'H:i:s');
        $timezone = TenantSetting::get('timezone', 'Asia/Taipei');
        
        return view('tenant.settings.system', compact('dateFormat', 'timeFormat', 'timezone'));
    }
    
    /**
     * Update system settings
     */
    public function updateSystem(Request $request)
    {
        $validated = $request->validate([
            'date_format' => 'required|string',
            'time_format' => 'required|string',
            'timezone' => 'required|string',
        ]);
        
        foreach ($validated as $key => $value) {
            TenantSetting::set($key, $value);
        }
        
        return redirect()->route('tenant.settings.system')
            ->with('success', '系統設定已更新');
    }

    /**
     * Display account settings
     */
    public function account()
    {
        $user = auth()->user();
        
        return view('tenant.settings.account', compact('user'));
    }

    /**
     * Update account settings
     */
    public function updateAccount(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        
        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }

        $user->save();

        return redirect()->route('tenant.settings.account')
            ->with('success', '帳號設定已更新');
    }
    
    
    /**
     * Display financial settings
     */
    public function financial()
    {
        $currencies = [
            'TWD' => '新台幣 (TWD)',
            'USD' => '美元 (USD)',
            'CNY' => '人民幣 (CNY)',
            'JPY' => '日圓 (JPY)',
            'EUR' => '歐元 (EUR)',
            'GBP' => '英鎊 (GBP)',
            'HKD' => '港幣 (HKD)',
        ];

        $closingDay = TenantSetting::get('closing_day', 1);
        $defaultCurrency = TenantSetting::get('default_currency', 'TWD');
        $fiscalYearStart = TenantSetting::get('fiscal_year_start', 1);
        $defaultFiscalYear = TenantSetting::get('default_fiscal_year', date('Y'));

        return view('tenant.settings.financial', compact('currencies', 'closingDay', 'defaultCurrency', 'fiscalYearStart', 'defaultFiscalYear'));
    }

    /**
     * Update financial settings
     */
    public function updateFinancial(Request $request)
    {
        $validated = $request->validate([
            'closing_day' => 'required|integer|min:1|max:31',
            'default_currency' => 'required|string|max:3',
            'fiscal_year_start' => 'required|integer|min:1|max:12',
            'default_fiscal_year' => 'required|integer|min:2000|max:2100',
        ], [
            'closing_day.required' => '請選擇關帳日',
            'closing_day.min' => '關帳日必須在 1-31 之間',
            'closing_day.max' => '關帳日必須在 1-31 之間',
            'default_currency.required' => '請選擇預設幣值',
            'fiscal_year_start.required' => '請選擇會計年度起始月份',
            'fiscal_year_start.min' => '月份必須在 1-12 之間',
            'fiscal_year_start.max' => '月份必須在 1-12 之間',
            'default_fiscal_year.required' => '請輸入預設帳務年度',
            'default_fiscal_year.min' => '年度必須在 2000-2100 之間',
            'default_fiscal_year.max' => '年度必須在 2000-2100 之間',
        ]);

        foreach ($validated as $key => $value) {
            TenantSetting::set($key, $value);
        }

        return redirect()->route('tenant.settings.financial')
            ->with('success', '財務設定已更新');
    }
}
