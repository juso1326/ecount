<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Code;
use Illuminate\Http\Request;

class CodeController extends Controller
{
    /**
     * 代碼分類定義
     */
    const CATEGORIES = [
        // 原有分類
        'project_task' => '專案任務',
        'project_type' => '專案類型',
        'deduction_type' => '應扣類別',
        'payment_type' => '給付類別',
        
        // 從舊系統遷移的分類
        'department_category' => '部門類別',
        'expense_type' => '費用類別',
        'status_code' => '狀態代碼',
    ];

    /**
     * 代碼管理首頁
     */
    public function index()
    {
        $categories = self::CATEGORIES;
        $codeCounts = [];
        
        foreach ($categories as $key => $name) {
            $codeCounts[$key] = Code::where('category', $key)->count();
        }
        
        return view('tenant.codes.index', compact('categories', 'codeCounts'));
    }

    /**
     * 顯示指定分類的代碼列表
     */
    public function category(Request $request, string $category)
    {
        if (!array_key_exists($category, self::CATEGORIES)) {
            abort(404, '分類不存在');
        }

        $categoryName = self::CATEGORIES[$category];
        
        $query = Code::where('category', $category);
        
        // 搜尋
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }
        
        // 狀態篩選
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }
        
        $codes = $query->orderBy('sort_order')->paginate(20);
        
        return view('tenant.codes.category', compact('codes', 'category', 'categoryName'));
    }

    /**
     * 創建代碼表單
     */
    public function create(string $category)
    {
        if (!array_key_exists($category, self::CATEGORIES)) {
            abort(404, '分類不存在');
        }
        
        $categoryName = self::CATEGORIES[$category];
        
        // 取得下一個排序號
        $nextSortOrder = Code::where('category', $category)->max('sort_order') + 1;
        
        return view('tenant.codes.create', compact('category', 'categoryName', 'nextSortOrder'));
    }

    /**
     * 儲存新代碼
     */
    public function store(Request $request, string $category)
    {
        if (!array_key_exists($category, self::CATEGORIES)) {
            abort(404, '分類不存在');
        }
        
        $validated = $request->validate([
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
        ], [
            'code.required' => '代碼為必填',
            'code.max' => '代碼長度不可超過 50 字元',
            'name.required' => '名稱為必填',
            'name.max' => '名稱長度不可超過 255 字元',
            'sort_order.required' => '排序為必填',
            'sort_order.integer' => '排序必須為整數',
            'sort_order.min' => '排序必須大於等於 0',
        ]);
        
        // 檢查代碼是否重複
        $exists = Code::where('category', $category)
            ->where('code', $validated['code'])
            ->exists();
            
        if ($exists) {
            return back()->withErrors(['code' => '此代碼已存在'])->withInput();
        }
        
        $validated['category'] = $category;
        $validated['is_active'] = $request->has('is_active');
        $validated['created_by'] = auth()->id();
        $validated['updated_by'] = auth()->id();
        
        Code::create($validated);
        
        return redirect()
            ->route('tenant.codes.category', $category)
            ->with('success', '代碼新增成功');
    }

    /**
     * 編輯代碼表單
     */
    public function edit(string $category, Code $code)
    {
        if ($code->category !== $category) {
            abort(404);
        }
        
        $categoryName = self::CATEGORIES[$category];
        
        return view('tenant.codes.edit', compact('code', 'category', 'categoryName'));
    }

    /**
     * 更新代碼
     */
    public function update(Request $request, string $category, Code $code)
    {
        if ($code->category !== $category) {
            abort(404);
        }
        
        $validated = $request->validate([
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
        ], [
            'code.required' => '代碼為必填',
            'code.max' => '代碼長度不可超過 50 字元',
            'name.required' => '名稱為必填',
            'name.max' => '名稱長度不可超過 255 字元',
            'sort_order.required' => '排序為必填',
            'sort_order.integer' => '排序必須為整數',
            'sort_order.min' => '排序必須大於等於 0',
        ]);
        
        // 檢查代碼是否重複（排除自己）
        $exists = Code::where('category', $category)
            ->where('code', $validated['code'])
            ->where('id', '!=', $code->id)
            ->exists();
            
        if ($exists) {
            return back()->withErrors(['code' => '此代碼已存在'])->withInput();
        }
        
        $validated['is_active'] = $request->has('is_active');
        $validated['updated_by'] = auth()->id();
        
        $code->update($validated);
        
        return redirect()
            ->route('tenant.codes.category', $category)
            ->with('success', '代碼更新成功');
    }

    /**
     * 刪除代碼
     */
    public function destroy(string $category, Code $code)
    {
        if ($code->category !== $category) {
            abort(404);
        }
        
        $code->delete();
        
        return redirect()
            ->route('tenant.codes.category', $category)
            ->with('success', '代碼刪除成功');
    }

    /**
     * 批次更新排序
     */
    public function updateSort(Request $request, string $category)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|integer|exists:codes,id',
            'items.*.sort_order' => 'required|integer|min:0',
        ]);
        
        foreach ($validated['items'] as $item) {
            Code::where('id', $item['id'])
                ->where('category', $category)
                ->update(['sort_order' => $item['sort_order']]);
        }
        
        return response()->json(['success' => true, 'message' => '排序更新成功']);
    }
}
