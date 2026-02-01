<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Payable;
use App\Models\Project;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayableController extends Controller
{
    public function index(Request $request)
    {
        $query = Payable::with(['project', 'company', 'responsibleUser']);

        // 搜尋（包含專案代碼和名稱）
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('payment_no', 'like', "%{$search}%")
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

        // 廠商篩選
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        // 狀態篩選
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 類型篩選
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // 年月篩選
        if ($request->filled('year') && $request->filled('month')) {
            $year = $request->year;
            $month = str_pad($request->month, 2, '0', STR_PAD_LEFT);
            $query->whereYear('payment_date', $year)
                  ->whereMonth('payment_date', $month);
        }

        $payables = $query->orderBy('payment_date', 'desc')
                          ->orderBy('payment_no', 'desc')
                          ->paginate(15);

        // 計算總額
        $totalAmount = $query->sum('amount');
        $totalPaid = $query->sum('paid_amount');

        return view('tenant.payables.index', compact('payables', 'totalAmount', 'totalPaid'));
    }

    public function create()
    {
        $projects = Project::where('is_active', true)->orderBy('code')->get();
        $companies = Company::where('is_active', true)->orderBy('name')->get();
        $users = User::where('is_active', true)->orderBy('name')->get();

        return view('tenant.payables.create', compact('projects', 'companies', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'payment_no' => 'required|string|max:50',
            'project_id' => 'nullable|exists:projects,id',
            'company_id' => 'nullable|exists:companies,id',
            'responsible_user_id' => 'nullable|exists:users,id',
            'payment_date' => 'required|date',
            'invoice_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'amount' => 'required|numeric|min:0',
            'deduction' => 'nullable|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'type' => 'required|in:purchase,expense,service,other',
            'status' => 'required|in:pending,partial,paid,overdue',
            'payment_method' => 'nullable|string|max:50',
            'paid_date' => 'nullable|date',
            'invoice_no' => 'nullable|string|max:50',
            'content' => 'nullable|string',
            'note' => 'nullable|string',
        ]);

        Payable::create($validated);

        return redirect()->route('tenant.payables.index')
            ->with('success', '應付帳款新增成功');
    }

    public function show(Payable $payable)
    {
        $payable->load(['project', 'company', 'responsibleUser']);
        
        return view('tenant.payables.show', compact('payable'));
    }

    public function edit(Payable $payable)
    {
        $projects = Project::where('is_active', true)->orderBy('code')->get();
        $companies = Company::where('is_active', true)->orderBy('name')->get();
        $users = User::where('is_active', true)->orderBy('name')->get();

        return view('tenant.payables.edit', compact('payable', 'projects', 'companies', 'users'));
    }

    public function update(Request $request, Payable $payable)
    {
        $validated = $request->validate([
            'payment_no' => 'required|string|max:50',
            'project_id' => 'nullable|exists:projects,id',
            'company_id' => 'nullable|exists:companies,id',
            'responsible_user_id' => 'nullable|exists:users,id',
            'payment_date' => 'required|date',
            'invoice_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'amount' => 'required|numeric|min:0',
            'deduction' => 'nullable|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'type' => 'required|in:purchase,expense,service,other',
            'status' => 'required|in:pending,partial,paid,overdue',
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

    public function destroy(Payable $payable)
    {
        $payable->delete();

        return redirect()->route('tenant.payables.index')
            ->with('success', '應付帳款刪除成功');
    }
}
