<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Department;
use App\Models\Project;
use App\Models\User;

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

        if (request()->wantsJson()) {
            return response()->json([
                'stats' => $stats,
                'project_stats' => $projectStats,
                'recent_projects' => $recentProjects,
                'budget_overview' => $budgetOverview,
            ]);
        }

        return view('tenant.dashboard', compact('stats', 'projectStats', 'recentProjects', 'budgetOverview'));
    }
}
