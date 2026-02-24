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
        $query = Payable::with(['project', 'company', 'responsibleUser', 'payeeUser', 'payeeCompany']);

        // 帳務年度篩選（逾期/到期快篩時不限年度）
        $fiscalYear = $request->input('fiscal_year', date('Y'));
        $bypassYearFilter = ($request->filled('status') && $request->status === 'overdue')
                         || $request->filled('due_filter');
        if ($fiscalYear && !$bypassYearFilter) {
            $query->where('fiscal_year', $fiscalYear);
        }

        // 日期範圍篩選
        $dateStart = $request->input('date_start');
        $dateEnd = $request->input('date_end');
        
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
            if ($request->status === 'overdue') {
                // 逾期篩選
                $query->where('due_date', '<', now())
                      ->whereIn('status', ['pending', 'partial']);
            } else {
                $query->where('status', $request->status);
            }
        }

        // 到期日快速篩選
        if ($request->filled('due_filter')) {
            $days = (int) $request->due_filter;
            $today = now();
            $query->whereBetween('due_date', [$today, $today->copy()->addDays($days)])
                  ->whereIn('status', ['pending', 'partial']);
        }

        // 排序
        $orderBy = $request->input('order_by', 'payment_date');
        $orderDir = $request->input('order_dir', 'desc');
        $query->orderBy($orderBy, $orderDir);

        $payables = $query->paginate(15);

        // 付款提醒統計
        $today = now();
        $overduePayables = Payable::where('due_date', '<', $today)
            ->whereIn('status', ['pending', 'partial'])
            ->count();
        $dueSoon7Days = Payable::whereBetween('due_date', [$today, $today->copy()->addDays(7)])
            ->whereIn('status', ['pending', 'partial'])
            ->count();
        $dueSoon30Days = Payable::whereBetween('due_date', [$today, $today->copy()->addDays(30)])
            ->whereIn('status', ['pending', 'partial'])
            ->count();

        // 可用年度清單
        $availableYears = Payable::select('fiscal_year')
            ->whereNotNull('fiscal_year')
            ->distinct()
            ->orderBy('fiscal_year', 'desc')
            ->pluck('fiscal_year');

        // 計算統計數據（基於當前篩選條件）
        $statsQuery = Payable::query();
        
        // 應用相同的篩選條件
        if ($fiscalYear) {
            $statsQuery->where('fiscal_year', $fiscalYear);
        }
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
            SUM(deduction) as total_deduction,
            SUM(paid_amount) as total_paid
        ')->first();
        
        $totalAmount = $stats->total_amount ?? 0;
        $totalDeduction = $stats->total_deduction ?? 0;
        $totalPaid = $stats->total_paid ?? 0;

        return view('tenant.payables.index', compact(
            'payables', 
            'dateStart', 
            'dateEnd', 
            'totalAmount', 
            'totalDeduction',
            'totalPaid', 
            'availableYears', 
            'fiscalYear',
            'overduePayables',
            'dueSoon7Days',
            'dueSoon30Days'
        ));
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

        // 將空字串的日期欄位轉為 null
        $dateFields = ['payment_date', 'invoice_date', 'due_date', 'paid_date'];
        foreach ($dateFields as $field) {
            if (isset($validated[$field]) && $validated[$field] === '') {
                $validated[$field] = null;
            }
        }

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

        // 將空字串的日期欄位轉為 null
        $dateFields = ['payment_date', 'invoice_date', 'due_date', 'paid_date'];
        foreach ($dateFields as $field) {
            if (isset($validated[$field]) && $validated[$field] === '') {
                $validated[$field] = null;
            }
        }

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

        return 'PAY-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
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
    
    /**
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

    /**
     * 匯出應付帳款清單
     */
    public function export(Request $request)
    {
        $query = Payable::with(['project', 'company', 'responsibleUser', 'payeeUser', 'payeeCompany']);

        // 套用與 index 相同的篩選條件
        $fiscalYear = $request->input('fiscal_year', date('Y'));
        if ($fiscalYear) {
            $query->where('fiscal_year', $fiscalYear);
        }

        if ($request->filled('smart_search')) {
            $search = $request->smart_search;
            $query->where(function($q) use ($search) {
                $q->where('payment_no', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhereHas('project', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payables = $query->orderBy('payment_date', 'desc')->get();

        $filename = '應付帳款清單_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($payables) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

            // 標題列
            fputcsv($file, ['付款單號', '付款日期', '到期日', '專案', '廠商', '內容', '金額', '已付款', '未付款', '狀態', '負責人']);

            // 資料列
            foreach ($payables as $payable) {
                $status = match($payable->status) {
                    'paid' => '已付',
                    'partial' => '部分',
                    'overdue' => '逾期',
                    default => '待付',
                };

                fputcsv($file, [
                    $payable->payment_no,
                    $payable->payment_date->format('Y-m-d'),
                    $payable->due_date?->format('Y-m-d'),
                    $payable->project?->name,
                    $payable->company?->name,
                    $payable->content,
                    $payable->amount,
                    $payable->paid_amount ?? 0,
                    $payable->remaining_amount,
                    $status,
                    $payable->responsibleUser?->name,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
