@extends('layouts.superadmin')

@section('title', '方案管理')
@section('page-title', '方案管理')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">方案管理</h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">管理系統租用方案與價格設定</p>
    </div>
    <a href="{{ route('superadmin.plans.create') }}" 
       class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        新增方案
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($plans as $plan)
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border {{ $plan->is_featured ? 'border-primary' : 'border-gray-200 dark:border-gray-700' }} relative">
        @if($plan->is_featured)
        <div class="absolute top-0 right-0 bg-primary text-white text-xs font-bold px-3 py-1 rounded-bl-lg rounded-tr-lg">
            推薦
        </div>
        @endif
        <div class="p-6">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $plan->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $plan->slug }}</p>
                </div>
                <span class="px-2 py-1 text-xs rounded-full {{ $plan->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                    {{ $plan->is_active ? '啟用' : '停用' }}
                </span>
            </div>
            <div class="mb-4">
                <div class="flex items-baseline">
                    <span class="text-3xl font-bold text-gray-900 dark:text-white">${{ number_format($plan->price, 0) }}</span>
                    <span class="ml-2 text-gray-500">/月</span>
                </div>
            </div>
            <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600">使用租戶</span>
                    <span class="font-medium">{{ $plan->tenants_count }} 個</span>
                </div>
            </div>
            <div class="mt-4 flex gap-2">
                <a href="{{ route('superadmin.plans.show', $plan) }}" 
                   class="flex-1 text-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                    查看
                </a>
                <a href="{{ route('superadmin.plans.edit', $plan) }}" 
                   class="flex-1 text-center px-4 py-2 bg-primary/10 text-primary rounded-lg hover:bg-primary/20">
                    編輯
                </a>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full bg-white dark:bg-gray-800 rounded-lg p-12 text-center">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">尚無方案</h3>
        <a href="{{ route('superadmin.plans.create') }}" 
           class="mt-4 inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg">
            新增方案
        </a>
    </div>
    @endforelse
</div>
@endsection
