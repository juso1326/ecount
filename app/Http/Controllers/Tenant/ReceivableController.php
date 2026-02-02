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
    public function index(Request $request)
    {
        $query = Receivable::with(['project', 'company', 'responsibleUser']);

        // 搜尋（包含專案代碼和名稱）
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('receipt_no', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhere('invoice_no', 'like', "%{$search}%")
                  ->orWhereHas('project', function($q) use ($search) {
                      $q->where('code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('company', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('short_name', 'like', "%{$search}%");
                  });
            });
        }

        // 專案篩選
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // 客戶篩選
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        // 狀態篩選
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 年月篩選（參考舊系統）
        if ($request->filled('year') && $request->filled('month')) {
            $year = $request->year;
            $month = str_pad($request->month, 2, '0', STR_PAD_LEFT);
            $query->whereYear('receipt_date', $year)
                  ->whereMonth('receipt_date', $month);
        }

        $receivables = $query->orderBy('receipt_date', 'desc')
                            ->orderBy('receipt_no', 'desc')
                            ->paginate(15);

        // 計算總額
        $totalAmount = $query->sum('amount');
        $totalReceived = $query->sum('received_amount');

        return view('tenant.receivables.index', compact('receivables', 'totalAmount', 'totalReceived'));
    }

    public function create()
    {
        $projects = Project::where('is_active', true)->orderBy('code')->get();
        $companies = Company::where('is_active', true)->orderBy('name')->get();
        $users = User::where('is_active', true)->orderBy('name')->get();

        return view('tenant.receivables.create', compact('projects', 'companies', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'receipt_no' => 'required|string|max:50',
            'project_id' => 'nullable|exists:projects,id',
            'company_id' => 'nullable|exists:companies,id',
            'responsible_user_id' => 'nullable|exists:users,id',
            'receipt_date' => 'required|date',
            'due_date' => 'nullable|date',
            'amount' => 'required|numeric|min:0',
            'amount_before_tax' => 'nullable|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'tax_amount' => 'nullable|numeric|min:0',
            'withholding_tax' => 'nullable|numeric|min:0',
            'received_amount' => 'nullable|numeric|min:0',
            'status' => 'required|in:pending,partial,paid,overdue',
            'payment_method' => 'nullable|string|max:50',
            'paid_date' => 'nullable|date',
            'invoice_no' => 'nullable|string|max:50',
            'quote_no' => 'nullable|string|max:50',
            'content' => 'nullable|string',
            'note' => 'nullable|string',
        ]);

        Receivable::create($validated);

        return redirect()->route('tenant.receivables.index')
            ->with('success', '應收帳款新增成功');
    }

    public function show(Receivable $receivable)
    {
        $receivable->load(['project', 'company', 'responsibleUser']);
        
        return view('tenant.receivables.show', compact('receivable'));
    }

    public function edit(Receivable $receivable)
    {
        $projects = Project::where('is_active', true)->orderBy('code')->get();
        $companies = Company::where('is_active', true)->orderBy('name')->get();
        $users = User::where('is_active', true)->orderBy('name')->get();

        return view('tenant.receivables.edit', compact('receivable', 'projects', 'companies', 'users'));
    }

    public function update(Request $request, Receivable $receivable)
    {
        $validated = $request->validate([
            'receipt_no' => 'required|string|max:50',
            'project_id' => 'nullable|exists:projects,id',
            'company_id' => 'nullable|exists:companies,id',
            'responsible_user_id' => 'nullable|exists:users,id',
            'receipt_date' => 'required|date',
            'due_date' => 'nullable|date',
            'amount' => 'required|numeric|min:0',
            'amount_before_tax' => 'nullable|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'tax_amount' => 'nullable|numeric|min:0',
            'withholding_tax' => 'nullable|numeric|min:0',
            'received_amount' => 'nullable|numeric|min:0',
            'status' => 'required|in:pending,partial,paid,overdue',
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

    public function destroy(Receivable $receivable)
    {
        $receivable->delete();

        return redirect()->route('tenant.receivables.index')
            ->with('success', '應收帳款刪除成功');
    }

    /**
     * 新增收款紀錄
     */
    public function addPayment(Request $request, Receivable $receivable)
    {
        $validated = $request->validate([
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:1|max:' . ($receivable->amount - $receivable->received_amount),
            'payment_method' => 'nullable|string|max:50',
            'note' => 'nullable|string',
        ]);

        // 建立收款紀錄
        $receivable->payments()->create($validated);

        // 更新已收金額
        $receivable->received_amount += $validated['amount'];
        
        // 自動更新狀態
        if ($receivable->received_amount >= $receivable->amount) {
            $receivable->status = 'paid';
            $receivable->paid_date = $validated['payment_date'];
        } elseif ($receivable->received_amount > 0) {
            $receivable->status = 'partially_paid';
        }
        
        $receivable->save();

        return redirect()->route('tenant.receivables.show', $receivable)
            ->with('success', '收款紀錄新增成功');
    }
}
