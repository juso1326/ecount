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
     * Default project statuses (fallback when no tags exist)
     */
    public static function defaultProjectStatuses(): array
    {
        return [
            ['value' => '1', 'label' => '新成立', 'color' => '#94A3B8'],
            ['value' => '2', 'label' => '提案',   'color' => '#A855F7'],
            ['value' => '3', 'label' => '進行中', 'color' => '#3B82F6'],
            ['value' => '4', 'label' => '結案',   'color' => '#22C55E'],
            ['value' => '5', 'label' => '請款中', 'color' => '#F97316'],
            ['value' => '6', 'label' => '已入帳', 'color' => '#10B981'],
            ['value' => '7', 'label' => '待發票', 'color' => '#EAB308'],
        ];
    }

    /**
     * Get project statuses from Tag model (type='project_status'), falls back to defaults
     */
    public static function getProjectStatuses(): array
    {
        $tags = \App\Models\Tag::where('type', \App\Models\Tag::TYPE_PROJECT_STATUS)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        if ($tags->isNotEmpty()) {
            return $tags->map(fn($t) => [
                'value' => (string)$t->id,
                'label' => $t->name,
                'color' => $t->color,
            ])->toArray();
        }

        return self::defaultProjectStatuses();
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
            'date_format' => 'required|in:Y-m-d,Y/m/d,Y.m.d,m/d/Y,d/m/Y,Ymd',
            'time_format' => 'required|in:H:i:s,H:i,h:i:s A,h:i A',
            'timezone' => 'required|string',
        ]);
        
        TenantSetting::set('date_format', $validated['date_format'], 'system', 'string');
        TenantSetting::set('time_format', $validated['time_format'], 'system', 'string');
        TenantSetting::set('timezone', $validated['timezone'], 'system', 'string');
        
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
            'currency_decimal_places' => 'nullable|integer|min:0|max:4',
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
            TenantSetting::set($key, $value, 'financial', in_array($key, ['closing_day', 'fiscal_year_start', 'default_fiscal_year', 'currency_decimal_places']) ? 'number' : 'string');
        }

        return redirect()->route('tenant.settings.financial')
            ->with('success', '財務設定已更新');
    }
}
