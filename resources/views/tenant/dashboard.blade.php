@extends('layouts.tenant')

@section('title', '儀表板')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">儀表板</h1>
    <p class="mt-2 text-gray-600">歡迎回來，{{ auth()->user()->name }}</p>
</div>

<!-- 統計卡片 -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <!-- 公司數 -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">公司數</dt>
                    <dd class="text-3xl font-semibold text-gray-900">{{ $stats['total_companies'] }}</dd>
                </dl>
            </div>
        </div>
    </div>

    <!-- 部門數 -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">部門數</dt>
                    <dd class="text-3xl font-semibold text-gray-900">{{ $stats['total_departments'] }}</dd>
                </dl>
            </div>
        </div>
    </div>

    <!-- 專案數 -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">專案數</dt>
                    <dd class="text-3xl font-semibold text-gray-900">{{ $stats['total_projects'] }}</dd>
                </dl>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- 專案狀態統計 -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">專案狀態</h2>
        <div class="space-y-4">
            <div class="flex justify-between items-center">
                <span class="text-gray-600">進行中</span>
                <span class="font-semibold text-blue-600">{{ $stats['active_projects'] }} 個</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-600">已完成</span>
                <span class="font-semibold text-green-600">{{ $stats['completed_projects'] }} 個</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-600">規劃中</span>
                <span class="font-semibold text-gray-600">{{ $projectStats['planning'] ?? 0 }} 個</span>
            </div>
        </div>
    </div>

    <!-- 預算總覽 -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">預算總覽</h2>
        <div class="space-y-4">
            <div>
                <div class="flex justify-between mb-1">
                    <span class="text-sm text-gray-600">總預算</span>
                    <span class="text-sm font-semibold">{{ number_format($budgetOverview['total_budget']) }}</span>
                </div>
            </div>
            <div>
                <div class="flex justify-between mb-1">
                    <span class="text-sm text-gray-600">已使用</span>
                    <span class="text-sm font-semibold">{{ number_format($budgetOverview['total_actual_cost']) }}</span>
                </div>
            </div>
            <div>
                <div class="flex justify-between mb-1">
                    <span class="text-sm text-gray-600">剩餘</span>
                    <span class="text-sm font-semibold {{ $budgetOverview['remaining_budget'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ number_format($budgetOverview['remaining_budget']) }}
                    </span>
                </div>
            </div>
            @if($budgetOverview['total_budget'] > 0)
            <div class="w-full bg-gray-200 rounded-full h-3 mt-4">
                <div class="bg-blue-500 h-3 rounded-full" style="width: {{ min(($budgetOverview['total_actual_cost'] / $budgetOverview['total_budget'] * 100), 100) }}%"></div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- 最近專案 -->
<div class="mt-6 bg-white rounded-lg shadow p-6">
    <h2 class="text-lg font-semibold text-gray-900 mb-4">最近專案</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">專案名稱</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">公司</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">部門</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">狀態</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">操作</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($recentProjects as $project)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $project->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $project->company->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $project->department?->name ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $statusColors = [
                                'planning' => 'bg-gray-100 text-gray-800',
                                'in_progress' => 'bg-blue-100 text-blue-800',
                                'on_hold' => 'bg-yellow-100 text-yellow-800',
                                'completed' => 'bg-green-100 text-green-800',
                                'cancelled' => 'bg-red-100 text-red-800',
                            ];
                        @endphp
                        <span class="px-2 py-1 text-xs rounded-full {{ $statusColors[$project->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $project->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="{{ route('tenant.projects.show', $project) }}" class="text-blue-600 hover:text-blue-900">查看</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">尚無專案</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($recentProjects->count() > 0)
        <div class="mt-4">
            <a href="{{ route('tenant.projects.index') }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                查看所有專案 →
            </a>
        </div>
    @endif
</div>
@endsection
