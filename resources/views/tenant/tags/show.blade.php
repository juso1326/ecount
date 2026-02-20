@extends('layouts.tenant')

@section('title', '標籤詳細')

@section('content')
<!-- 麵包屑 -->
<div class="mb-4">
    <p class="text-sm text-gray-600 dark:text-gray-400">
        <a href="{{ route('tenant.tags.index', ['type' => $tag->type]) }}" class="hover:text-primary">標籤管理</a>
        <span class="mx-2">/</span>
        {{ $tag->name }}
    </p>
</div>

<!-- 標題與操作 -->
<div class="mb-4 flex justify-between items-center">
    <div class="flex items-center gap-3">
        <span class="inline-flex items-center px-4 py-2 rounded-full text-base font-semibold"
              style="background-color: {{ $tag->color }}20; color: {{ $tag->color }};">
            {{ $tag->name }}
        </span>
        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $types[$tag->type] ?? $tag->type }}</span>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('tenant.tags.edit', $tag) }}"
           class="px-4 py-2 text-sm font-medium text-white bg-primary hover:bg-primary-dark rounded-lg">
            編輯
        </a>
        <a href="{{ route('tenant.tags.index', ['type' => $tag->type]) }}"
           class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600">
            返回列表
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- 基本資訊 -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">基本資訊</h2>
        <dl class="space-y-3">
            <div class="flex justify-between">
                <dt class="text-sm text-gray-500 dark:text-gray-400">標籤名稱</dt>
                <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $tag->name }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-sm text-gray-500 dark:text-gray-400">類型</dt>
                <dd class="text-sm text-gray-900 dark:text-white">{{ $types[$tag->type] ?? $tag->type }}</dd>
            </div>
            <div class="flex justify-between items-center">
                <dt class="text-sm text-gray-500 dark:text-gray-400">顏色</dt>
                <dd class="flex items-center gap-2">
                    <div class="w-5 h-5 rounded" style="background-color: {{ $tag->color }};"></div>
                    <span class="text-sm text-gray-900 dark:text-white">{{ $tag->color }}</span>
                </dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-sm text-gray-500 dark:text-gray-400">排序</dt>
                <dd class="text-sm text-gray-900 dark:text-white">{{ $tag->sort_order }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-sm text-gray-500 dark:text-gray-400">說明</dt>
                <dd class="text-sm text-gray-900 dark:text-white">{{ $tag->description ?? '-' }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-sm text-gray-500 dark:text-gray-400">狀態</dt>
                <dd>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $tag->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $tag->is_active ? '啟用' : '停用' }}
                    </span>
                </dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-sm text-gray-500 dark:text-gray-400">建立時間</dt>
                <dd class="text-sm text-gray-500 dark:text-gray-400">@date($tag->created_at)</dd>
            </div>
        </dl>
    </div>

    <!-- 使用情況 -->
    <div class="lg:col-span-2 space-y-4">
        @if($tag->type === 'project' && $tag->projects->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                使用此標籤的專案 <span class="text-sm font-normal text-gray-500">({{ $tag->projects->count() }})</span>
            </h2>
            <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($tag->projects->take(10) as $project)
                <li class="py-2 flex items-center justify-between">
                    <div>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $project->name }}</span>
                        <span class="ml-2 text-xs text-gray-500">{{ $project->code }}</span>
                    </div>
                    <a href="{{ route('tenant.projects.show', $project) }}" class="text-xs text-primary hover:underline">查看</a>
                </li>
                @endforeach
            </ul>
        </div>
        @endif

        @if($tag->type === 'company' && $tag->companies->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                使用此標籤的公司 <span class="text-sm font-normal text-gray-500">({{ $tag->companies->count() }})</span>
            </h2>
            <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($tag->companies->take(10) as $company)
                <li class="py-2 flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $company->name }}</span>
                    <a href="{{ route('tenant.companies.show', $company) }}" class="text-xs text-primary hover:underline">查看</a>
                </li>
                @endforeach
            </ul>
        </div>
        @endif

        @if($tag->type === 'user' && $tag->users->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                使用此標籤的成員 <span class="text-sm font-normal text-gray-500">({{ $tag->users->count() }})</span>
            </h2>
            <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($tag->users->take(10) as $user)
                <li class="py-2 flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->name }}</span>
                    <a href="{{ route('tenant.users.show', $user) }}" class="text-xs text-primary hover:underline">查看</a>
                </li>
                @endforeach
            </ul>
        </div>
        @endif

        @if(($tag->type === 'project' && $tag->projects->count() === 0) ||
            ($tag->type === 'company' && $tag->companies->count() === 0) ||
            ($tag->type === 'user' && $tag->users->count() === 0) ||
            $tag->type === 'payment_method')
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 text-center text-sm text-gray-500 dark:text-gray-400">
            此標籤尚未被使用
        </div>
        @endif
    </div>
</div>
@endsection
