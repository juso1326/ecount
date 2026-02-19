@extends('layouts.tenant')

@section('title', '專案損益分析')

@section('page-title', '專案損益分析')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center">
        <form method="GET" class="flex gap-2">
            <select name="status" onchange="this.form.submit()" 
                    class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2">
                <option value="">全部狀態</option>
                <option value="active" {{ $status == 'active' ? 'selected' : '' }}>進行中</option>
                <option value="completed" {{ $status == 'completed' ? 'selected' : '' }}>已完成</option>
            </select>
        </form>
    </div>
</div>

<!-- 總覽卡片 -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">專案總數</div>
        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
            {{ $summary['total_projects'] }}
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">總預算</div>
        <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
            NT$ {{ number_format($summary['total_budget'], 0) }}
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">總收入</div>
        <div class="text-2xl font-bold text-green-600 dark:text-green-400">
            NT$ {{ number_format($summary['total_revenue'], 0) }}
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">總成本</div>
        <div class="text-2xl font-bold text-red-600 dark:text-red-400">
            NT$ {{ number_format($summary['total_cost'], 0) }}
        </div>
    </div>
</div>

<!-- 專案毛利排名 -->
<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">專案毛利排名</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">專案代碼</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">專案名稱</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">收入</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">成本</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">毛利</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">毛利率</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($projectProfits as $project)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600 dark:text-blue-400">
                        <a href="{{ route('tenant.projects.show', $project->id) }}" target="_blank" class="hover:underline">
                            {{ $project->code }}
                        </a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $project->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-green-600 dark:text-green-400">
                        NT$ {{ number_format($project->revenue, 0) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-red-600 dark:text-red-400">
                        NT$ {{ number_format($project->cost, 0) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold {{ $project->profit >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                        NT$ {{ number_format($project->profit, 0) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                        <span class="px-2 py-1 rounded {{ $project->margin >= 30 ? 'bg-green-100 text-green-800' : ($project->margin >= 10 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                            {{ number_format($project->margin, 1) }}%
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- 預算對比分析 -->
<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">預算對比分析</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">專案代碼</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">專案名稱</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">預算金額</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">實際成本</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">差異</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">達成率</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($budgetComparison as $project)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600 dark:text-blue-400">
                        <a href="{{ route('tenant.projects.show', $project->id) }}" class="hover:underline">
                            {{ $project->code }}
                        </a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $project->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 dark:text-white">
                        NT$ {{ number_format($project->budget, 0) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 dark:text-white">
                        NT$ {{ number_format($project->actual_cost, 0) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right {{ $project->variance >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        NT$ {{ number_format($project->variance, 0) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                        <span class="px-2 py-1 rounded {{ $project->usage_rate <= 80 ? 'bg-green-100 text-green-800' : ($project->usage_rate <= 100 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                            {{ number_format($project->usage_rate, 1) }}%
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
