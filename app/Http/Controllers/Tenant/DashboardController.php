<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Company;
use App\Models\Department;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the tenant dashboard.
     */
    public function index()
    {
        $fiscalYear = request()->input('fiscal_year', date('Y'));
        
        // 基本統計
        $stats = [
            'total_companies' => Company::count(),
            'total_departments' => Department::count(),
            'total_projects' => Project::count(),
            'total_users' => User::count(),
            'active_projects' => Project::where('status', 'in_progress')->count(),
            'completed_projects' => Project::where('status', 'completed')->count(),
        ];

        // 專案狀態統計
        $projectStats = Project::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        // 最近專案
        $recentProjects = Project::with(['company', 'department'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // 進行中專案數量
        $activeProjects = $stats['active_projects'];

        // 獲取系統公告
        $announcement = Announcement::getActive();
        
        // 財務統計（本年度）
        $financialStats = $this->getFinancialStats($fiscalYear);
        
        // 專案收益統計
        $projectProfitStats = $this->getProjectProfitStats($fiscalYear);
        
        // 可用年度
        $availableYears = \App\Models\Receivable::select('fiscal_year')
            ->whereNotNull('fiscal_year')
            ->distinct()
            ->orderBy('fiscal_year', 'desc')
            ->pluck('fiscal_year');

        if (request()->wantsJson()) {
            return response()->json([
                'stats' => $stats,
                'project_stats' => $projectStats,
                'recent_projects' => $recentProjects,
                'active_projects' => $activeProjects,
                'announcement' => $announcement,
                'financial_stats' => $financialStats,
                'project_profit_stats' => $projectProfitStats,
            ]);
        }

        return view('tenant.dashboard', compact(
            'stats', 
            'projectStats', 
            'recentProjects', 
            'activeProjects', 
            'announcement',
            'financialStats',
            'projectProfitStats',
            'fiscalYear',
            'availableYears'
        ));
    }
    
    /**
     * 取得財務統計
     */
    private function getFinancialStats($fiscalYear)
    {
        // 應收統計
        $receivableStats = \App\Models\Receivable::where('fiscal_year', $fiscalYear)
            ->selectRaw('
                SUM(amount) as total_receivable,
                SUM(received_amount) as total_received,
                COUNT(CASE WHEN invoice_no IS NOT NULL AND invoice_no != "" AND amount > received_amount THEN 1 END) as unpaid_count
            ')
            ->first();
        
        // 應付統計
        $payableStats = \App\Models\Payable::where('fiscal_year', $fiscalYear)
            ->selectRaw('
                SUM(amount) as total_payable,
                SUM(paid_amount) as total_paid,
                SUM(CASE WHEN payee_type = "user" THEN paid_amount ELSE 0 END) as employee_salary,
                SUM(CASE WHEN payee_type = "company" AND (type LIKE "%薪資%" OR type LIKE "%勞務%" OR type LIKE "%外包%") THEN paid_amount ELSE 0 END) as outsource_cost
            ')
            ->first();
        
        // 計算衍生指標
        $totalReceivable = $receivableStats->total_receivable ?? 0;
        $totalReceived = $receivableStats->total_received ?? 0;
        $totalPayable = $payableStats->total_payable ?? 0;
        $totalPaid = $payableStats->total_paid ?? 0;
        
        $netIncome = $totalReceived - $totalPaid;
        $profitMargin = $totalReceived > 0 ? ($netIncome / $totalReceived) * 100 : 0;
        
        // 風險指標
        $unpaidReceivables = $totalReceivable - $totalReceived;
        $unpaidPayables = $totalPayable - $totalPaid;
        
        return [
            'total_receivable' => $totalReceivable,
            'total_received' => $totalReceived,
            'unpaid_receivables' => $unpaidReceivables,
            'unpaid_count' => $receivableStats->unpaid_count ?? 0,
            
            'total_payable' => $totalPayable,
            'total_paid' => $totalPaid,
            'unpaid_payables' => $unpaidPayables,
            'employee_salary' => $payableStats->employee_salary ?? 0,
            'outsource_cost' => $payableStats->outsource_cost ?? 0,
            
            'net_income' => $netIncome,
            'profit_margin' => $profitMargin,
        ];
    }
    
    /**
     * 取得專案收益統計
     */
    private function getProjectProfitStats($fiscalYear)
    {
        $projects = Project::with(['receivables' => function($q) use ($fiscalYear) {
                $q->where('fiscal_year', $fiscalYear);
            }, 'payables' => function($q) use ($fiscalYear) {
                $q->where('fiscal_year', $fiscalYear);
            }])
            ->get()
            ->map(function($project) {
                $totalReceived = $project->receivables->sum('received_amount');
                $totalPaid = $project->payables->sum('paid_amount');
                $profit = $totalReceived - $totalPaid;
                
                return [
                    'project' => $project,
                    'total_received' => $totalReceived,
                    'total_paid' => $totalPaid,
                    'profit' => $profit,
                ];
            })
            ->filter(function($data) {
                return $data['total_received'] > 0 || $data['total_paid'] > 0;
            })
            ->sortByDesc('profit')
            ->take(5);
        
        return $projects;
    }

    /**
     * 更新系統公告（僅管理員或經理）
     */
    public function updateAnnouncement(Request $request)
    {
        // 檢查權限：admin 或 manager 角色可以編輯
        $user = auth()->user();
        if (!$user->hasAnyRole(['admin', 'manager'])) {
            abort(403, '您沒有編輯系統公告的權限');
        }

        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        $announcement = Announcement::getActive();
        
        if (!$announcement) {
            $announcement = new Announcement();
            $announcement->created_by = auth()->id();
        }

        $announcement->content = $validated['content'];
        $announcement->updated_by = auth()->id();
        $announcement->save();

        return back()->with('success', '系統公告已更新');
    }
}
