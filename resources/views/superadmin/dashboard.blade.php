@extends('layouts.superadmin')

@section('title', '儀表板')
@section('page-title', '儀表板')

@section('content')
<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- Total Tenants Card -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">總租戶數</p>
                <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $stats['total_tenants'] }}</h3>
            </div>
            <div class="p-3 bg-primary/10 rounded-lg">
                <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Active Tenants Card -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">啟用中</p>
                <h3 class="text-3xl font-bold text-green-600 dark:text-green-400 mt-2">{{ $stats['active_tenants'] }}</h3>
            </div>
            <div class="p-3 bg-green-100 dark:bg-green-900/20 rounded-lg">
                <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Suspended Tenants Card -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">已暫停</p>
                <h3 class="text-3xl font-bold text-yellow-600 dark:text-yellow-400 mt-2">{{ $stats['suspended_tenants'] }}</h3>
            </div>
            <div class="p-3 bg-yellow-100 dark:bg-yellow-900/20 rounded-lg">
                <svg class="w-8 h-8 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Inactive Tenants Card -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">未啟用</p>
                <h3 class="text-3xl font-bold text-gray-600 dark:text-gray-400 mt-2">{{ $stats['inactive_tenants'] }}</h3>
            </div>
            <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-lg">
                <svg class="w-8 h-8 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Charts & Tables Grid -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Plan Statistics Card -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">方案統計</h3>
        </div>
        <div class="p-6 space-y-6">
            <!-- Basic Plan -->
            <div>
                <div class="flex justify-between items-center mb-2">
                    <div class="flex items-center">
                        <span class="inline-block w-3 h-3 bg-blue-500 rounded-full mr-2"></span>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Basic</span>
                    </div>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $planStats['basic'] ?? 0 }}</span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                    <div class="bg-blue-500 h-2.5 rounded-full transition-all duration-300" style="width: {{ $stats['total_tenants'] > 0 ? round((($planStats['basic'] ?? 0) / $stats['total_tenants'] * 100), 1) : 0 }}%"></div>
                </div>
            </div>

            <!-- Professional Plan -->
            <div>
                <div class="flex justify-between items-center mb-2">
                    <div class="flex items-center">
                        <span class="inline-block w-3 h-3 bg-indigo-500 rounded-full mr-2"></span>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Professional</span>
                    </div>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $planStats['professional'] ?? 0 }}</span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                    <div class="bg-indigo-500 h-2.5 rounded-full transition-all duration-300" style="width: {{ $stats['total_tenants'] > 0 ? round((($planStats['professional'] ?? 0) / $stats['total_tenants'] * 100), 1) : 0 }}%"></div>
                </div>
            </div>

            <!-- Enterprise Plan -->
            <div>
                <div class="flex justify-between items-center mb-2">
                    <div class="flex items-center">
                        <span class="inline-block w-3 h-3 bg-purple-500 rounded-full mr-2"></span>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Enterprise</span>
                    </div>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $planStats['enterprise'] ?? 0 }}</span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                    <div class="bg-purple-500 h-2.5 rounded-full transition-all duration-300" style="width: {{ $stats['total_tenants'] > 0 ? round((($planStats['enterprise'] ?? 0) / $stats['total_tenants'] * 100), 1) : 0 }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Tenants Card -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">最近建立的租戶</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @forelse($recentTenants as $tenant)
                    <div class="flex items-center justify-between py-3 border-b border-gray-200 dark:border-gray-700 last:border-0">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                                <span class="text-primary font-semibold">{{ strtoupper(substr($tenant->id, 0, 2)) }}</span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $tenant->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $tenant->id }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                @if($tenant->status === 'active') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                @elseif($tenant->status === 'suspended') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400
                                @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400
                                @endif">
                                @if($tenant->status === 'active')
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1"></span>
                                @endif
                                {{ ucfirst($tenant->status) }}
                            </span>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $tenant->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">尚無租戶</p>
                    </div>
                @endforelse
            </div>
            
            @if($recentTenants->count() > 0)
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('superadmin.tenants.index') }}" 
                       class="text-primary hover:text-primary/80 text-sm font-medium inline-flex items-center">
                        查看所有租戶
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- System Info Card -->
<div class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">系統資訊</h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">PHP 版本</p>
                    <p class="font-semibold text-gray-900 dark:text-white">{{ $systemInfo['php_version'] }}</p>
                </div>
            </div>
            
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-red-100 dark:bg-red-900/20 rounded-lg">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Laravel 版本</p>
                    <p class="font-semibold text-gray-900 dark:text-white">{{ $systemInfo['laravel_version'] }}</p>
                </div>
            </div>
            
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-green-100 dark:bg-green-900/20 rounded-lg">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">資料庫</p>
                    <p class="font-semibold text-gray-900 dark:text-white">{{ ucfirst($systemInfo['database_connection']) }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">儀表板</h1>
    <p class="mt-2 text-gray-600">系統總覽與統計資訊</p>
</div>

<!-- 統計卡片 -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <!-- 總租戶數 -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">總租戶數</dt>
                    <dd class="text-3xl font-semibold text-gray-900">{{ $stats['total_tenants'] }}</dd>
                </dl>
            </div>
        </div>
    </div>

    <!-- 啟用中 -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">啟用中</dt>
                    <dd class="text-3xl font-semibold text-green-600">{{ $stats['active_tenants'] }}</dd>
                </dl>
            </div>
        </div>
    </div>

    <!-- 已暫停 -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">已暫停</dt>
                    <dd class="text-3xl font-semibold text-yellow-600">{{ $stats['suspended_tenants'] }}</dd>
                </dl>
            </div>
        </div>
    </div>

    <!-- 未啟用 -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-gray-500 rounded-md p-3">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">未啟用</dt>
                    <dd class="text-3xl font-semibold text-gray-600">{{ $stats['inactive_tenants'] }}</dd>
                </dl>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- 方案統計 -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">方案統計</h2>
        <div class="space-y-4">
            <div class="flex justify-between items-center">
                <span class="text-gray-600">Basic</span>
                <span class="font-semibold text-gray-900">{{ $planStats['basic'] ?? 0 }} 個租戶</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $stats['total_tenants'] > 0 ? (($planStats['basic'] ?? 0) / $stats['total_tenants'] * 100) : 0 }}%"></div>
            </div>

            <div class="flex justify-between items-center">
                <span class="text-gray-600">Professional</span>
                <span class="font-semibold text-gray-900">{{ $planStats['professional'] ?? 0 }} 個租戶</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-indigo-500 h-2 rounded-full" style="width: {{ $stats['total_tenants'] > 0 ? (($planStats['professional'] ?? 0) / $stats['total_tenants'] * 100) : 0 }}%"></div>
            </div>

            <div class="flex justify-between items-center">
                <span class="text-gray-600">Enterprise</span>
                <span class="font-semibold text-gray-900">{{ $planStats['enterprise'] ?? 0 }} 個租戶</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-purple-500 h-2 rounded-full" style="width: {{ $stats['total_tenants'] > 0 ? (($planStats['enterprise'] ?? 0) / $stats['total_tenants'] * 100) : 0 }}%"></div>
            </div>
        </div>
    </div>

    <!-- 最近建立的租戶 -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">最近建立的租戶</h2>
        <div class="space-y-3">
            @forelse($recentTenants as $tenant)
                <div class="flex justify-between items-center border-b pb-3">
                    <div>
                        <p class="font-medium text-gray-900">{{ $tenant->name }}</p>
                        <p class="text-sm text-gray-500">{{ $tenant->id }}</p>
                    </div>
                    <div class="text-right">
                        <span class="px-2 py-1 text-xs rounded-full 
                            @if($tenant->status === 'active') bg-green-100 text-green-800
                            @elseif($tenant->status === 'suspended') bg-yellow-100 text-yellow-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ $tenant->status }}
                        </span>
                        <p class="text-xs text-gray-500 mt-1">{{ $tenant->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            @empty
                <p class="text-gray-500">尚無租戶</p>
            @endforelse
        </div>
        
        @if($recentTenants->count() > 0)
            <div class="mt-4">
                <a href="{{ route('superadmin.tenants.index') }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                    查看所有租戶 →
                </a>
            </div>
        @endif
    </div>
</div>

<!-- 系統資訊 -->
<div class="mt-6 bg-white rounded-lg shadow p-6">
    <h2 class="text-lg font-semibold text-gray-900 mb-4">系統資訊</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <span class="text-sm text-gray-500">PHP 版本</span>
            <p class="font-medium text-gray-900">{{ $systemInfo['php_version'] }}</p>
        </div>
        <div>
            <span class="text-sm text-gray-500">Laravel 版本</span>
            <p class="font-medium text-gray-900">{{ $systemInfo['laravel_version'] }}</p>
        </div>
        <div>
            <span class="text-sm text-gray-500">資料庫連線</span>
            <p class="font-medium text-gray-900">{{ $systemInfo['database_connection'] }}</p>
        </div>
    </div>
</div>
@endsection
