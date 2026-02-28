<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Receivable;
use App\Models\ReceivablePayment;
use App\Models\Project;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReceivableController extends Controller
{
    /**
     * 應收帳款列表（按 PRJ03 邏輯）
     */
    public function index(Request $request)
    {
        $query = Receivable::with(['project', 'company', 'responsibleUser']);

        // 帳務年度篩選（逾期/到期快篩時不限年度）
        $fiscalYear = $request->input('fiscal_year', date('Y'));
        $bypassYearFilter = ($request->filled('status') && $request->status === 'overdue')
                         || $request->filled('due_filter');
        if ($fiscalYear && !$bypassYearFilter) {
            $query->where('fiscal_year', $fiscalYear);
        }

        // 智能搜尋
        if ($request->filled('smart_search')) {
            $query->smartSearch($request->smart_search);
        }

        // 日期範圍篩選（預設最近一年）
        $dateStart = $request->input('date_start');
        $dateEnd = $request->input('date_end');
        
        // 只在沒有使用智能搜尋且沒有選擇年度時才套用預設日期範圍
        if (!$request->filled('smart_search') && !$request->filled('fiscal_year') && $dateStart && $dateEnd) {
            $query->whereBetween('receipt_date', [$dateStart, $dateEnd]);
        } elseif ($dateStart && $dateEnd) {
            $query->whereBetween('receipt_date', [$dateStart, $dateEnd]);
        }

        // 一般搜尋（專案名稱、內容、發票號碼）
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('content', 'like', "%{$search}%")
                  ->orWhere('invoice_no', 'like', "%{$search}%")
                  ->orWhere('receipt_no', 'like', "%{$search}%")
                  ->orWhereHas('project', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // 客戶篩選
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        // 狀態篩選
        if ($request->filled('status')) {
            if ($request->status === 'overdue') {
                // 逾期篩選
                $query->where('due_date', '<', now())
                      ->whereIn('status', ['unpaid', 'partial']);
            } else {
                $query->where('status', $request->status);
            }
        }

        // 到期日快速篩選
        if ($request->filled('due_filter')) {
            $days = (int) $request->due_filter;
            $today = now();
            $query->whereBetween('due_date', [$today, $today->copy()->addDays($days)])
                  ->whereIn('status', ['unpaid', 'partial']);
        }

        // 排除已結案專案（與舊系統一致）
        if (!$request->filled('show_all')) {
            $query->whereHas('project', function($q) {
                $q->where('status', '!=', Project::STATUS_CANCELLED);
            });
        }

        // 排序
        $orderBy = $request->input('order_by', 'receipt_date');
        $orderDir = $request->input('order_dir', 'desc');
        $query->orderBy($orderBy, $orderDir);

        $receivables = $query->paginate(15);

        // 可用年度清單
        $availableYears = Receivable::select('fiscal_year')
            ->whereNotNull('fiscal_year')
            ->distinct()
            ->orderBy('fiscal_year', 'desc')
            ->pluck('fiscal_year');

        // 計算統計數據（基於當前篩選條件）
        $statsQuery = Receivable::query();
        
        // 應用相同的篩選條件
        if ($fiscalYear) {
            $statsQuery->where('fiscal_year', $fiscalYear);
        }
        if ($dateStart && $dateEnd) {
            $statsQuery->whereBetween('receipt_date', [$dateStart, $dateEnd]);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $statsQuery->where(function($q) use ($search) {
                $q->where('content', 'like', "%{$search}%")
                  ->orWhere('invoice_no', 'like', "%{$search}%")
                  ->orWhere('receipt_no', 'like', "%{$search}%")
                  ->orWhereHas('project', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }
        if ($request->filled('company_id')) {
            $statsQuery->where('company_id', $request->company_id);
        }
        if ($request->filled('status')) {
            $statsQuery->where('status', $request->status);
        }
        if (!$request->filled('show_all')) {
            $statsQuery->whereHas('project', function($q) {
                $q->where('status', '!=', Project::STATUS_CANCELLED);
            });
        }
        
        $stats = $statsQuery->selectRaw('
            SUM(amount) as total_amount,
            SUM(received_amount) as total_received,
            SUM(withholding_tax) as total_withholding
        ')->first();
        
        $totalAmount = $stats->total_amount ?? 0;
        $totalReceived = $stats->total_received ?? 0;
        $totalWithholding = $stats->total_withholding ?? 0;

        $projectStatuses = \App\Http\Controllers\Tenant\SettingsController::getProjectStatuses();
        $paymentMethods = \App\Models\Tag::where('type', \App\Models\Tag::TYPE_PAYMENT_METHOD)->orderBy('name')->get();

        return view('tenant.receivables.index', compact('receivables', 'dateStart', 'dateEnd', 'totalAmount', 'totalReceived', 'totalWithholding', 'availableYears', 'fiscalYear', 'projectStatuses', 'paymentMethods'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'nullable|exists:projects,id',
            'company_id' => 'required|exists:companies,id',
            'responsible_user_id' => 'nullable|exists:users,id',
            'receipt_date' => 'nullable|date',
            'amount' => 'required|numeric|min:0',
            'amount_before_tax' => 'required|numeric|min:0',
            'tax_setting_id' => 'nullable|exists:tax_settings,id',
            'tax_inclusive' => 'nullable|boolean',
            'tax_amount' => 'nullable|numeric|min:0',
            'invoice_no' => 'nullable|string|max:50',
            'content' => 'nullable|string',
            'note' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        // 將空字串的日期欄位轉為 null
        if (isset($validated['receipt_date']) && $validated['receipt_date'] === '') {
            $validated['receipt_date'] = null;
        }

        // 自動生成單號
        $validated['receipt_no'] = $this->generateReceiptCode();
        
        // 如果沒有提供收款日期，使用今天
        if (empty($validated['receipt_date'])) {
            $validated['receipt_date'] = now()->format('Y-m-d');
        }
        
        // 如果沒有負責人，使用當前用戶
        if (empty($validated['responsible_user_id'])) {
            $validated['responsible_user_id'] = auth()->id();
        }
        
        // 預設狀態
        if (empty($validated['status'])) {
            $validated['status'] = 'unpaid';
        }
        
        // 預設已收款金額為 0
        $validated['received_amount'] = 0;

        Receivable::create($validated);

        return redirect()->route('tenant.receivables.index')
            ->with('success', '應收帳款新增成功');
    }

    /**
     * Display the specified resource.
     */
    public function show(Receivable $receivable)
    {
        $receivable->load(['project', 'company', 'responsibleUser', 'payments']);
        
        return view('tenant.receivables.show', compact('receivable'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $projects = Project::where('is_active', true)->orderBy('name')->get();
        $companies = Company::where('is_active', true)->orderBy('name')->get();
        $users = User::where('is_active', true)->orderBy('name')->get();
        $taxSettings = \App\Models\TaxSetting::where('is_active', true)->orderBy('rate')->get();
        
        // 自動生成應收單號
        $nextCode = $this->generateReceiptCode();

        return view('tenant.receivables.create', compact('projects', 'companies', 'users', 'taxSettings', 'nextCode'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Receivable $receivable)
    {
        $projects = Project::where('is_active', true)->orderBy('name')->get();
        $companies = Company::where('is_active', true)->orderBy('name')->get();
        $users = User::where('is_active', true)->orderBy('name')->get();
        $taxSettings = \App\Models\TaxSetting::where('is_active', true)->orderBy('rate')->get();

        return view('tenant.receivables.edit', compact('receivable', 'projects', 'companies', 'users', 'taxSettings'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Receivable $receivable)
    {
        $validated = $request->validate([
            'receipt_no' => 'required|string|max:50',
            'project_id' => 'required|exists:projects,id',
            'company_id' => 'required|exists:companies,id',
            'responsible_user_id' => 'nullable|exists:users,id',
            'receipt_date' => 'required|date',
            'due_date' => 'nullable|date',
            'amount' => 'required|numeric|min:0',
            'amount_before_tax' => 'nullable|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'tax_amount' => 'nullable|numeric|min:0',
            'withholding_tax' => 'nullable|numeric|min:0',
            'received_amount' => 'nullable|numeric|min:0',
            'status' => 'nullable|string',
            'payment_method' => 'nullable|string|max:50',
            'paid_date' => 'nullable|date',
            'invoice_no' => 'nullable|string|max:50',
            'quote_no' => 'nullable|string|max:50',
            'content' => 'nullable|string',
            'note' => 'nullable|string',
        ]);

        // 將空字串的日期欄位轉為 null
        $dateFields = ['receipt_date', 'due_date', 'paid_date'];
        foreach ($dateFields as $field) {
            if (isset($validated[$field]) && $validated[$field] === '') {
                $validated[$field] = null;
            }
        }

        $receivable->update($validated);

        return redirect()->route('tenant.receivables.index')
            ->with('success', '應收帳款更新成功');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Receivable $receivable)
    {
        $projectId = $receivable->project_id;
        $receivable->delete();

        return redirect()->route('tenant.projects.show', $projectId)
            ->with('success', '應收帳款刪除成功');
    }

    /**
     * 自動生成應收單號
     */
    private function generateReceiptCode(): string
    {
        // 取得最新的應收單號
        $lastReceivable = Receivable::withTrashed()
            ->where('receipt_no', 'like', 'RCV-%')
            ->orderByRaw('CAST(SUBSTRING(receipt_no, 5) AS UNSIGNED) DESC')
            ->first();

        if ($lastReceivable) {
            preg_match('/RCV-(\d+)/', $lastReceivable->receipt_no, $matches);
            $nextNumber = isset($matches[1]) ? intval($matches[1]) + 1 : 1;
        } else {
            $nextNumber = 1;
        }

        return 'RCV-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
    
    /**
     * 快速更新應收帳款
     */
    public function quickUpdate(Request $request, Receivable $receivable)
    {
        $validated = $request->validate([
            'receipt_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'content' => 'nullable|string',
            'due_date' => 'nullable|date',
            'invoice_no' => 'nullable|string',
            'note' => 'nullable|string',
        ]);

        $receivable->update($validated);

        return redirect()->route('tenant.projects.show', $receivable->project_id)
            ->with('success', '應收帳款更新成功');
    }
    
    /**
     * 快速收款頁面（入帳記錄）
     */
    public function quickReceive(Receivable $receivable)
    {
        // 取得此應收帳款的所有入帳記錄
        $payments = $receivable->payments()->orderBy('payment_date', 'desc')->get();
        
        // 計算剩餘應收金額
        $totalReceived = $payments->sum('amount');
        $remainingAmount = $receivable->amount - $totalReceived;
        
        // 取得付款方式標籤
        $paymentMethods = \App\Models\Tag::where('type', \App\Models\Tag::TYPE_PAYMENT_METHOD)->orderBy('name')->get();
        
        return view('tenant.receivables.quick-receive', compact('receivable', 'payments', 'totalReceived', 'remainingAmount', 'paymentMethods'));
    }
    
    /**
     * 重設收款資料（清除所有入帳記錄）
     */
    public function resetPayments(Receivable $receivable)
    {
        // 刪除所有入帳記錄
        $receivable->payments()->delete();
        
        // 重置狀態為未收款
        $receivable->update([
            'status' => 'unpaid',
            'received_amount' => 0,
        ]);
        
        return redirect()->route('tenant.receivables.quick-receive', $receivable)
            ->with('success', '已重設收款資料，所有入帳記錄已清除');
    }

    /**
     * 匯出應收帳款清單
     */
    public function export(Request $request)
    {
        $query = Receivable::with(['project', 'company', 'responsibleUser']);

        // 套用與 index 相同的篩選條件
        $fiscalYear = $request->input('fiscal_year', date('Y'));
        if ($fiscalYear) {
            $query->where('fiscal_year', $fiscalYear);
        }

        if ($request->filled('smart_search')) {
            $search = $request->smart_search;
            $query->where(function($q) use ($search) {
                $q->where('receipt_no', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhereHas('project', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                  })
                  ->orWhereHas('company', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $receivables = $query->orderBy('receipt_date', 'desc')->get();

        $filename = '應收帳款清單_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($receivables) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

            // 標題列
            fputcsv($file, ['收款單號', '收款日期', '到期日', '專案', '客戶', '內容', '金額', '已收款', '未收款', '狀態', '負責人']);

            // 資料列
            foreach ($receivables as $receivable) {
                $status = match($receivable->status) {
                    'paid' => '已收',
                    'partial' => '部分',
                    'overdue' => '逾期',
                    default => '待收',
                };

                fputcsv($file, [
                    $receivable->receipt_no,
                    $receivable->receipt_date->format('Y-m-d'),
                    $receivable->due_date?->format('Y-m-d'),
                    $receivable->project?->name,
                    $receivable->company?->name,
                    $receivable->content,
                    $receivable->amount,
                    $receivable->received_amount ?? 0,
                    $receivable->remaining_amount,
                    $status,
                    $receivable->responsibleUser?->name,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
