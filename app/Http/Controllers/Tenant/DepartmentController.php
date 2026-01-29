<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Department::with(['parent', 'manager']);

        // 搜尋
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // 狀態篩選
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // 上層部門篩選
        if ($request->filled('parent_id')) {
            $query->where('parent_id', $request->parent_id);
        }

        $departments = $query->orderBy('sort_order')->orderBy('created_at', 'desc')->paginate(15);

        if ($request->wantsJson()) {
            return response()->json([
                'data' => $departments->items(),
                'meta' => [
                    'total' => $departments->total(),
                    'per_page' => $departments->perPage(),
                    'current_page' => $departments->currentPage(),
                    'last_page' => $departments->lastPage(),
                ]
            ]);
        }

        return view('tenant.departments.index', compact('departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $parentDepartments = Department::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
        
        $managers = User::orderBy('name')->get();

        return view('tenant.departments.create', compact('parentDepartments', 'managers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:50|unique:departments,code',
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:departments,id',
            'type' => 'nullable|string|max:50',
            'manager_id' => 'nullable|exists:users,id',
            'sort_order' => 'integer|min:0',
            'note' => 'nullable|string',
            'is_active' => 'boolean',
        ], [
            'code.required' => '部門代碼為必填',
            'code.unique' => '部門代碼已存在',
            'name.required' => '部門名稱為必填',
            'parent_id.exists' => '上層部門不存在',
            'manager_id.exists' => '部門主管不存在',
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => '驗證失敗',
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $department = Department::create($request->all());

        if ($request->wantsJson()) {
            return response()->json([
                'message' => '部門建立成功',
                'data' => $department
            ], 201);
        }

        return redirect()->route('tenant.departments.index')
            ->with('success', '部門建立成功');
    }

    /**
     * Display the specified resource.
     */
    public function show(Department $department)
    {
        $department->load(['parent', 'children', 'manager', 'projects']);

        if (request()->wantsJson()) {
            return response()->json([
                'data' => $department
            ]);
        }

        return view('tenant.departments.show', compact('department'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Department $department)
    {
        $parentDepartments = Department::where('is_active', true)
            ->where('id', '!=', $department->id) // 排除自己
            ->orderBy('sort_order')
            ->get();
        
        $managers = User::orderBy('name')->get();

        return view('tenant.departments.edit', compact('department', 'parentDepartments', 'managers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Department $department)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:50|unique:departments,code,' . $department->id,
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:departments,id',
            'type' => 'nullable|string|max:50',
            'manager_id' => 'nullable|exists:users,id',
            'sort_order' => 'integer|min:0',
            'note' => 'nullable|string',
            'is_active' => 'boolean',
        ], [
            'code.required' => '部門代碼為必填',
            'code.unique' => '部門代碼已存在',
            'name.required' => '部門名稱為必填',
            'parent_id.exists' => '上層部門不存在',
            'manager_id.exists' => '部門主管不存在',
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => '驗證失敗',
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        // 防止設置自己為上層部門
        if ($request->parent_id == $department->id) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => '不能將自己設為上層部門'
                ], 422);
            }
            return back()->withErrors(['parent_id' => '不能將自己設為上層部門'])->withInput();
        }

        $department->update($request->all());

        if ($request->wantsJson()) {
            return response()->json([
                'message' => '部門更新成功',
                'data' => $department
            ]);
        }

        return redirect()->route('tenant.departments.index')
            ->with('success', '部門更新成功');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        // 檢查是否有下層部門
        if ($department->children()->count() > 0) {
            if (request()->wantsJson()) {
                return response()->json([
                    'message' => '此部門有下層部門，無法刪除'
                ], 422);
            }
            return back()->with('error', '此部門有下層部門，無法刪除');
        }

        $department->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'message' => '部門刪除成功'
            ]);
        }

        return redirect()->route('tenant.departments.index')
            ->with('success', '部門刪除成功');
    }
}
