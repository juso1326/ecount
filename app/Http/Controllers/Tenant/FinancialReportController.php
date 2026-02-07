<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Receivable;
use App\Models\Payable;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinancialReportController extends Controller
{
    /**
     * 財務報表首頁
     */
    public function index(Request $request)
    {
        $fiscalYear = $request->input('fiscal_year', date('Y'));
        
        // 取得可用年度
        $availableYears = Receivable::select('fiscal_year')
            ->whereNotNull('fiscal_year')
            ->distinct()
            ->union(
                Payable::select('fiscal_year')
                    ->whereNotNull('fiscal_year')
                    ->distinct()
            )
            ->orderBy('fiscal_year', 'desc')
            ->pluck('fiscal_year');
        
        // 快速統計（依會計年度）
        $summary = [
            'total_receivable' => Receivable::where('fiscal_year', $fiscalYear)->sum('amount'),
            'total_received' => Receivable::where('fiscal_year', $fiscalYear)->sum('received_amount'),
            'total_payable' => Payable::where('fiscal_year', $fiscalYear)->sum('amount'),
            'total_paid' => Payable::where('fiscal_year', $fiscalYear)->sum('paid_amount'),
        ];
        
        $summary['remaining_receivable'] = $summary['total_receivable'] - $summary['total_received'];
        $summary['remaining_payable'] = $summary['total_payable'] - $summary['total_paid'];
        $summary['net_income'] = $summary['total_received'] - $summary['total_paid'];
        
        return view('tenant.financial_reports.index', compact('summary', 'fiscalYear', 'availableYears'));
    }
    
    /**
     * 應收應付統計表
     */
    public function receivablesPayables(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->subMonths(6)->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));
        $projectId = $request->input('project_id');
        $companyId = $request->input('company_id');
        
        // 應收統計
        $receivablesQuery = Receivable::query()
            ->whereBetween('invoice_date', [$dateFrom, $dateTo]);
        
        if ($projectId) {
            $receivablesQuery->where('project_id', $projectId);
        }
        if ($companyId) {
            $receivablesQuery->where('company_id', $companyId);
        }
        
        $receivables = $receivablesQuery->with(['project', 'company'])
            ->orderBy('invoice_date', 'desc')
            ->paginate(20);
        
        // 應付統計
        $payablesQuery = Payable::query()
            ->whereBetween('payment_date', [$dateFrom, $dateTo]);
        
        if ($projectId) {
            $payablesQuery->where('project_id', $projectId);
        }
        
        $payables = $payablesQuery->with(['project'])
            ->orderBy('payment_date', 'desc')
            ->paginate(20);
        
        // 統計數據
        $stats = [
            'receivable_total' => $receivablesQuery->sum('amount'),
            'receivable_received' => $receivablesQuery->sum('received_amount'),
            'payable_total' => $payablesQuery->sum('amount'),
            'payable_paid' => $payablesQuery->sum('paid_amount'),
        ];
        
        // 專案列表（用於篩選）
        $projects = Project::orderBy('code')->get(['id', 'code', 'name']);
        
        return view('tenant.financial_reports.receivables_payables', compact(
            'receivables',
            'payables',
            'stats',
            'projects',
            'dateFrom',
            'dateTo',
            'projectId',
            'companyId'
        ));
    }
    
    /**
     * 月度財務匯總
     */
    public function monthlySummary(Request $request)
    {
        $year = $request->input('year', now()->year);
        $months = 12;
        
        // 按月統計應收
        $receivablesByMonth = Receivable::select(
                DB::raw('MONTH(invoice_date) as month'),
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('SUM(received_amount) as received_amount')
            )
            ->whereYear('invoice_date', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');
        
        // 按月統計應付
        $payablesByMonth = Payable::select(
                DB::raw('MONTH(payment_date) as month'),
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('SUM(paid_amount) as paid_amount')
            )
            ->whereYear('payment_date', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');
        
        // 組合月度資料
        $monthlyData = [];
        for ($m = 1; $m <= $months; $m++) {
            $receivable = $receivablesByMonth->get($m);
            $payable = $payablesByMonth->get($m);
            
            $monthlyData[] = [
                'month' => $m,
                'month_name' => now()->month($m)->format('M'),
                'receivable_amount' => $receivable->total_amount ?? 0,
                'received_amount' => $receivable->received_amount ?? 0,
                'payable_amount' => $payable->total_amount ?? 0,
                'paid_amount' => $payable->paid_amount ?? 0,
                'net_income' => ($receivable->received_amount ?? 0) - ($payable->paid_amount ?? 0),
            ];
        }
        
        return view('tenant.financial_reports.monthly_summary', compact('monthlyData', 'year'));
    }
    
    /**
     * 逾期應收應付報表
     */
    public function overdue()
    {
        $today = now()->format('Y-m-d');
        
        // 逾期應收
        $overdueReceivables = Receivable::whereRaw('amount > received_amount')
            ->where('due_date', '<', $today)
            ->with(['project', 'company'])
            ->orderBy('due_date')
            ->get()
            ->map(function($item) use ($today) {
                $item->overdue_days = now()->diffInDays($item->due_date);
                return $item;
            });
        
        // 逾期應付
        $overduePayables = Payable::whereRaw('amount > paid_amount')
            ->where('due_date', '<', $today)
            ->with(['project'])
            ->orderBy('due_date')
            ->get()
            ->map(function($item) use ($today) {
                $item->overdue_days = now()->diffInDays($item->due_date);
                return $item;
            });
        
        return view('tenant.financial_reports.overdue', compact('overdueReceivables', 'overduePayables'));
    }
    
    /**
     * API: 趨勢圖表資料
     */
    public function trendData(Request $request)
    {
        $months = $request->input('months', 6);
        $startDate = now()->subMonths($months)->startOfMonth();
        
        // 按月統計
        $data = [];
        for ($i = 0; $i < $months; $i++) {
            $date = now()->subMonths($months - $i - 1)->startOfMonth();
            $monthStart = $date->format('Y-m-d');
            $monthEnd = $date->endOfMonth()->format('Y-m-d');
            
            $receivableSum = Receivable::whereBetween('invoice_date', [$monthStart, $monthEnd])
                ->sum('received_amount');
            
            $payableSum = Payable::whereBetween('payment_date', [$monthStart, $monthEnd])
                ->sum('paid_amount');
            
            $data[] = [
                'month' => $date->format('Y-m'),
                'receivable' => $receivableSum,
                'payable' => $payableSum,
                'net' => $receivableSum - $payableSum,
            ];
        }
        
        return response()->json($data);
    }
    
    /**
     * 總支出報表（含薪資與應付）
     */
    public function totalExpenses(Request $request)
    {
        $fiscalYear = $request->input('fiscal_year', date('Y'));
        
        // 取得可用年度
        $availableYears = Payable::select('fiscal_year')
            ->whereNotNull('fiscal_year')
            ->distinct()
            ->orderBy('fiscal_year', 'desc')
            ->pluck('fiscal_year');
        
        // 1. 員工薪資（應付帳款中 payee_type = user）
        $employeeSalaries = Payable::where('fiscal_year', $fiscalYear)
            ->where('payee_type', 'user')
            ->whereNotNull('payee_user_id')
            ->with('payeeUser')
            ->get();
        
        $employeeSalaryTotal = $employeeSalaries->sum('amount');
        $employeeSalaryPaid = $employeeSalaries->sum('paid_amount');
        
        // 2. 外包薪資/勞務（應付帳款中 payee_type = company，且 type 包含薪資/勞務相關）
        $vendorPayables = Payable::where('fiscal_year', $fiscalYear)
            ->where('payee_type', 'company')
            ->whereNotNull('payee_company_id')
            ->where(function($q) {
                $q->where('type', 'like', '%薪資%')
                  ->orWhere('type', 'like', '%勞務%')
                  ->orWhere('type', 'like', '%外包%')
                  ->orWhere('content', 'like', '%薪資%')
                  ->orWhere('content', 'like', '%勞務%')
                  ->orWhere('content', 'like', '%外包%');
            })
            ->with('payeeCompany')
            ->get();
        
        $vendorPayableTotal = $vendorPayables->sum('amount');
        $vendorPayablePaid = $vendorPayables->sum('paid_amount');
        
        // 3. 其他應付（排除薪資相關）
        $otherPayables = Payable::where('fiscal_year', $fiscalYear)
            ->where(function($q) {
                $q->where('payee_type', 'company')
                  ->where(function($q2) {
                      $q2->where('type', 'not like', '%薪資%')
                         ->where('type', 'not like', '%勞務%')
                         ->where('type', 'not like', '%外包%')
                         ->where('content', 'not like', '%薪資%')
                         ->where('content', 'not like', '%勞務%')
                         ->where('content', 'not like', '%外包%');
                  });
            })
            ->orWhere(function($q) use ($fiscalYear) {
                $q->where('fiscal_year', $fiscalYear)
                  ->whereNull('payee_type');
            })
            ->with(['payeeCompany', 'project'])
            ->get();
        
        $otherPayableTotal = $otherPayables->sum('amount');
        $otherPayablePaid = $otherPayables->sum('paid_amount');
        
        // 總計
        $summary = [
            'employee_salary_total' => $employeeSalaryTotal,
            'employee_salary_paid' => $employeeSalaryPaid,
            'employee_salary_unpaid' => $employeeSalaryTotal - $employeeSalaryPaid,
            
            'vendor_payable_total' => $vendorPayableTotal,
            'vendor_payable_paid' => $vendorPayablePaid,
            'vendor_payable_unpaid' => $vendorPayableTotal - $vendorPayablePaid,
            
            'other_payable_total' => $otherPayableTotal,
            'other_payable_paid' => $otherPayablePaid,
            'other_payable_unpaid' => $otherPayableTotal - $otherPayablePaid,
            
            'grand_total' => $employeeSalaryTotal + $vendorPayableTotal + $otherPayableTotal,
            'grand_paid' => $employeeSalaryPaid + $vendorPayablePaid + $otherPayablePaid,
            'grand_unpaid' => ($employeeSalaryTotal - $employeeSalaryPaid) + ($vendorPayableTotal - $vendorPayablePaid) + ($otherPayableTotal - $otherPayablePaid),
        ];
        
        return view('tenant.financial_reports.total_expenses', compact(
            'fiscalYear',
            'availableYears',
            'summary',
            'employeeSalaries',
            'vendorPayables',
            'otherPayables'
        ));
    }
    
    /**
     * 專案收支分析表
     */
    public function projectAnalysis(Request $request)
    {
        $fiscalYear = $request->input('fiscal_year', date('Y'));
        $projectId = $request->input('project_id');
        
        // 取得可用年度
        $availableYears = Receivable::select('fiscal_year')
            ->whereNotNull('fiscal_year')
            ->distinct()
            ->union(
                Payable::select('fiscal_year')
                    ->whereNotNull('fiscal_year')
                    ->distinct()
            )
            ->orderBy('fiscal_year', 'desc')
            ->pluck('fiscal_year');
        
        // 專案列表
        $projects = Project::orderBy('code')->get();
        
        // 專案收支查詢
        $projectsQuery = Project::query();
        
        if ($projectId) {
            $projectsQuery->where('id', $projectId);
        }
        
        $projectData = $projectsQuery->get()->map(function($project) use ($fiscalYear) {
            // 應收統計
            $receivables = Receivable::where('project_id', $project->id)
                ->where('fiscal_year', $fiscalYear)
                ->selectRaw('
                    SUM(amount) as total_receivable,
                    SUM(received_amount) as total_received
                ')
                ->first();
            
            // 應付統計
            $payables = Payable::where('project_id', $project->id)
                ->where('fiscal_year', $fiscalYear)
                ->selectRaw('
                    SUM(amount) as total_payable,
                    SUM(paid_amount) as total_paid
                ')
                ->first();
            
            $totalReceivable = $receivables->total_receivable ?? 0;
            $totalReceived = $receivables->total_received ?? 0;
            $totalPayable = $payables->total_payable ?? 0;
            $totalPaid = $payables->total_paid ?? 0;
            
            // 計算收支與成本比例
            $profit = $totalReceived - $totalPaid;
            $costRatio = $totalReceivable > 0 ? ($totalPayable / $totalReceivable) * 100 : 0;
            $profitMargin = $totalReceivable > 0 ? ($profit / $totalReceivable) * 100 : 0;
            
            return [
                'id' => $project->id,
                'code' => $project->code,
                'name' => $project->name,
                'status' => $project->status,
                'total_receivable' => $totalReceivable,
                'total_received' => $totalReceived,
                'receivable_unpaid' => $totalReceivable - $totalReceived,
                'total_payable' => $totalPayable,
                'total_paid' => $totalPaid,
                'payable_unpaid' => $totalPayable - $totalPaid,
                'profit' => $profit,
                'cost_ratio' => $costRatio,
                'profit_margin' => $profitMargin,
            ];
        })->filter(function($data) {
            // 過濾掉沒有任何交易的專案
            return $data['total_receivable'] > 0 || $data['total_payable'] > 0;
        });
        
        // 總計
        $summary = [
            'total_receivable' => $projectData->sum('total_receivable'),
            'total_received' => $projectData->sum('total_received'),
            'receivable_unpaid' => $projectData->sum('receivable_unpaid'),
            'total_payable' => $projectData->sum('total_payable'),
            'total_paid' => $projectData->sum('total_paid'),
            'payable_unpaid' => $projectData->sum('payable_unpaid'),
            'profit' => $projectData->sum('profit'),
        ];
        
        $summary['cost_ratio'] = $summary['total_receivable'] > 0 
            ? ($summary['total_payable'] / $summary['total_receivable']) * 100 
            : 0;
        
        $summary['profit_margin'] = $summary['total_receivable'] > 0 
            ? ($summary['profit'] / $summary['total_receivable']) * 100 
            : 0;
        
        return view('tenant.financial_reports.project_analysis', compact(
            'fiscalYear',
            'availableYears',
            'projectId',
            'projects',
            'projectData',
            'summary'
        ));
    }
    
    /**
     * 應收未收報表
     */
    public function unpaidReceivables(Request $request)
    {
        $fiscalYear = $request->input('fiscal_year', date('Y'));
        $companyId = $request->input('company_id');
        
        // 取得可用年度
        $availableYears = Receivable::select('fiscal_year')
            ->whereNotNull('fiscal_year')
            ->distinct()
            ->orderBy('fiscal_year', 'desc')
            ->pluck('fiscal_year');
        
        // 應收未收查詢（已開發票但未收款）
        $unpaidQuery = Receivable::where('fiscal_year', $fiscalYear)
            ->whereNotNull('invoice_no')
            ->where('invoice_no', '!=', '')
            ->whereRaw('amount > received_amount')
            ->with(['company', 'project']);
        
        if ($companyId) {
            $unpaidQuery->where('company_id', $companyId);
        }
        
        $unpaidReceivables = $unpaidQuery->orderBy('invoice_date')->get();
        
        // 依公司分組統計
        $companySummary = $unpaidReceivables->groupBy('company_id')->map(function($items, $companyId) {
            $company = $items->first()->company;
            return [
                'company_id' => $companyId,
                'company_name' => $company->name ?? '未指定',
                'count' => $items->count(),
                'total_amount' => $items->sum('amount'),
                'total_received' => $items->sum('received_amount'),
                'total_unpaid' => $items->sum(fn($item) => $item->amount - $item->received_amount),
            ];
        })->sortByDesc('total_unpaid');
        
        // 逾期統計
        $today = now();
        $overdue = $unpaidReceivables->filter(fn($item) => $item->due_date && $item->due_date < $today);
        $upcoming = $unpaidReceivables->filter(fn($item) => !$item->due_date || $item->due_date >= $today);
        
        // 總計
        $summary = [
            'total_count' => $unpaidReceivables->count(),
            'total_amount' => $unpaidReceivables->sum('amount'),
            'total_received' => $unpaidReceivables->sum('received_amount'),
            'total_unpaid' => $unpaidReceivables->sum(fn($item) => $item->amount - $item->received_amount),
            'overdue_count' => $overdue->count(),
            'overdue_unpaid' => $overdue->sum(fn($item) => $item->amount - $item->received_amount),
            'upcoming_count' => $upcoming->count(),
            'upcoming_unpaid' => $upcoming->sum(fn($item) => $item->amount - $item->received_amount),
        ];
        
        // 公司列表（用於篩選）
        $companies = Company::orderBy('name')->get(['id', 'name']);
        
        return view('tenant.financial_reports.unpaid_receivables', compact(
            'fiscalYear',
            'availableYears',
            'companyId',
            'companies',
            'unpaidReceivables',
            'companySummary',
            'summary'
        ));
    }
}
