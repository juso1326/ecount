@extends('layouts.tenant')

@section('title', '稅率詳細')

@section('content')
<!-- 麵包屑 -->
<div class="mb-4">
    <p class="text-sm text-gray-600 dark:text-gray-400">
        <a href="{{ route('tenant.tax-settings.index') }}" class="hover:text-primary">稅率設定</a>
        <span class="mx-2">/</span>
        {{ $taxSetting->name }}
    </p>
</div>

<!-- 操作按鈕 -->
<div class="mb-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $taxSetting->name }}</h1>
    <div class="flex gap-2">
        <a href="{{ route('tenant.tax-settings.edit', $taxSetting) }}"
           class="px-4 py-2 text-sm font-medium text-white bg-primary hover:bg-primary-dark rounded-lg">
            編輯
        </a>
        <a href="{{ route('tenant.tax-settings.index') }}"
           class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600">
            返回列表
        </a>
    </div>
</div>

<div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 max-w-lg">
    <dl class="space-y-4">
        <div class="flex justify-between">
            <dt class="text-sm text-gray-500 dark:text-gray-400">名稱</dt>
            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $taxSetting->name }}</dd>
        </div>
        <div class="flex justify-between">
            <dt class="text-sm text-gray-500 dark:text-gray-400">稅率</dt>
            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $taxSetting->rate }}%</dd>
        </div>
        <div class="flex justify-between">
            <dt class="text-sm text-gray-500 dark:text-gray-400">狀態</dt>
            <dd>
                @if($taxSetting->is_active)
                    <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 rounded-full">啟用</span>
                @else
                    <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400 rounded-full">停用</span>
                @endif
            </dd>
        </div>
        <div class="flex justify-between">
            <dt class="text-sm text-gray-500 dark:text-gray-400">預設稅率</dt>
            <dd>
                @if($taxSetting->is_default)
                    <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 rounded-full">是</span>
                @else
                    <span class="text-sm text-gray-400">否</span>
                @endif
            </dd>
        </div>
        @if($taxSetting->description)
        <div>
            <dt class="text-sm text-gray-500 dark:text-gray-400 mb-1">說明</dt>
            <dd class="text-sm text-gray-900 dark:text-white">{{ $taxSetting->description }}</dd>
        </div>
        @endif
        <div class="flex justify-between pt-2 border-t border-gray-100 dark:border-gray-700">
            <dt class="text-sm text-gray-500 dark:text-gray-400">建立時間</dt>
            <dd class="text-sm text-gray-600 dark:text-gray-400">@date($taxSetting->created_at)</dd>
        </div>
        <div class="flex justify-between">
            <dt class="text-sm text-gray-500 dark:text-gray-400">更新時間</dt>
            <dd class="text-sm text-gray-600 dark:text-gray-400">@date($taxSetting->updated_at)</dd>
        </div>
    </dl>
</div>
@endsection
