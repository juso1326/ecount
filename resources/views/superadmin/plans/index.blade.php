@extends('layouts.superadmin')

@section('title', '方案管理')
@section('page-title', '方案管理')

@section('content')
<div class="space-y-4">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">方案列表</h2>
        <a href="{{ route('superadmin.plans.create') }}"
           class="inline-flex items-center px-3 py-2 bg-primary hover:bg-primary/90 text-white text-sm font-medium rounded-lg transition">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            新增方案
        </a>
    </div>

    <!-- 表格 -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">方案名稱</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">代碼</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">月費</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">年費</th>
                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">使用人數上限</th>
                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">使用租戶</th>
                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">狀態</th>
                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">操作</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($plans as $plan)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-750 transition">
                    <!-- 名稱 -->
                    <td class="px-3 py-2 whitespace-nowrap">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $plan->name }}</span>
                            @if($plan->is_featured)
                                <span class="px-1.5 py-0.5 bg-indigo-100 text-indigo-700 text-xs rounded-full font-medium">推薦</span>
                            @endif
                        </div>
                        @if($plan->description)
                            <div class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 max-w-xs truncate">{{ $plan->description }}</div>
                        @endif
                    </td>

                    <!-- 代碼 -->
                    <td class="px-3 py-2 whitespace-nowrap">
                        <code class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-1.5 py-0.5 rounded">{{ $plan->slug }}</code>
                    </td>

                    <!-- 月費 -->
                    <td class="px-3 py-2 whitespace-nowrap text-right text-sm text-gray-700 dark:text-gray-300">
                        NT${{ number_format($plan->price) }}
                    </td>

                    <!-- 年費 -->
                    <td class="px-3 py-2 whitespace-nowrap text-right">
                        @if($plan->annual_price)
                            <span class="text-sm text-gray-700 dark:text-gray-300">NT${{ number_format($plan->annual_price) }}</span>
                            <span class="text-xs text-green-600 dark:text-green-400 ml-1">省{{ $plan->annual_discount_percentage }}%</span>
                        @else
                            <span class="text-xs text-gray-400">—</span>
                        @endif
                    </td>

                    <!-- 人數上限 -->
                    <td class="px-3 py-2 whitespace-nowrap text-center text-sm text-gray-600 dark:text-gray-400">
                        {{ $plan->max_users ? $plan->max_users.' 人' : '不限' }}
                    </td>

                    <!-- 使用租戶 -->
                    <td class="px-3 py-2 whitespace-nowrap text-center">
                        <a href="{{ route('superadmin.tenants.index', ['plan' => $plan->slug]) }}"
                           class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-50 text-indigo-700 hover:bg-indigo-100 dark:bg-indigo-900/30 dark:text-indigo-400 transition">
                            {{ $plan->tenants_count }} 個
                        </a>
                    </td>

                    <!-- 狀態 -->
                    <td class="px-3 py-2 whitespace-nowrap text-center">
                        @if($plan->is_active)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1"></span>啟用
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                <span class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1"></span>停用
                            </span>
                        @endif
                    </td>

                    <!-- 操作 -->
                    <td class="px-3 py-2 whitespace-nowrap text-center text-sm">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('superadmin.plans.show', $plan) }}"
                               class="text-primary hover:text-primary/80 text-xs font-medium">查看</a>
                            <span class="text-gray-300 dark:text-gray-600">|</span>
                            <a href="{{ route('superadmin.plans.edit', $plan) }}"
                               class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 text-xs font-medium">編輯</a>
                            <span class="text-gray-300 dark:text-gray-600">|</span>
                            <form action="{{ route('superadmin.plans.toggle-active', $plan) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                    class="text-xs font-medium {{ $plan->is_active ? 'text-yellow-600 hover:text-yellow-800 dark:text-yellow-400' : 'text-green-600 hover:text-green-800 dark:text-green-400' }}">
                                    {{ $plan->is_active ? '停用' : '啟用' }}
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                        尚無方案，<a href="{{ route('superadmin.plans.create') }}" class="text-primary hover:underline">立即新增</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($plans->hasPages())
        <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-3">
            {{ $plans->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
