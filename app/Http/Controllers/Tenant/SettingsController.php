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
     * Display system settings
     */
    public function system()
    {
        return view('tenant.settings.system');
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
     * Display code management settings
     */
    public function codes()
    {
        return view('tenant.settings.codes');
    }
    
    /**
     * Update code management settings
     */
    public function updateCodes(Request $request)
    {
        $validated = $request->validate([
            'company_code_prefix' => 'required|string|max:5',
            'company_code_length' => 'required|integer|min:1|max:10',
            'company_code_auto' => 'nullable|boolean',
            'department_code_prefix' => 'required|string|max:5',
            'department_code_length' => 'required|integer|min:1|max:10',
            'department_code_auto' => 'nullable|boolean',
            'project_code_prefix' => 'required|string|max:5',
            'project_code_length' => 'required|integer|min:1|max:10',
            'project_code_auto' => 'nullable|boolean',
        ]);
        
        foreach ($validated as $key => $value) {
            TenantSetting::set($key, $value ?? false);
        }
        
        return redirect()->route('tenant.settings.codes')
            ->with('success', '代碼設定已更新');
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

        return view('tenant.settings.financial', compact('currencies', 'closingDay', 'defaultCurrency'));
    }

    /**
     * Update financial settings
     */
    public function updateFinancial(Request $request)
    {
        $validated = $request->validate([
            'closing_day' => 'required|integer|min:1|max:31',
            'default_currency' => 'required|string|max:3',
        ], [
            'closing_day.required' => '請選擇關帳日',
            'closing_day.min' => '關帳日必須在 1-31 之間',
            'closing_day.max' => '關帳日必須在 1-31 之間',
            'default_currency.required' => '請選擇預設幣值',
        ]);

        foreach ($validated as $key => $value) {
            TenantSetting::set($key, $value);
        }

        return redirect()->route('tenant.settings.financial')
            ->with('success', '財務設定已更新');
    }
}
