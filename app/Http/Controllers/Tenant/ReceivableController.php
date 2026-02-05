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

        // 智能搜尋
        if ($request->filled('smart_search')) {
            $query->smartSearch($request->smart_search);
        }

        // 日期範圍篩選（預設最近一年）
        $dateStart = $request->input('date_start', now()->subYear()->format('Y-m-d'));
        $dateEnd = $request->input('date_end', now()->format('Y-m-d'));
        
        // 只在沒有使用智能搜尋時才套用預設日期範圍
        if (!$request->filled('smart_search') && $dateStart && $dateEnd) {
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
            $query->where('status', $request->status);
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

        // 計算統計數據（基於當前篩選條件）
        $statsQuery = Receivable::query();
        
        // 應用相同的篩選條件
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
            SUM(received_amount) as total_received
        ')->first();
        
        $totalAmount = $stats->total_amount ?? 0;
        $totalReceived = $stats->total_received ?? 0;

        return view('tenant.receivables.index', compact('receivables', 'dateStart', 'dateEnd', 'totalAmount', 'totalReceived'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'receipt_no' => 'nullable|string|max:50|unique:receivables,receipt_no',
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

        // 如果沒有提供單號，自動生成
        if (empty($validated['receipt_no'])) {
            $validated['receipt_no'] = $this->generateReceiptCode();
        }

        Receivable::create($validated);

        return redirect()->route('tenant.receivables.index')
            ->with('success', '應收帳款新增成功');
    }

    /**
     * Display the specified resource.
     */
    public function show(Receivable $receivable)
    {
        $receivable->load(['project', 'company', 'responsibleUser']);
        
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

        return 'RCV-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
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
}
