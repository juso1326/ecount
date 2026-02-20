<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\TaxSetting;
use Illuminate\Http\Request;

class TaxSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $taxSettings = TaxSetting::ordered()->get();

        return view('tenant.tax-settings.index', compact('taxSettings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tenant.tax-settings.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0|max:100',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ], [
            'name.required' => '請輸入稅款名稱',
            'rate.required' => '請輸入稅率',
            'rate.min' => '稅率不能小於 0',
            'rate.max' => '稅率不能大於 100',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['is_default'] = $request->has('is_default');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        // 如果設為預設，取消其他預設
        if ($validated['is_default']) {
            TaxSetting::where('is_default', true)->update(['is_default' => false]);
        }

        TaxSetting::create($validated);

        return redirect()
            ->route('tenant.tax-settings.index')
            ->with('success', '稅款設定已新增');
    }

    /**
     * Display the specified resource.
     */
    public function show(TaxSetting $taxSetting)
    {
        return view('tenant.tax-settings.show', compact('taxSetting'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TaxSetting $taxSetting)
    {
        return view('tenant.tax-settings.edit', compact('taxSetting'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TaxSetting $taxSetting)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0|max:100',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['is_default'] = $request->has('is_default');
        $validated['sort_order'] = $validated['sort_order'] ?? $taxSetting->sort_order;

        // 如果設為預設，取消其他預設
        if ($validated['is_default'] && !$taxSetting->is_default) {
            TaxSetting::where('is_default', true)->update(['is_default' => false]);
        }

        $taxSetting->update($validated);

        return redirect()
            ->route('tenant.tax-settings.index')
            ->with('success', '稅款設定已更新');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TaxSetting $taxSetting)
    {
        if ($taxSetting->is_default) {
            return back()->with('error', '無法刪除預設稅率設定');
        }

        $taxSetting->delete();

        return redirect()
            ->route('tenant.tax-settings.index')
            ->with('success', '稅款設定已刪除');
    }

    /**
     * Set as default tax setting
     */
    public function setDefault(TaxSetting $taxSetting)
    {
        $taxSetting->setAsDefault();

        return back()->with('success', '已設為預設稅率');
    }
}
