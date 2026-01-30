@extends('layouts.tenant')

@section('title', '儀表板')

@section('content')
<!-- Page Header -->
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">儀表板</h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">歡迎回來，{{ auth()->user()->name }}</p>
    </div>
    <div class="text-sm text-gray-500 dark:text-gray-400">
        {{ now()->format('Y-m-d H:i') }}
    </div>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6 xl:grid-cols-4 2xl:gap-7 mb-6">
    <!-- Total Companies -->
    <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <h4 class="text-title-md font-bold text-gray-900 dark:text-white">
                    {{ $stats['total_companies'] }}
                </h4>
                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">公司總數</span>
            </div>
            <div class="flex h-11 w-11 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/20">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Total Departments -->
    <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <h4 class="text-title-md font-bold text-gray-900 dark:text-white">
                    {{ $stats['total_departments'] }}
                </h4>
                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">部門總數</span>
            </div>
            <div class="flex h-11 w-11 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/20">
                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Total Projects -->
    <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <h4 class="text-title-md font-bold text-gray-900 dark:text-white">
                    {{ $stats['total_projects'] }}
                </h4>
                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">專案總數</span>
            </div>
            <div class="flex h-11 w-11 items-center justify-center rounded-full bg-purple-100 dark:bg-purple-900/20">
                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Active Projects -->
    <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <h4 class="text-title-md font-bold text-gray-900 dark:text-white">
                    {{ $stats['active_projects'] }}
                </h4>
                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">進行中專案</span>
            </div>
            <div class="flex h-11 w-11 items-center justify-center rounded-full bg-yellow-100 dark:bg-yellow-900/20">
                <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Charts & Recent Activity -->
<div class="grid grid-cols-1 gap-4 md:gap-6 2xl:gap-7 lg:grid-cols-2 mb-6">
    <!-- Project Status Chart -->
    <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 shadow-sm">
        <h3 class="mb-4 text-xl font-semibold text-gray-900 dark:text-white">專案狀態分布</h3>
        <div class="relative" style="height: 300px;">
            <canvas id="projectStatusChart"></canvas>
        </div>
    </div>

    <!-- Budget Overview -->
    <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 shadow-sm">
        <h3 class="mb-4 text-xl font-semibold text-gray-900 dark:text-white">預算總覽</h3>
        <div class="space-y-4">
            <!-- Total Budget -->
            <div class="flex items-center justify-between p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">總預算</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        ${{ number_format($budgetOverview['total_budget'], 0) }}
                    </p>
                </div>
                <svg class="w-10 h-10 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>

            <!-- Actual Cost -->
            <div class="flex items-center justify-between p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">實際支出</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        ${{ number_format($budgetOverview['total_actual_cost'], 0) }}
                    </p>
                </div>
                <svg class="w-10 h-10 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
            </div>

            <!-- Remaining Budget -->
            <div class="flex items-center justify-between p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">剩餘預算</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        ${{ number_format($budgetOverview['remaining_budget'], 0) }}
                    </p>
                </div>
                <svg class="w-10 h-10 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>

            <!-- Usage Percentage -->
            <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">預算使用率</span>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">
                        {{ $budgetOverview['total_budget'] > 0 ? number_format(($budgetOverview['total_actual_cost'] / $budgetOverview['total_budget']) * 100, 1) : 0 }}%
                    </span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 h-3 rounded-full transition-all duration-300" 
                         style="width: {{ $budgetOverview['total_budget'] > 0 ? min(($budgetOverview['total_actual_cost'] / $budgetOverview['total_budget']) * 100, 100) : 0 }}%">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Projects -->
<div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm">
    <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4">
        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">最近專案</h3>
    </div>
    <div class="p-6">
        @if($recentProjects->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">專案名稱</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">公司</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">部門</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">預算</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">狀態</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">建立時間</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($recentProjects as $project)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('tenant.projects.show', $project) }}" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline">
                                {{ $project->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                            {{ $project->company->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                            {{ $project->department->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                            ${{ number_format($project->budget, 0) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusColors = [
                                    'planning' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                    'in_progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
                                    'on_hold' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
                                    'completed' => 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
                                    'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
                                ];
                                $statusLabels = [
                                    'planning' => '規劃中',
                                    'in_progress' => '進行中',
                                    'on_hold' => '暫停',
                                    'completed' => '已完成',
                                    'cancelled' => '已取消',
                                ];
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$project->status] ?? '' }}">
                                {{ $statusLabels[$project->status] ?? $project->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $project->created_at->format('Y-m-d') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">沒有專案</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">開始建立您的第一個專案</p>
            <div class="mt-6">
                <a href="{{ route('tenant.projects.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    新增專案
                </a>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Chart.js Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Project Status Chart
    const ctx = document.getElementById('projectStatusChart');
    if (ctx) {
        const projectStats = @json($projectStats);
        
        const statusLabels = {
            'planning': '規劃中',
            'in_progress': '進行中',
            'on_hold': '暫停',
            'completed': '已完成',
            'cancelled': '已取消'
        };
        
        const labels = Object.keys(projectStats).map(key => statusLabels[key] || key);
        const data = Object.values(projectStats);
        
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: [
                        'rgb(156, 163, 175)', // gray
                        'rgb(59, 130, 246)',  // blue
                        'rgb(251, 191, 36)',  // yellow
                        'rgb(34, 197, 94)',   // green
                        'rgb(239, 68, 68)',   // red
                    ],
                    borderWidth: 2,
                    borderColor: document.documentElement.classList.contains('dark') ? '#1f2937' : '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: document.documentElement.classList.contains('dark') ? '#d1d5db' : '#374151',
                            padding: 15,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endsection
