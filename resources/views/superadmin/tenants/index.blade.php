@extends('layouts.superadmin')

@section('title', '租戶管理')
@section('page-title', '租戶管理')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">租戶列表</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">管理所有租戶帳號與方案</p>
        </div>
        <a href="{{ route('superadmin.tenants.create') }}" 
           class="inline-flex items-center px-4 py-2.5 bg-primary hover:bg-primary/90 text-white font-medium rounded-lg transition shadow-md hover:shadow-lg">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            新增租戶
        </a>
    </div>

    <!-- Filter Card -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="p-6">
            <form method="GET" action="{{ route('superadmin.tenants.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">搜尋</label>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="租戶 ID、名稱、Email..." 
                               class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent dark:bg-gray-700 dark:text-white">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">狀態</label>
                        <select name="status" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent dark:bg-gray-700 dark:text-white">
                            <option value="">全部狀態</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>啟用中</option>
                            <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>已暫停</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>未啟用</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">方案</label>
                        <select name="plan" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent dark:bg-gray-700 dark:text-white">
                            <option value="">全部方案</option>
                            <option value="basic" {{ request('plan') === 'basic' ? 'selected' : '' }}>Basic</option>
                            <option value="professional" {{ request('plan') === 'professional' ? 'selected' : '' }}>Professional</option>
                            <option value="enterprise" {{ request('plan') === 'enterprise' ? 'selected' : '' }}>Enterprise</option>
                        </select>
                    </div>
                    
                    <div class="flex items-end gap-2">
                        <button type="submit" class="flex-1 px-4 py-2.5 bg-gray-700 hover:bg-gray-800 dark:bg-gray-600 dark:hover:bg-gray-500 text-white font-medium rounded-lg transition">
                            搜尋
                        </button>
                        @if(request()->hasAny(['search', 'status', 'plan']))
                            <a href="{{ route('superadmin.tenants.index') }}" 
                               class="px-4 py-2.5 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg transition">
                                清除
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">租戶資訊</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">方案</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">狀態</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">建立時間</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">操作</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($tenants as $tenant)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-primary rounded-lg flex items-center justify-center text-white font-bold">
                                    {{ strtoupper(substr($tenant->id, 0, 2)) }}
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $tenant->name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $tenant->email }}</div>
                                    <div class="text-xs text-gray-400 dark:text-gray-500 font-mono">ID: {{ $tenant->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                @if($tenant->plan === 'basic') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                                @elseif($tenant->plan === 'professional') bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400
                                @else bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400
                                @endif">
                                {{ ucfirst($tenant->plan) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($tenant->status === 'active')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span>
                                    啟用中
                                </span>
                            @elseif($tenant->status === 'suspended')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                                    <span class="w-1.5 h-1.5 bg-yellow-500 rounded-full mr-1.5"></span>
                                    已暫停
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400">
                                    <span class="w-1.5 h-1.5 bg-gray-500 rounded-full mr-1.5"></span>
                                    未啟用
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $tenant->created_at->format('Y-m-d H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('superadmin.tenants.show', $tenant) }}" 
                                   class="text-primary hover:text-primary/80 dark:text-primary/90 dark:hover:text-primary transition">
                                    查看
                                </a>
                                <span class="text-gray-300 dark:text-gray-600">|</span>
                                <a href="{{ route('superadmin.tenants.edit', $tenant) }}" 
                                   class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 transition">
                                    編輯
                                </a>
                                @if($tenant->status === 'active')
                                    <span class="text-gray-300 dark:text-gray-600">|</span>
                                    <form action="{{ route('superadmin.tenants.suspend', $tenant) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 dark:hover:text-yellow-300 transition" 
                                                onclick="return confirm('確定要暫停此租戶嗎？')">
                                            暫停
                                        </button>
                                    </form>
                                @elseif($tenant->status === 'suspended')
                                    <span class="text-gray-300 dark:text-gray-600">|</span>
                                    <form action="{{ route('superadmin.tenants.activate', $tenant) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300 transition">
                                            啟用
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <svg class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">沒有找到任何租戶</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($tenants->hasPages())
        <div class="border-t border-gray-200 dark:border-gray-700 px-6 py-4">
            {{ $tenants->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
