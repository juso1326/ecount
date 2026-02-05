<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = ExpenseCategory::with('parent')
            ->topLevel()
            ->ordered()
            ->get();

        return view('tenant.expense-categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $parentCategories = ExpenseCategory::topLevel()->active()->ordered()->get();
        
        return view('tenant.expense-categories.create', compact('parentCategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'parent_id' => 'nullable|exists:expense_categories,id',
            'code' => 'required|string|max:50|unique:expense_categories,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ], [
            'code.required' => '請輸入支出項目代碼',
            'code.unique' => '此代碼已被使用',
            'name.required' => '請輸入支出項目名稱',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        ExpenseCategory::create($validated);

        return redirect()
            ->route('tenant.expense-categories.index')
            ->with('success', '支出項目已新增');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ExpenseCategory $expenseCategory)
    {
        $parentCategories = ExpenseCategory::topLevel()
            ->active()
            ->where('id', '!=', $expenseCategory->id)
            ->ordered()
            ->get();
        
        return view('tenant.expense-categories.edit', compact('expenseCategory', 'parentCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ExpenseCategory $expenseCategory)
    {
        $validated = $request->validate([
            'parent_id' => 'nullable|exists:expense_categories,id',
            'code' => 'required|string|max:50|unique:expense_categories,code,' . $expenseCategory->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? $expenseCategory->sort_order;

        $expenseCategory->update($validated);

        return redirect()
            ->route('tenant.expense-categories.index')
            ->with('success', '支出項目已更新');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ExpenseCategory $expenseCategory)
    {
        // 檢查是否有子分類
        if ($expenseCategory->children()->count() > 0) {
            return back()->with('error', '此分類下有子分類，無法刪除');
        }

        $expenseCategory->delete();

        return redirect()
            ->route('tenant.expense-categories.index')
            ->with('success', '支出項目已刪除');
    }
}
