@extends('layouts.tenant')

@section('title', '儀表板')

@section('page-title', '儀表板')

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

<!-- System Announcement -->
@if($announcement)
<div class="mb-6 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 shadow-sm" x-data="{ editing: false }">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
            <svg class="w-6 h-6 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
            </svg>
            系統公告
        </h2>
        @if(auth()->user()->hasAnyRole(['admin', 'manager']))
        <button @click="editing = !editing" 
                class="text-sm text-primary hover:text-primary-dark font-medium" 
                x-text="editing ? '取消' : '編輯'">
            編輯
        </button>
        @endif
    </div>

    <!-- Display Mode -->
    <div x-show="!editing" class="prose dark:prose-invert max-w-none">
        <div class="text-gray-700 dark:text-gray-300">{!! nl2br(e($announcement->content)) !!}</div>
        @if($announcement->updated_at)
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-4">
            最後更新：{{ $announcement->updated_at->format('Y-m-d H:i') }}
            @if($announcement->updater)
                by {{ $announcement->updater->name }}
            @endif
        </p>
        @endif
    </div>

    <!-- Edit Mode -->
    @if(auth()->user()->hasAnyRole(['admin', 'manager']))
    <form x-show="editing" method="POST" action="{{ route('tenant.dashboard.announcement') }}" x-cloak>
        @csrf
        <textarea 
            name="content" 
            rows="6" 
            class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent"
            placeholder="輸入系統公告內容...">{{ $announcement->content }}</textarea>
        <div class="mt-4 flex justify-end space-x-3">
            <button type="button" @click="editing = false" 
                    class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                取消
            </button>
            <button type="submit" 
                    class="px-4 py-2 rounded-lg bg-primary text-white hover:bg-primary-dark">
                儲存
            </button>
        </div>
    </form>
    @endif
</div>
@endif

<!-- Reports Section -->
<div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3 mb-6">
    <!-- 報表卡片 1 -->
    <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">本月收入</h3>
            <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <div class="text-3xl font-bold text-gray-900 dark:text-white">$0</div>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">尚無收入資料</p>
    </div>

    <!-- 報表卡片 2 -->
    <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">本月支出</h3>
            <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
        </div>
        <div class="text-3xl font-bold text-gray-900 dark:text-white">$0</div>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">尚無支出資料</p>
    </div>

    <!-- 報表卡片 3 -->
    <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">進行中專案</h3>
            <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
        </div>
        <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $activeProjects }}</div>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">個進行中的專案</p>
    </div>
</div>

<!-- Recent Projects -->
@if($recentProjects->count() > 0)
<div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">最近專案</h2>
    </div>
    <div class="p-6">
        <div class="space-y-4">
            @foreach($recentProjects as $project)
            <div class="flex items-center justify-between p-4 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-750">
                <div class="flex-1">
                    <h3 class="font-medium text-gray-900 dark:text-white">{{ $project->name }}</h3>
                    <div class="flex items-center gap-4 mt-1 text-sm text-gray-500 dark:text-gray-400">
                        <span>{{ $project->code }}</span>
                        @if($project->company)
                        <span>{{ $project->company->name }}</span>
                        @endif
                        <span>{{ $project->start_date?->format('Y-m-d') }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    @if($project->status === 'active')
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                        進行中
                    </span>
                    @elseif($project->status === 'completed')
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                        已完成
                    </span>
                    @else
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                        {{ $project->status }}
                    </span>
                    @endif
                    <a href="{{ route('tenant.projects.show', $project) }}" class="text-primary hover:text-primary-dark">
                        查看
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="mt-4 text-center">
            <a href="{{ route('tenant.projects.index') }}" class="text-primary hover:text-primary-dark font-medium">
                查看所有專案 →
            </a>
        </div>
    </div>
</div>
@endif

@endsection
