<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Company;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     * 按照舊系統 PRJ01 邏輯重寫
     */
    public function index(Request $request)
    {
        $query = Project::with(['company', 'department', 'manager', 'members']);

        // 智能搜尋（支援專案名稱/代碼/成員/負責人/發票號/報價單號）
        if ($request->filled('smart_search')) {
            $query->smartSearch($request->smart_search);
        }

        // 日期範圍篩選（預設最近一年）
        $dateStart = $request->input('date_start', now()->subYear()->format('Y-m-d'));
        $dateEnd = $request->input('date_end', now()->format('Y-m-d'));
        
        if (!$request->filled('smart_search')) {
            // 只在沒有使用智能搜尋時才套用預設日期範圍
            $query->dateRange($dateStart, $dateEnd);
        }

        // 專案類型篩選
        if ($request->filled('project_type')) {
            $query->where('project_type', $request->project_type);
        }
        
        // 專案狀態篩選
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // 預設排除已取消專案（status != 'cancelled'）
            $query->where('status', '!=', Project::STATUS_CANCELLED);
        }

        // 公司篩選
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        // 排序
        $orderBy = $request->input('order_by', 'start_date');
        $orderDir = $request->input('order_dir', 'desc');
        $query->orderBy($orderBy, $orderDir);

        $projects = $query->paginate(15);
        
        // 計算財務統計（按舊系統邏輯）
        $projects->getCollection()->transform(function ($project) {
            // 應收總額和扣繳（從應收帳款）
            $receivableSum = DB::table('receivables')
                ->where('project_id', $project->id)
                ->selectRaw('
                    SUM(amount) as total_receivable,
                    SUM(received_amount) as total_received,
                    SUM(withholding_tax) as total_withholding_tax
                ')
                ->first();
            
            // 應付總額和已付總額（從應付帳款）
            $payableSum = DB::table('payables')
                ->where('project_id', $project->id)
                ->selectRaw('
                    SUM(amount) as total_payable,
                    SUM(paid_amount) as total_paid
                ')
                ->first();
            
            $project->total_receivable = $receivableSum->total_receivable ?? 0;
            $project->total_received = $receivableSum->total_received ?? 0;
            $project->withholding_tax = $receivableSum->total_withholding_tax ?? 0;
            $project->total_payable = $payableSum->total_payable ?? 0;
            $project->total_paid = $payableSum->total_paid ?? 0;
            
            // 累計 = 已收 - 已付
            $project->accumulated_income = $project->total_received - $project->total_paid;
            
            // 成員列表（安全處理可能為 null 的情況）
            $project->member_names = $project->members ? $project->members->pluck('name')->implode(', ') : '';
            
            return $project;
        });

        // 計算總計
        $totals = [
            'total_receivable' => $projects->sum('total_receivable'),
            'withholding_tax' => $projects->sum('withholding_tax'),
            'total_payable' => $projects->sum('total_payable'),
            'accumulated_income' => $projects->sum('accumulated_income'),
        ];

        return view('tenant.projects.index', compact('projects', 'totals', 'dateStart', 'dateEnd'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $companies = Company::where('is_active', true)->orderBy('name')->get();
        $managers = User::where('is_active', true)->orderBy('name')->get();
        
        // 取得已有的專案類型（去重）
        $projectTypes = Project::whereNotNull('project_type')
            ->where('project_type', '!=', '')
            ->distinct()
            ->pluck('project_type')
            ->filter()
            ->values();
        
        // 自動生成專案代碼
        $nextCode = $this->generateProjectCode();

        return view('tenant.projects.create', compact('companies', 'managers', 'projectTypes', 'nextCode'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'nullable|string|max:50|unique:projects,code',
            'name' => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
            'department_id' => 'nullable|exists:departments,id',
            'manager_id' => 'nullable|exists:users,id',
            'project_type' => 'nullable|string',
            'status' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'budget' => 'nullable|numeric|min:0',
            'actual_cost' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'quote_no' => 'nullable|string',
            'note' => 'nullable|string',
        ]);

        // 如果沒有提供代碼，自動生成
        if (empty($validated['code'])) {
            $validated['code'] = $this->generateProjectCode();
        }

        $project = Project::create($validated);

        return redirect()->route('tenant.projects.index')
            ->with('success', '專案新增成功');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        $project->load(['company', 'department', 'manager', 'members', 'receivables', 'payables']);

        // 格式化資料供 JavaScript 使用
        $receivablesData = $project->receivables->map(function($r) {
            return [
                'id' => $r->id,
                'receipt_date' => $r->receipt_date?->format('Y-m-d'),
                'amount' => $r->amount,
                'content' => $r->content,
                'due_date' => $r->due_date?->format('Y-m-d'),
                'invoice_no' => $r->invoice_no,
                'note' => $r->note,
            ];
        });

        $payablesData = $project->payables->map(function($p) {
            return [
                'id' => $p->id,
                'payment_date' => $p->payment_date?->format('Y-m-d'),
                'amount' => $p->amount,
                'vendor' => $p->vendor,
                'content' => $p->content,
                'note' => $p->note,
            ];
        });

        return view('tenant.projects.show', compact('project', 'receivablesData', 'payablesData'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        $companies = Company::where('is_active', true)->orderBy('name')->get();
        $managers = User::where('is_active', true)->orderBy('name')->get();
        
        // 取得已有的專案類型（去重）
        $projectTypes = Project::whereNotNull('project_type')
            ->where('project_type', '!=', '')
            ->distinct()
            ->pluck('project_type')
            ->filter()
            ->values();

        return view('tenant.projects.edit', compact('project', 'companies', 'managers', 'projectTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
            'manager_id' => 'nullable|exists:users,id',
            'project_type' => 'nullable|string',
            'start_date' => 'nullable|date',
            'budget' => 'nullable|numeric|min:0',
            'quote_no' => 'nullable|string',
        ]);

        $project->update($validated);

        return redirect()->route('tenant.projects.show', $project)
            ->with('success', '專案更新成功');
    }

    /**
     * 快速更新專案（狀態、執行日期、結束日期、備註）
     */
    public function quickUpdate(Request $request, Project $project)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:planning,in_progress,on_hold,completed,cancelled',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'note' => 'nullable|string',
        ]);

        $project->update($validated);

        return redirect()->route('tenant.projects.show', $project)
            ->with('success', '專案更新成功');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        // 檢查是否有關聯資料
        if ($project->receivables()->count() > 0 || $project->payables()->count() > 0) {
            return back()->with('error', '此專案有財務資料，無法刪除');
        }

        $project->delete();

        return redirect()->route('tenant.projects.index')
            ->with('success', '專案刪除成功');
    }
    
    /**
     * 新增成員到專案
     */
    public function addMember(Request $request, Project $project)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);
        
        if ($project->members()->where('user_id', $validated['user_id'])->exists()) {
            return back()->with('error', '該使用者已經是專案成員');
        }
        
        $project->members()->attach($validated['user_id'], [
            'joined_at' => now(),
        ]);
        
        return redirect()->route('tenant.projects.show', $project)
            ->with('success', '成員新增成功');
    }
    
    /**
     * 從專案移除成員
     */
    public function removeMember(Project $project, User $user)
    {
        $project->members()->detach($user->id);
        
        return redirect()->route('tenant.projects.show', $project)
            ->with('success', '成員移除成功');
    }
    
    /**
     * 快速新增應收帳款
     */
    public function quickAddReceivable(Request $request, Project $project)
    {
        $validated = $request->validate([
            'receipt_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:receipt_date',
            'amount' => 'required|numeric|min:0',
            'content' => 'nullable|string',
            'invoice_no' => 'nullable|string',
            'note' => 'nullable|string',
        ]);
        
        // 自動生成收款編號
        $lastReceivable = \App\Models\Receivable::withTrashed()
            ->where('receipt_no', 'like', 'REC-%')
            ->orderByRaw('CAST(SUBSTRING(receipt_no, 5) AS UNSIGNED) DESC')
            ->first();
        
        if ($lastReceivable) {
            preg_match('/REC-(\d+)/', $lastReceivable->receipt_no, $matches);
            $nextNumber = isset($matches[1]) ? intval($matches[1]) + 1 : 1;
        } else {
            $nextNumber = 1;
        }
        
        $validated['receipt_no'] = 'REC-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
        $validated['project_id'] = $project->id;
        $validated['company_id'] = $project->company_id;
        $validated['status'] = \App\Models\Receivable::STATUS_UNPAID;
        $validated['received_amount'] = 0;
        $validated['responsible_user_id'] = auth()->id();
        
        \App\Models\Receivable::create($validated);
        
        return redirect()->route('tenant.projects.show', $project)
            ->with('success', '應收帳款新增成功');
    }
    
    /**
     * 快速新增應付帳款
     */
    public function quickAddPayable(Request $request, Project $project)
    {
        $validated = $request->validate([
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'vendor' => 'nullable|string',
            'content' => 'nullable|string',
            'note' => 'nullable|string',
        ]);
        
        // 自動生成付款編號
        $lastPayable = \App\Models\Payable::withTrashed()
            ->where('payment_no', 'like', 'PAY-%')
            ->orderByRaw('CAST(SUBSTRING(payment_no, 5) AS UNSIGNED) DESC')
            ->first();
        
        if ($lastPayable) {
            preg_match('/PAY-(\d+)/', $lastPayable->payment_no, $matches);
            $nextNumber = isset($matches[1]) ? intval($matches[1]) + 1 : 1;
        } else {
            $nextNumber = 1;
        }
        
        $validated['payment_no'] = 'PAY-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
        $validated['project_id'] = $project->id;
        $validated['company_id'] = $project->company_id;
        $validated['status'] = \App\Models\Payable::STATUS_UNPAID;
        $validated['paid_amount'] = 0;
        $validated['responsible_user_id'] = auth()->id();
        
        \App\Models\Payable::create($validated);
        
        return redirect()->route('tenant.projects.show', $project)
            ->with('success', '應付帳款新增成功');
    }
    
    /**
     * 自動生成專案代碼
     */
    private function generateProjectCode(): string
    {
        // 取得最新的專案代碼
        $lastProject = Project::withTrashed()
            ->where('code', 'like', 'PRJ-%')
            ->orderByRaw('CAST(SUBSTRING(code, 5) AS UNSIGNED) DESC')
            ->first();

        if ($lastProject) {
            // 從最後一個代碼提取數字並加1
            preg_match('/PRJ-(\d+)/', $lastProject->code, $matches);
            $nextNumber = isset($matches[1]) ? intval($matches[1]) + 1 : 1;
        } else {
            $nextNumber = 1;
        }

        return 'PRJ-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }
}
