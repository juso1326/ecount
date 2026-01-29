@extends('layouts.superadmin')

@section('title', '超級管理員儀表板')

@section('content')
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
