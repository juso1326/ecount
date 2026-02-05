<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Payable;
use App\Models\Project;
use App\Models\Company;
use App\Models\User;
use App\Models\Tag;
use App\Models\TaxSetting;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayableController extends Controller
{
    /**
     * 應付帳款列表（按 PRJ02 邏輯）
     */
    public function index(Request $request)
    {
        $query = Payable::with(['project', 'company', 'responsibleUser']);

        // 日期範圍篩選（預設最近一年）
        $dateStart = $request->input('date_start', now()->subYear()->format('Y-m-d'));
        $dateEnd = $request->input('date_end', now()->format('Y-m-d'));
        
        if ($dateStart && $dateEnd) {
            $query->whereBetween('payment_date', [$dateStart, $dateEnd]);
        }

        // 搜尋（專案名稱、內容、發票號碼）
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('content', 'like', "%{$search}%")
                  ->orWhere('invoice_no', 'like', "%{$search}%")
                  ->orWhere('payment_no', 'like', "%{$search}%")
                  ->orWhereHas('project', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // 廠商/供應商篩選
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        // 付款類型篩選
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // 付款狀態篩選
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 排序
        $orderBy = $request->input('order_by', 'payment_date');
        $orderDir = $request->input('order_dir', 'desc');
        $query->orderBy($orderBy, $orderDir);

        $payables = $query->paginate(15);

        // 計算統計數據（基於當前篩選條件）
        $statsQuery = Payable::query();
        
        // 應用相同的篩選條件
        if ($dateStart && $dateEnd) {
            $statsQuery->whereBetween('payment_date', [$dateStart, $dateEnd]);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $statsQuery->where(function($q) use ($search) {
                $q->where('content', 'like', "%{$search}%")
                  ->orWhere('invoice_no', 'like', "%{$search}%")
                  ->orWhere('payment_no', 'like', "%{$search}%")
                  ->orWhereHas('project', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }
        if ($request->filled('company_id')) {
            $statsQuery->where('company_id', $request->company_id);
        }
        if ($request->filled('type')) {
            $statsQuery->where('type', $request->type);
        }
        if ($request->filled('status')) {
            $statsQuery->where('status', $request->status);
        }
        
        $stats = $statsQuery->selectRaw('
            SUM(amount) as total_amount,
            SUM(paid_amount) as total_paid
        ')->first();
        
        $totalAmount = $stats->total_amount ?? 0;
        $totalPaid = $stats->total_paid ?? 0;

        return view('tenant.payables.index', compact('payables', 'dateStart', 'dateEnd', 'totalAmount', 'totalPaid'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'payment_no' => 'nullable|string|max:50|unique:payables,payment_no',
            'project_id' => 'required|exists:projects,id',
            'company_id' => 'nullable|exists:companies,id',
            'responsible_user_id' => 'nullable|exists:users,id',
            'payee_type' => 'nullable|string|max:20',
            'payee_user_id' => 'nullable|exists:users,id',
            'payee_company_id' => 'nullable|exists:companies,id',
            'type' => 'required|string|max:50',
            'content' => 'nullable|string',
            'payment_date' => 'required|date',
            'invoice_date' => 'nullable|date',
            'invoice_no' => 'nullable|string|max:50',
            'due_date' => 'nullable|date',
            'amount' => 'required|numeric|min:0',
            'deduction' => 'nullable|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'status' => 'nullable|string',
            'payment_method' => 'nullable|string|max:50',
            'paid_date' => 'nullable|date',
            'note' => 'nullable|string',
        ]);

        // 如果沒有提供單號，自動生成
        if (empty($validated['payment_no'])) {
            $validated['payment_no'] = $this->generatePaymentCode();
        }
        
        // 設定預設狀態
        if (empty($validated['status'])) {
            $validated['status'] = 'unpaid';
        }

        Payable::create($validated);

        return redirect()->route('tenant.payables.index')
            ->with('success', '應付帳款新增成功');
    }

    /**
     * Display the specified resource.
     */
    public function show(Payable $payable)
    {
        $payable->load(['project', 'company', 'responsibleUser']);
        
        return view('tenant.payables.show', compact('payable'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $projects = Project::where('is_active', true)->orderBy('name')->get();
        $companies = Company::where('is_active', true)->orderBy('name')->get();
        $users = User::where('is_active', true)->orderBy('name')->get();
        $paymentMethods = Tag::where('type', Tag::TYPE_PAYMENT_METHOD)->orderBy('name')->get();
        $taxSettings = TaxSetting::where('is_active', true)->orderBy('name')->get();
        $expenseCategories = ExpenseCategory::orderBy('parent_id')->orderBy('sort_order')->get();
        
        // 自動生成應付單號
        $nextCode = $this->generatePaymentCode();

        return view('tenant.payables.create', compact('projects', 'companies', 'users', 'paymentMethods', 'taxSettings', 'expenseCategories', 'nextCode'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payable $payable)
    {
        $projects = Project::where('is_active', true)->orderBy('name')->get();
        $companies = Company::where('is_active', true)->orderBy('name')->get();
        $users = User::where('is_active', true)->orderBy('name')->get();
        $paymentMethods = Tag::where('type', Tag::TYPE_PAYMENT_METHOD)->orderBy('name')->get();
        $taxSettings = TaxSetting::where('is_active', true)->orderBy('name')->get();
        $expenseCategories = ExpenseCategory::orderBy('parent_id')->orderBy('sort_order')->get();

        return view('tenant.payables.edit', compact('payable', 'projects', 'companies', 'users', 'paymentMethods', 'taxSettings', 'expenseCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payable $payable)
    {
        $validated = $request->validate([
            'payment_no' => 'required|string|max:50',
            'project_id' => 'required|exists:projects,id',
            'company_id' => 'nullable|exists:companies,id',
            'responsible_user_id' => 'nullable|exists:users,id',
            'type' => 'required|string|max:50',
            'payment_date' => 'required|date',
            'invoice_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'amount' => 'required|numeric|min:0',
            'deduction' => 'nullable|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'status' => 'nullable|string',
            'payment_method' => 'nullable|string|max:50',
            'paid_date' => 'nullable|date',
            'invoice_no' => 'nullable|string|max:50',
            'content' => 'nullable|string',
            'note' => 'nullable|string',
        ]);

        $payable->update($validated);

        return redirect()->route('tenant.payables.index')
            ->with('success', '應付帳款更新成功');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payable $payable)
    {
        $projectId = $payable->project_id;
        $payable->delete();

        return redirect()->route('tenant.projects.show', $projectId)
            ->with('success', '應付帳款刪除成功');
    }

    /**
     * 自動生成應付單號
     */
    private function generatePaymentCode(): string
    {
        // 取得最新的應付單號
        $lastPayable = Payable::withTrashed()
            ->where('payment_no', 'like', 'PAY-%')
            ->orderByRaw('CAST(SUBSTRING(payment_no, 5) AS UNSIGNED) DESC')
            ->first();

        if ($lastPayable) {
            preg_match('/PAY-(\d+)/', $lastPayable->payment_no, $matches);
            $nextNumber = isset($matches[1]) ? intval($matches[1]) + 1 : 1;
        } else {
            $nextNumber = 1;
        }

        return 'PAY-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }
    
    /**
     * 快速更新應付帳款
     */
    public function quickUpdate(Request $request, Payable $payable)
    {
        $validated = $request->validate([
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'vendor' => 'nullable|string',
            'content' => 'nullable|string',
            'note' => 'nullable|string',
        ]);

        $payable->update($validated);

        return redirect()->route('tenant.projects.show', $payable->project_id)
            ->with('success', '應付帳款更新成功');
    }
    
     * 快速給付頁面（薪資入帳）
     */
    public function quickPay(Payable $payable)
    {
        $payable->load(['project', 'company', 'responsibleUser', 'payeeUser', 'payeeCompany', 'payments']);
        
        // 計算已付和剩餘金額
        $totalPaid = $payable->payments()->sum('amount');
        $remainingAmount = $payable->amount - $totalPaid;
        
        // 取得付款方式標籤
        $paymentMethods = Tag::where('type', Tag::TYPE_PAYMENT_METHOD)->orderBy('name')->get();
        
        return view('tenant.payables.quick-pay', compact('payable', 'totalPaid', 'remainingAmount', 'paymentMethods'));
    }
    
    /**
     * 重設給付記錄
     */
    public function resetPayments(Payable $payable)
    {
        $payable->payments()->delete();
        $payable->update(['status' => 'unpaid']);
        
        return redirect()->route('tenant.payables.quick-pay', $payable)
            ->with('success', '給付記錄已重設');
    }
}
