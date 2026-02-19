<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Receivable;
use App\Models\Payable;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    /**
     * 財務綜合分析報表
     */
    public function financialOverview(Request $request)
    {
        $year = $request->input('year', date('Y'));
        $startDate = "{$year}-01-01";
        $endDate = "{$year}-12-31";
        
        // 每月營收 vs. 支出趨勢
        $monthlyTrends = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthStart = "{$year}-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-01";
            $monthEnd = date('Y-m-t', strtotime($monthStart));
            
            // 使用已收金額計算實際營收
            $revenue = Receivable::whereBetween('receipt_date', [$monthStart, $monthEnd])
                ->sum('received_amount');
            
            // 使用已付金額計算實際支出
            $expense = Payable::whereBetween('payment_date', [$monthStart, $monthEnd])
                ->sum('paid_amount');
            
            $monthlyTrends[] = [
                'month' => $month,
                'revenue' => (float)$revenue,
                'expense' => (float)$expense,
                'profit' => (float)($revenue - $expense)
            ];
        }
        
        // 支出比例分析（按類型分組）
        $expenseBreakdown = Payable::select('type', DB::raw('SUM(paid_amount) as total'))
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->whereNotNull('type')
            ->groupBy('type')
            ->get()
            ->map(function($item) {
                // 轉換類型名稱為中文
                $typeNames = [
                    'salary' => '薪資',
                    'outsource' => '外包',
                    'material' => '材料',
                    'rent' => '租金',
                    'utility' => '水電',
                    'other' => '其他'
                ];
                $item->type = $typeNames[$item->type] ?? $item->type;
                $item->total = (float)$item->total;
                return $item;
            });
        
        // 如果沒有類型，按收款對象分組
        if ($expenseBreakdown->isEmpty()) {
            $expenseBreakdown = Payable::select('payee_type', DB::raw('SUM(paid_amount) as total'))
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->whereNotNull('payee_type')
                ->groupBy('payee_type')
                ->get()
                ->map(function($item) {
                    $item->type = $item->payee_type === 'user' ? '員工薪資' : '廠商支付';
                    $item->total = (float)$item->total;
                    return $item;
                });
        }
        
        // 總覽數據
        $summary = [
            'total_revenue' => (float)Receivable::where('fiscal_year', $year)->sum('received_amount'),
            'total_expense' => (float)Payable::where('fiscal_year', $year)->sum('paid_amount'),
            'total_receivable' => (float)Receivable::where('fiscal_year', $year)->sum('amount'),
            'total_received' => (float)Receivable::where('fiscal_year', $year)->sum('received_amount'),
        ];
        $summary['net_profit'] = $summary['total_revenue'] - $summary['total_expense'];
        $summary['expected_revenue'] = $summary['total_receivable'] - $summary['total_received'];
        
        return view('tenant.reports.financial-overview', compact('monthlyTrends', 'expenseBreakdown', 'summary', 'year'));
    }
    
    /**
     * 應收/應付帳款分析報表
     */
    public function arApAnalysis(Request $request)
    {
        $today = date('Y-m-d');
        
        // 應收帳款總覽
        $arSummary = [
            'total' => (float)Receivable::sum('amount'),
            'received' => (float)Receivable::sum('received_amount'),
            'outstanding' => (float)Receivable::whereRaw('amount > received_amount')->sum(DB::raw('amount - received_amount')),
        ];
        
        // 應付帳款總覽
        $apSummary = [
            'total' => (float)Payable::sum('amount'),
            'paid' => (float)Payable::sum('paid_amount'),
            'outstanding' => (float)Payable::whereRaw('amount > paid_amount')->sum(DB::raw('amount - paid_amount')),
        ];
        
        // 應收帳款帳齡分析
        $arTotal = Receivable::whereRaw('amount > received_amount')->sum(DB::raw('amount - received_amount'));
        $arAging = [
            ['period' => '未到期', 'amount' => 0, 'percentage' => 0],
            ['period' => '1-30天', 'amount' => 0, 'percentage' => 0],
            ['period' => '31-60天', 'amount' => 0, 'percentage' => 0],
            ['period' => '61-90天', 'amount' => 0, 'percentage' => 0],
            ['period' => '90天以上', 'amount' => 0, 'percentage' => 0],
        ];
        
        $arAging[0]['amount'] = (float)Receivable::whereRaw('amount > received_amount')
            ->where('due_date', '>=', $today)
            ->sum(DB::raw('amount - received_amount'));
        $arAging[1]['amount'] = (float)Receivable::whereRaw('amount > received_amount')
            ->whereRaw('DATEDIFF(?, due_date) BETWEEN 1 AND 30', [$today])
            ->sum(DB::raw('amount - received_amount'));
        $arAging[2]['amount'] = (float)Receivable::whereRaw('amount > received_amount')
            ->whereRaw('DATEDIFF(?, due_date) BETWEEN 31 AND 60', [$today])
            ->sum(DB::raw('amount - received_amount'));
        $arAging[3]['amount'] = (float)Receivable::whereRaw('amount > received_amount')
            ->whereRaw('DATEDIFF(?, due_date) BETWEEN 61 AND 90', [$today])
            ->sum(DB::raw('amount - received_amount'));
        $arAging[4]['amount'] = (float)Receivable::whereRaw('amount > received_amount')
            ->whereRaw('DATEDIFF(?, due_date) > 90', [$today])
            ->sum(DB::raw('amount - received_amount'));
        
        foreach ($arAging as &$aging) {
            $aging['percentage'] = $arTotal > 0 ? round(($aging['amount'] / $arTotal) * 100, 1) : 0;
        }
        
        // 應付帳款帳齡分析
        $apTotal = Payable::whereRaw('amount > paid_amount')->sum(DB::raw('amount - paid_amount'));
        $apAging = [
            ['period' => '未到期', 'amount' => 0, 'percentage' => 0],
            ['period' => '1-30天', 'amount' => 0, 'percentage' => 0],
            ['period' => '31-60天', 'amount' => 0, 'percentage' => 0],
            ['period' => '61-90天', 'amount' => 0, 'percentage' => 0],
            ['period' => '90天以上', 'amount' => 0, 'percentage' => 0],
        ];
        
        $apAging[0]['amount'] = (float)Payable::whereRaw('amount > paid_amount')
            ->where('due_date', '>=', $today)
            ->sum(DB::raw('amount - paid_amount'));
        $apAging[1]['amount'] = (float)Payable::whereRaw('amount > paid_amount')
            ->whereRaw('DATEDIFF(?, due_date) BETWEEN 1 AND 30', [$today])
            ->sum(DB::raw('amount - paid_amount'));
        $apAging[2]['amount'] = (float)Payable::whereRaw('amount > paid_amount')
            ->whereRaw('DATEDIFF(?, due_date) BETWEEN 31 AND 60', [$today])
            ->sum(DB::raw('amount - paid_amount'));
        $apAging[3]['amount'] = (float)Payable::whereRaw('amount > paid_amount')
            ->whereRaw('DATEDIFF(?, due_date) BETWEEN 61 AND 90', [$today])
            ->sum(DB::raw('amount - paid_amount'));
        $apAging[4]['amount'] = (float)Payable::whereRaw('amount > paid_amount')
            ->whereRaw('DATEDIFF(?, due_date) > 90', [$today])
            ->sum(DB::raw('amount - paid_amount'));
        
        foreach ($apAging as &$aging) {
            $aging['percentage'] = $apTotal > 0 ? round(($aging['amount'] / $apTotal) * 100, 1) : 0;
        }
        
        // 專案未收款排名 TOP 10（按專案）
        $topProjects = Receivable::select(
                'project_id',
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('SUM(received_amount) as received_amount'),
                DB::raw('SUM(amount - received_amount) as outstanding_amount')
            )
            ->whereRaw('amount > received_amount')
            ->whereNotNull('project_id')
            ->with('project')
            ->groupBy('project_id')
            ->orderBy('outstanding_amount', 'desc')
            ->limit(10)
            ->get()
            ->map(function($item) {
                $item->name = $item->project ? $item->project->name : '未指定專案';
                $item->code = $item->project ? $item->project->code : '-';
                return $item;
            });
        
        // 未來60天現金流預測
        $cashFlowForecast = collect();
        for ($i = 0; $i < 60; $i++) {
            $date = date('Y-m-d', strtotime("+{$i} days"));
            $expectedIn = (float)Receivable::where('due_date', $date)
                ->sum(DB::raw('amount - received_amount'));
            $expectedOut = (float)Payable::where('due_date', $date)
                ->sum(DB::raw('amount - paid_amount'));
            
            if ($expectedIn > 0 || $expectedOut > 0) {
                $cashFlowForecast->push([
                    'date' => date('m/d', strtotime($date)),
                    'expected_in' => $expectedIn,
                    'expected_out' => $expectedOut,
                ]);
            }
        }
        
        return view('tenant.reports.ar-ap-analysis', compact('arSummary', 'apSummary', 'arAging', 'apAging', 'topProjects', 'cashFlowForecast'));
    }
    
    /**
     * 專案損益報表
     */
    public function projectProfitLoss(Request $request)
    {
        $status = $request->input('status');
        
        $query = Project::with(['receivables', 'payables']);
        
        if ($status === 'active') {
            $query->whereNull('end_date')->orWhere('end_date', '>=', date('Y-m-d'));
        } elseif ($status === 'completed') {
            $query->where('end_date', '<', date('Y-m-d'));
        }
        
        $projects = $query->get();
        
        // 計算專案毛利
        $projectProfits = $projects->map(function($project) {
            $revenue = (float)$project->receivables->sum('received_amount');
            $cost = (float)$project->payables->sum('paid_amount');
            $profit = $revenue - $cost;
            $margin = $revenue > 0 ? ($profit / $revenue) * 100 : 0;
            
            return (object)[
                'id' => $project->id,
                'code' => $project->code,
                'name' => $project->name,
                'revenue' => $revenue,
                'cost' => $cost,
                'profit' => $profit,
                'margin' => round($margin, 2),
            ];
        })->sortByDesc('profit')->values();
        
        // 預算對比分析
        $budgetComparison = $projects->map(function($project) {
            $budget = (float)($project->budget ?? 0);
            $actualCost = (float)$project->payables->sum('paid_amount');
            $variance = $budget - $actualCost;
            $usageRate = $budget > 0 ? ($actualCost / $budget) * 100 : 0;
            
            return (object)[
                'id' => $project->id,
                'code' => $project->code,
                'name' => $project->name,
                'budget' => $budget,
                'actual_cost' => $actualCost,
                'variance' => $variance,
                'usage_rate' => round($usageRate, 2),
            ];
        })->sortBy('usage_rate')->values();
        
        // 總覽統計
        $summary = [
            'total_projects' => $projects->count(),
            'total_budget' => (float)$projects->sum('budget'),
            'total_revenue' => $projectProfits->sum('revenue'),
            'total_cost' => $projectProfits->sum('cost'),
        ];
        
        return view('tenant.reports.project-profit-loss', compact('projectProfits', 'budgetComparison', 'summary', 'status'));
    }
    
    /**
     * 薪資與人力成本報表
     */
    public function payrollLabor(Request $request)
    {
        $year = $request->input('year', date('Y'));
        
        // 獲取薪資相關支付（payee_type = user 的支付記錄）
        $salaryPayables = Payable::where('payee_type', 'user')
            ->where('fiscal_year', $year)
            ->get();
        
        // 年度總薪資
        $totalSalary = (float)$salaryPayables->sum('paid_amount');
        
        // 月度薪資明細
        $monthlyDetails = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthStart = "{$year}-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-01";
            $monthEnd = date('Y-m-t', strtotime($monthStart));
            
            $monthPayables = $salaryPayables->whereBetween('payment_date', [$monthStart, $monthEnd]);
            
            // 計算各項目（簡化版本，實際應該從 SalaryService 獲取詳細數據）
            $baseSalary = (float)$monthPayables->where('type', 'salary')->sum('paid_amount');
            $bonus = (float)$monthPayables->where('type', 'bonus')->sum('paid_amount');
            $otherAdditions = (float)$monthPayables->whereIn('type', ['allowance', 'other'])->sum('paid_amount');
            $deductions = (float)$monthPayables->where('deduction', '>', 0)->sum('deduction');
            
            $netPay = $baseSalary + $bonus + $otherAdditions - $deductions;
            
            $monthlyDetails[] = [
                'month' => $month,
                'base_salary' => $baseSalary > 0 ? $baseSalary : $netPay, // 如果沒有分類就用總額
                'bonus' => $bonus,
                'other_additions' => $otherAdditions,
                'deductions' => $deductions,
                'net_pay' => $netPay > 0 ? $netPay : $baseSalary,
            ];
        }
        
        // 部門成本分析（從員工薪資統計）
        $departmentCosts = User::select('department_id', DB::raw('COUNT(*) as employee_count'))
            ->whereNotNull('department_id')
            ->where('is_active', true)
            ->groupBy('department_id')
            ->get()
            ->map(function($item) use ($salaryPayables) {
                // 計算該部門員工的薪資總和
                $userIds = User::where('department_id', $item->department_id)->where('is_active', true)->pluck('id');
                $totalCost = (float)$salaryPayables->whereIn('payee_user_id', $userIds)->sum('paid_amount');
                
                // 取得部門名稱
                $departmentName = '部門 ' . $item->department_id;
                if (\Schema::hasTable('departments')) {
                    $dept = \DB::table('departments')->find($item->department_id);
                    if ($dept && isset($dept->name)) {
                        $departmentName = $dept->name;
                    }
                }
                
                return (object)[
                    'department' => $departmentName,
                    'employee_count' => $item->employee_count,
                    'total_cost' => $totalCost,
                ];
            });
        
        // 員工薪資排名 TOP 10
        $topEarners = $salaryPayables->groupBy('payee_user_id')
            ->map(function($payments, $userId) {
                $user = User::find($userId);
                if (!$user) return null;
                
                $totalSalary = (float)$payments->sum('paid_amount');
                $monthCount = $payments->pluck('payment_date')->map(function($date) {
                    return date('Y-m', strtotime($date));
                })->unique()->count();
                
                return (object)[
                    'name' => $user->name,
                    'department' => $user->position ?? '未分類',
                    'monthly_salary' => $monthCount > 0 ? round($totalSalary / $monthCount) : $totalSalary,
                    'annual_salary' => $totalSalary,
                ];
            })
            ->filter()
            ->sortByDesc('annual_salary')
            ->take(10)
            ->values();
        
        // 總覽統計
        $activeEmployees = User::where('is_active', true)->count();
        $avgMonthlySalary = $activeEmployees > 0 ? round($totalSalary / 12 / $activeEmployees) : 0;
        $avgAnnualSalary = $activeEmployees > 0 ? round($totalSalary / $activeEmployees) : 0;
        
        $summary = [
            'total_employees' => $activeEmployees,
            'total_salary' => $totalSalary,
            'avg_monthly_salary' => $avgMonthlySalary,
            'avg_annual_salary' => $avgAnnualSalary,
        ];
        
        return view('tenant.reports.payroll-labor', compact('summary', 'monthlyDetails', 'departmentCosts', 'topEarners', 'year'));
    }
}
