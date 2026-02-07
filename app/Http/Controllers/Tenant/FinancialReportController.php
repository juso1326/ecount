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
}
