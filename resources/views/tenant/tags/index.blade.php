@extends('layouts.tenant')

@section('title', '標籤管理')

@section('page-title', '標籤管理')

@section('content')
<div class="mb-3 flex justify-end items-center">
    <a href="{{ route('tenant.tags.create', ['type' => $type ?? 'project']) }}" 
       class="bg-primary hover:bg-primary-dark text-white font-medium py-2 px-4 rounded-lg shadow-sm">
        + 新增標籤
    </a>
</div>

<!-- 標籤類型切換 -->
<div class="mb-6">
    <div class="flex space-x-2 border-b border-gray-200 dark:border-gray-700">
        @foreach($types as $key => $label)
            <a href="{{ route('tenant.tags.index', ['type' => $key]) }}" 
               class="px-4 py-2 font-medium {{ ($type ?? 'project') === $key ? 'text-primary border-b-2 border-primary' : 'text-gray-600 dark:text-gray-400 hover:text-primary' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>
</div>

<!-- 成功訊息 -->
@if(session('success'))
    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
@endif

@if(session('error'))
    <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
@endif

<!-- 資料表格 -->
<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">排序</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">標籤名稱</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">顏色</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">說明</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">狀態</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">操作</th>
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($tags as $tag)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                        {{ $tag->sort_order }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium" 
                              style="background-color: {{ $tag->color }}20; color: {{ $tag->color }};">
                            {{ $tag->name }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                        <div class="flex items-center">
                            <div class="w-6 h-6 rounded" style="background-color: {{ $tag->color }};"></div>
                            <span class="ml-2">{{ $tag->color }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                        {{ $tag->description ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $tag->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $tag->is_active ? '啟用' : '停用' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <a href="{{ route('tenant.tags.edit', $tag) }}" 
                           class="text-primary hover:text-primary-dark mr-3">
                            編輯
                        </a>
                        <form action="{{ route('tenant.tags.destroy', $tag) }}" 
                              method="POST" 
                              class="inline"
                              onsubmit="return confirm('確定要刪除此標籤嗎？');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">
                                刪除
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                        目前沒有標籤資料
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    <p class="text-sm text-gray-600 dark:text-gray-400">
        💡 提示：標籤可用於分類專案、客戶和團隊成員，方便快速篩選和管理。
    </p>
</div>
@endsection
