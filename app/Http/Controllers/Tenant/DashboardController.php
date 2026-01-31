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

        // 預算總覽
        $budgetOverview = [
            'total_budget' => Project::sum('budget'),
            'total_actual_cost' => Project::sum('actual_cost'),
        ];
        $budgetOverview['remaining_budget'] = $budgetOverview['total_budget'] - $budgetOverview['total_actual_cost'];

        // 獲取系統公告
        $announcement = Announcement::getActive();

        if (request()->wantsJson()) {
            return response()->json([
                'stats' => $stats,
                'project_stats' => $projectStats,
                'recent_projects' => $recentProjects,
                'budget_overview' => $budgetOverview,
                'announcement' => $announcement,
            ]);
        }

        return view('tenant.dashboard', compact('stats', 'projectStats', 'recentProjects', 'budgetOverview', 'announcement'));
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
