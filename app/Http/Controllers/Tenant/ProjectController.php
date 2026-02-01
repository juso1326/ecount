<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Company;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Project::with(['company', 'department', 'manager', 'members', 'receivables', 'payables']);

        // 搜尋（包含客戶、部門名稱）
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhereHas('company', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('short_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('department', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                  });
            });
        }

        // 狀態篩選
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 公司篩選
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        // 部門篩選
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // 專案類型篩選
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $projects = $query->orderBy('start_date', 'desc')->paginate(15);
        
        // 計算每個專案的財務統計（使用已載入的關聯）
        $projects->getCollection()->transform(function ($project) {
            // 應收總額
            $project->total_receivable = $project->receivables ? $project->receivables->sum('amount') : 0;
            // 已收金額
            $project->total_received = $project->receivables ? $project->receivables->sum('received_amount') : 0;
            // 扣繳稅額
            $project->withholding_tax = $project->receivables ? $project->receivables->sum('withholding_tax') : 0;
            // 應付總額（專案支出）
            $project->total_payable = $project->payables ? $project->payables->sum('amount') : 0;
            // 已付金額
            $project->total_paid = $project->payables ? $project->payables->sum('paid_amount') : 0;
            // 累計收入（已收 - 扣繳 - 已付）
            $project->accumulated_income = $project->total_received - $project->withholding_tax - $project->total_paid;
            
            return $project;
        });

        if ($request->wantsJson()) {
            return response()->json([
                'data' => $projects->items(),
                'meta' => [
                    'total' => $projects->total(),
                    'per_page' => $projects->perPage(),
                    'current_page' => $projects->currentPage(),
                    'last_page' => $projects->lastPage(),
                ]
            ]);
        }

        return view('tenant.projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $companies = Company::where('is_active', true)->orderBy('name')->get();
        $departments = Department::where('is_active', true)->orderBy('sort_order')->get();
        $managers = User::orderBy('name')->get();

        return view('tenant.projects.create', compact('companies', 'departments', 'managers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:50|unique:projects,code',
            'name' => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
            'department_id' => 'nullable|exists:departments,id',
            'manager_id' => 'nullable|exists:users,id',
            'status' => 'required|in:planning,in_progress,on_hold,completed,cancelled',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'budget' => 'numeric|min:0',
            'actual_cost' => 'numeric|min:0',
            'description' => 'nullable|string',
            'note' => 'nullable|string',
            'is_active' => 'boolean',
        ], [
            'code.required' => '專案代碼為必填',
            'code.unique' => '專案代碼已存在',
            'name.required' => '專案名稱為必填',
            'company_id.required' => '所屬公司為必填',
            'company_id.exists' => '公司不存在',
            'department_id.exists' => '部門不存在',
            'manager_id.exists' => '專案經理不存在',
            'status.required' => '專案狀態為必填',
            'status.in' => '專案狀態不正確',
            'end_date.after_or_equal' => '結束日期必須晚於或等於開始日期',
            'budget.numeric' => '預算必須為數字',
            'actual_cost.numeric' => '實際成本必須為數字',
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

        $project = Project::create($request->all());

        if ($request->wantsJson()) {
            return response()->json([
                'message' => '專案建立成功',
                'data' => $project
            ], 201);
        }

        return redirect()->route('tenant.projects.index')
            ->with('success', '專案建立成功');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        $project->load([
            'company', 
            'department', 
            'manager', 
            'members.projects' => function($query) {
                $query->where('status', 'in_progress');
            },
            'receivables',
            'payables'
        ]);

        if (request()->wantsJson()) {
            return response()->json([
                'data' => $project
            ]);
        }

        return view('tenant.projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        $companies = Company::where('is_active', true)->orderBy('name')->get();
        $departments = Department::where('is_active', true)->orderBy('sort_order')->get();
        $managers = User::orderBy('name')->get();

        return view('tenant.projects.edit', compact('project', 'companies', 'departments', 'managers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:50|unique:projects,code,' . $project->id,
            'name' => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
            'department_id' => 'nullable|exists:departments,id',
            'manager_id' => 'nullable|exists:users,id',
            'status' => 'required|in:planning,in_progress,on_hold,completed,cancelled',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'budget' => 'numeric|min:0',
            'actual_cost' => 'numeric|min:0',
            'description' => 'nullable|string',
            'note' => 'nullable|string',
            'is_active' => 'boolean',
        ], [
            'code.required' => '專案代碼為必填',
            'code.unique' => '專案代碼已存在',
            'name.required' => '專案名稱為必填',
            'company_id.required' => '所屬公司為必填',
            'company_id.exists' => '公司不存在',
            'department_id.exists' => '部門不存在',
            'manager_id.exists' => '專案經理不存在',
            'status.required' => '專案狀態為必填',
            'status.in' => '專案狀態不正確',
            'end_date.after_or_equal' => '結束日期必須晚於或等於開始日期',
            'budget.numeric' => '預算必須為數字',
            'actual_cost.numeric' => '實際成本必須為數字',
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

        $project->update($request->all());

        if ($request->wantsJson()) {
            return response()->json([
                'message' => '專案更新成功',
                'data' => $project
            ]);
        }

        return redirect()->route('tenant.projects.index')
            ->with('success', '專案更新成功');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        // 檢查是否有關聯資料
        $hasReceivables = $project->receivables()->count() > 0;
        $hasPayables = $project->payables()->count() > 0;

        if ($hasReceivables || $hasPayables) {
            if (request()->wantsJson()) {
                return response()->json([
                    'message' => '此專案有應收或應付帳款，無法刪除'
                ], 422);
            }
            return back()->with('error', '此專案有應收或應付帳款，無法刪除');
        }

        $project->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'message' => '專案刪除成功'
            ]);
        }

        return redirect()->route('tenant.projects.index')
            ->with('success', '專案刪除成功');
    }
    
    /**
     * Add member to project
     */
    public function addMember(Request $request, Project $project)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'nullable|string|max:50',
        ]);
        
        // 檢查是否已經是成員
        if ($project->members()->where('user_id', $validated['user_id'])->exists()) {
            return back()->with('error', '該使用者已經是專案成員');
        }
        
        $project->members()->attach($validated['user_id'], [
            'role' => $validated['role'] ?? null,
            'joined_at' => now(),
        ]);
        
        return redirect()->route('tenant.projects.show', $project)->with('success', '成員新增成功');
    }
    
    /**
     * Remove member from project
     */
    public function removeMember(Project $project, User $user)
    {
        $project->members()->detach($user->id);
        
        return redirect()->route('tenant.projects.show', $project)->with('success', '成員移除成功');
    }
}
