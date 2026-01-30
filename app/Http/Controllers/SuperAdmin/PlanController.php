<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PlanController extends Controller
{
    /**
     * Display a listing of the plans.
     */
    public function index()
    {
        $plans = Plan::withCount('tenants')
            ->ordered()
            ->paginate(15);

        return view('superadmin.plans.index', compact('plans'));
    }

    /**
     * Show the form for creating a new plan.
     */
    public function create()
    {
        return view('superadmin.plans.create');
    }

    /**
     * Store a newly created plan in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:plans,name',
            'slug' => 'required|string|max:255|unique:plans,slug',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'annual_price' => 'nullable|numeric|min:0',
            'max_users' => 'nullable|integer|min:1',
            'max_companies' => 'nullable|integer|min:1',
            'max_projects' => 'nullable|integer|min:1',
            'storage_limit' => 'nullable|integer|min:1',
            'features' => 'nullable|array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'nullable|integer',
        ], [
            'name.required' => '方案名稱為必填',
            'name.unique' => '方案名稱已存在',
            'slug.required' => '方案代碼為必填',
            'slug.unique' => '方案代碼已存在',
            'price.required' => '月費價格為必填',
            'price.numeric' => '月費價格必須為數字',
        ]);

        // 處理 features
        if ($request->has('feature_list')) {
            $features = array_filter(array_map('trim', explode("\n", $request->feature_list)));
            $validated['features'] = $features;
        }

        $plan = Plan::create($validated);

        return redirect()
            ->route('superadmin.plans.show', $plan)
            ->with('success', '方案建立成功');
    }

    /**
     * Display the specified plan.
     */
    public function show(Plan $plan)
    {
        $plan->load(['tenants' => function ($query) {
            $query->latest()->take(10);
        }]);

        return view('superadmin.plans.show', compact('plan'));
    }

    /**
     * Show the form for editing the specified plan.
     */
    public function edit(Plan $plan)
    {
        return view('superadmin.plans.edit', compact('plan'));
    }

    /**
     * Update the specified plan in storage.
     */
    public function update(Request $request, Plan $plan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:plans,name,' . $plan->id,
            'slug' => 'required|string|max:255|unique:plans,slug,' . $plan->id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'annual_price' => 'nullable|numeric|min:0',
            'max_users' => 'nullable|integer|min:1',
            'max_companies' => 'nullable|integer|min:1',
            'max_projects' => 'nullable|integer|min:1',
            'storage_limit' => 'nullable|integer|min:1',
            'features' => 'nullable|array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'nullable|integer',
        ], [
            'name.required' => '方案名稱為必填',
            'name.unique' => '方案名稱已存在',
            'slug.required' => '方案代碼為必填',
            'slug.unique' => '方案代碼已存在',
            'price.required' => '月費價格為必填',
            'price.numeric' => '月費價格必須為數字',
        ]);

        // 處理 features
        if ($request->has('feature_list')) {
            $features = array_filter(array_map('trim', explode("\n", $request->feature_list)));
            $validated['features'] = $features;
        }

        $plan->update($validated);

        return redirect()
            ->route('superadmin.plans.show', $plan)
            ->with('success', '方案更新成功');
    }

    /**
     * Remove the specified plan from storage.
     */
    public function destroy(Plan $plan)
    {
        // 檢查是否有租戶使用此方案
        $tenantsCount = $plan->tenants()->count();
        
        if ($tenantsCount > 0) {
            return back()->with('error', "無法刪除方案，仍有 {$tenantsCount} 個租戶正在使用此方案");
        }

        $plan->delete();

        return redirect()
            ->route('superadmin.plans.index')
            ->with('success', '方案已刪除');
    }

    /**
     * Toggle plan active status.
     */
    public function toggleActive(Plan $plan)
    {
        $plan->update(['is_active' => !$plan->is_active]);

        $status = $plan->is_active ? '啟用' : '停用';
        
        return back()->with('success', "方案已{$status}");
    }
}
