@extends('layouts.tenant')

@section('title', '支出項目詳細')

@section('content')
<!-- 麵包屑 -->
<div class="mb-4">
    <p class="text-sm text-gray-600 dark:text-gray-400">
        <a href="{{ route('tenant.expense-categories.index') }}" class="hover:text-primary">支出項目</a>
        <span class="mx-2">/</span>
        {{ $expenseCategory->name }}
    </p>
</div>

<!-- 操作按鈕 -->
<div class="mb-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $expenseCategory->name }}</h1>
    <div class="flex gap-2">
        <a href="{{ route('tenant.expense-categories.edit', $expenseCategory) }}"
           class="px-4 py-2 text-sm font-medium text-white bg-primary hover:bg-primary-dark rounded-lg">
            編輯
        </a>
        <a href="{{ route('tenant.expense-categories.index') }}"
           class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600">
            返回列表
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- 基本資訊 -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">基本資訊</h2>
        <dl class="space-y-3">
            <div class="flex justify-between">
                <dt class="text-sm text-gray-500 dark:text-gray-400">代碼</dt>
                <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $expenseCategory->code }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-sm text-gray-500 dark:text-gray-400">名稱</dt>
                <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $expenseCategory->name }}</dd>
            </div>
            @if($expenseCategory->parent)
            <div class="flex justify-between">
                <dt class="text-sm text-gray-500 dark:text-gray-400">上層分類</dt>
                <dd class="text-sm font-medium text-gray-900 dark:text-white">
                    <a href="{{ route('tenant.expense-categories.show', $expenseCategory->parent) }}" class="text-primary hover:underline">
                        {{ $expenseCategory->parent->name }}
                    </a>
                </dd>
            </div>
            @endif
            <div class="flex justify-between">
                <dt class="text-sm text-gray-500 dark:text-gray-400">完整路徑</dt>
                <dd class="text-sm text-gray-900 dark:text-white">{{ $expenseCategory->full_name }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-sm text-gray-500 dark:text-gray-400">排序</dt>
                <dd class="text-sm text-gray-900 dark:text-white">{{ $expenseCategory->sort_order }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-sm text-gray-500 dark:text-gray-400">說明</dt>
                <dd class="text-sm text-gray-900 dark:text-white">{{ $expenseCategory->description ?? '-' }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-sm text-gray-500 dark:text-gray-400">狀態</dt>
                <dd>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $expenseCategory->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $expenseCategory->is_active ? '啟用' : '停用' }}
                    </span>
                </dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-sm text-gray-500 dark:text-gray-400">建立時間</dt>
                <dd class="text-sm text-gray-500 dark:text-gray-400">@date($expenseCategory->created_at)</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-sm text-gray-500 dark:text-gray-400">最後更新</dt>
                <dd class="text-sm text-gray-500 dark:text-gray-400">@date($expenseCategory->updated_at)</dd>
            </div>
        </dl>
    </div>

    <!-- 子分類 -->
    @if($expenseCategory->children->count() > 0)
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            子分類 <span class="text-sm font-normal text-gray-500">({{ $expenseCategory->children->count() }} 項)</span>
        </h2>
        <ul class="space-y-2">
            @foreach($expenseCategory->children as $child)
            <li class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                <div>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $child->name }}</span>
                    <span class="ml-2 text-xs text-gray-500">{{ $child->code }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 text-xs rounded-full {{ $child->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                        {{ $child->is_active ? '啟用' : '停用' }}
                    </span>
                    <a href="{{ route('tenant.expense-categories.show', $child) }}" class="text-xs text-primary hover:underline">詳細</a>
                </div>
            </li>
            @endforeach
        </ul>
    </div>
    @endif
</div>
@endsection
