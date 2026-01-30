<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanySettingController extends Controller
{
    /**
     * 顯示公司設定頁面
     */
    public function index()
    {
        // 獲取第一個公司（或當前租戶的主要公司）
        $company = Company::firstOrCreate(
            ['code' => 'DEFAULT'],
            [
                'name' => '預設公司',
                'is_active' => true,
            ]
        );

        return view('tenant.settings.company', compact('company'));
    }

    /**
     * 更新公司設定
     */
    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            // 基本資訊
            'code' => 'required|string|max:50|unique:companies,code,' . $company->id,
            'name' => 'required|string|max:255',
            'short_name' => 'nullable|string|max:50',
            'type' => 'required|in:company,individual',
            'is_outsource' => 'boolean',
            
            // 稅務資訊
            'tax_id' => 'nullable|string|max:20',
            'is_tax_entity' => 'boolean',
            'invoice_title' => 'nullable|string|max:255',
            'invoice_type' => 'nullable|in:duplicate,triplicate',
            
            // 聯絡資訊
            'representative' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'fax' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
            
            // 線上資訊
            'website' => 'nullable|url|max:255',
            'facebook' => 'nullable|url|max:255',
            'line_id' => 'nullable|string|max:100',
            'instagram' => 'nullable|string|max:100',
            
            // 營業資訊
            'business_hours' => 'nullable|string|max:255',
            'industry' => 'nullable|string|max:100',
            'capital' => 'nullable|string|max:100',
            
            // 銀行資訊
            'bank_name' => 'nullable|string|max:100',
            'bank_branch' => 'nullable|string|max:100',
            'bank_account' => 'nullable|string|max:50',
            'bank_account_name' => 'nullable|string|max:100',
            
            // 品牌
            'brand_color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            
            // 其他
            'note' => 'nullable|string',
            'is_active' => 'boolean',
        ], [
            'code.required' => '公司代碼為必填',
            'code.unique' => '公司代碼已存在',
            'name.required' => '公司名稱為必填',
            'type.required' => '請選擇類型',
            'type.in' => '類型格式錯誤',
            'email.email' => 'Email 格式錯誤',
            'website.url' => '網址格式錯誤',
            'facebook.url' => 'Facebook 網址格式錯誤',
            'brand_color.regex' => '品牌色碼格式錯誤（例如：#3C50E0）',
            'logo.image' => 'Logo 必須為圖片',
            'logo.mimes' => 'Logo 格式僅支援 jpeg, png, jpg, gif',
            'logo.max' => 'Logo 檔案大小不可超過 2MB',
        ]);

        // 處理 Logo 上傳
        if ($request->hasFile('logo')) {
            // 刪除舊 Logo
            if ($company->logo_path) {
                Storage::disk('public')->delete($company->logo_path);
            }
            
            // 儲存新 Logo
            $path = $request->file('logo')->store('logos', 'public');
            $validated['logo_path'] = $path;
        }

        // 更新公司資料
        $company->update($validated);

        return back()->with('success', '公司設定已更新');
    }

    /**
     * 刪除 Logo
     */
    public function deleteLogo(Company $company)
    {
        if ($company->logo_path) {
            Storage::disk('public')->delete($company->logo_path);
            $company->update(['logo_path' => null]);
        }

        return back()->with('success', 'Logo 已刪除');
    }
}
