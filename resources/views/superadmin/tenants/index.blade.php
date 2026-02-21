@extends('layouts.superadmin')

@section('title', '租戶管理')
@section('page-title', '租戶管理')

@section('content')
<div class="space-y-4">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">租戶列表</h2>
        <a href="{{ route('superadmin.tenants.create') }}"
           class="inline-flex items-center px-3 py-2 bg-primary hover:bg-primary/90 text-white text-sm font-medium rounded-lg transition">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            新增租戶
        </a>
    </div>

    <!-- 篩選 -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <form method="GET" action="{{ route('superadmin.tenants.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="ID、名稱、Email..."
                       class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent">

                <select name="status" class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-primary">
                    <option value="">全部狀態</option>
                    <option value="active"    {{ request('status') === 'active'    ? 'selected' : '' }}>啟用中</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>已暫停</option>
                    <option value="inactive"  {{ request('status') === 'inactive'  ? 'selected' : '' }}>未啟用</option>
                </select>

                <select name="plan" class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-primary">
                    <option value="">全部方案</option>
                    <option value="basic"        {{ request('plan') === 'basic'        ? 'selected' : '' }}>基礎</option>
                    <option value="professional" {{ request('plan') === 'professional' ? 'selected' : '' }}>專業</option>
                    <option value="enterprise"   {{ request('plan') === 'enterprise'   ? 'selected' : '' }}>企業</option>
                </select>

                <select name="expiry" class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-primary">
                    <option value="">全部到期狀態</option>
                    <option value="expiring" {{ request('expiry') === 'expiring' ? 'selected' : '' }}>7天內到期</option>
                    <option value="expired"  {{ request('expiry') === 'expired'  ? 'selected' : '' }}>已到期</option>
                </select>

                <div class="flex gap-2">
                    <button type="submit" class="flex-1 px-3 py-2 text-sm bg-gray-700 hover:bg-gray-800 text-white rounded-lg transition">搜尋</button>
                    @if(request()->hasAny(['search','status','plan','expiry']))
                        <a href="{{ route('superadmin.tenants.index') }}"
                           class="px-3 py-2 text-sm bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition">清除</a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- 快到期提示 -->
    @php
        $expiringCount = $tenants->filter(fn($t) => $t->isPlanExpiringSoon())->count();
        $expiredCount  = $tenants->filter(fn($t) => $t->isPlanExpired())->count();
    @endphp
    @if($expiringCount > 0 || $expiredCount > 0)
    <div class="flex flex-wrap gap-3">
        @if($expiredCount > 0)
        <div class="flex items-center gap-2 px-3 py-2 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg text-sm text-red-700 dark:text-red-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>已到期：<strong>{{ $expiredCount }}</strong> 個租戶</span>
        </div>
        @endif
        @if($expiringCount > 0)
        <div class="flex items-center gap-2 px-3 py-2 bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg text-sm text-orange-700 dark:text-orange-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>7天內到期：<strong>{{ $expiringCount }}</strong> 個租戶</span>
        </div>
        @endif
    </div>
    @endif

    <!-- 表格 -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-3 py-1 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">租戶</th>
                    <th class="px-3 py-1 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">方案</th>
                    <th class="px-3 py-1 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">狀態</th>
                    <th class="px-3 py-1 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">到期日</th>
                    <th class="px-3 py-1 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">建立日</th>
                    <th class="px-3 py-1 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">操作</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($tenants as $tenant)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-750 transition
                    @if($tenant->isPlanExpired()) bg-red-50/40 dark:bg-red-900/10
                    @elseif($tenant->isPlanExpiringSoon()) bg-orange-50/40 dark:bg-orange-900/10
                    @endif">

                    <!-- 租戶資訊 -->
                    <td class="px-3 py-2 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $tenant->name }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $tenant->email }}</div>
                        <div class="text-xs text-gray-400 dark:text-gray-500 font-mono">{{ $tenant->id }}</div>
                    </td>

                    <!-- 方案 -->
                    <td class="px-3 py-2 whitespace-nowrap">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                            @if($tenant->plan === 'basic') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                            @elseif($tenant->plan === 'professional') bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400
                            @else bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400
                            @endif">
                            {{ $tenant->plan_name }}
                        </span>
                    </td>

                    <!-- 狀態 -->
                    <td class="px-3 py-2 whitespace-nowrap">
                        @if($tenant->status === 'active')
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1"></span>啟用中
                            </span>
                        @elseif($tenant->status === 'suspended')
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                                <span class="w-1.5 h-1.5 bg-yellow-500 rounded-full mr-1"></span>已暫停
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-400">
                                <span class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1"></span>未啟用
                            </span>
                        @endif
                    </td>

                    <!-- 到期日 -->
                    <td class="px-3 py-2 whitespace-nowrap">
                        @if($tenant->plan_ends_at)
                            @if($tenant->isPlanExpired())
                                <span class="inline-flex items-center gap-1 text-xs font-medium text-red-600 dark:text-red-400">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    已到期 {{ $tenant->plan_ends_at->format('Y-m-d') }}
                                </span>
                            @elseif($tenant->isPlanExpiringSoon())
                                <span class="inline-flex items-center gap-1 text-xs font-medium text-orange-600 dark:text-orange-400">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ $tenant->planDaysRemaining() }} 天後到期
                                </span>
                            @else
                                <span class="text-xs text-gray-600 dark:text-gray-400">{{ $tenant->plan_ends_at->format('Y-m-d') }}</span>
                            @endif
                        @else
                            <span class="text-xs text-gray-400 dark:text-gray-500">無期限</span>
                        @endif
                    </td>

                    <!-- 建立日 -->
                    <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">
                        {{ $tenant->created_at->format('Y-m-d') }}
                    </td>

                    <!-- 操作 -->
                    <td class="px-3 py-2 whitespace-nowrap text-center text-sm">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('superadmin.tenants.show', $tenant) }}"
                               class="text-primary hover:text-primary/80 text-xs font-medium">查看</a>
                            <span class="text-gray-300 dark:text-gray-600">|</span>
                            <a href="{{ route('superadmin.tenants.edit', $tenant) }}"
                               class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 text-xs font-medium">編輯</a>
                            @if($tenant->status === 'active')
                                <span class="text-gray-300 dark:text-gray-600">|</span>
                                <form action="{{ route('superadmin.tenants.suspend', $tenant) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 text-xs font-medium"
                                            onclick="return confirm('確定要暫停此租戶嗎？')">暫停</button>
                                </form>
                            @elseif($tenant->status === 'suspended')
                                <span class="text-gray-300 dark:text-gray-600">|</span>
                                <form action="{{ route('superadmin.tenants.activate', $tenant) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-800 dark:text-green-400 text-xs font-medium">啟用</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                        沒有找到任何租戶
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($tenants->hasPages())
        <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-3">
            {{ $tenants->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
