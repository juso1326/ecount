@extends('layouts.tenant')

@section('title', '標籤管理')

@section('page-title', '標籤管理')

@section('content')
<!-- 分頁資訊與操作按鈕 -->
<div class="mb-2 flex justify-between items-center">
    <div class="text-sm text-gray-600 dark:text-gray-400">
        @if($tags->count() > 0)
            共 <span class="font-medium">{{ $tags->count() }}</span> 筆
        @else
            <span>無資料</span>
        @endif
    </div>
    <a href="{{ route('tenant.tags.create', ['type' => $type ?? 'project']) }}" 
       class="bg-primary hover:bg-primary-dark text-white font-medium py-2 px-4 rounded-lg shadow-sm">
        + 新增標籤
    </a>
</div>

<!-- 標籤類型切換 -->
<div class="mb-2">
    <div class="flex space-x-2 border-b border-gray-200 dark:border-gray-700">
        @foreach($types as $key => $label)
            <a href="{{ route('tenant.tags.index', ['type' => $key]) }}" 
               class="px-4 py-2 font-medium {{ ($type ?? 'project') === $key ? 'text-primary border-b-2 border-primary' : 'text-gray-600 dark:text-gray-400 hover:text-primary' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>
</div>

<!-- 搜尋與篩選 -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-2">
    <form method="GET" action="{{ route('tenant.tags.index') }}" class="space-y-4">
        <input type="hidden" name="type" value="{{ $type ?? 'project' }}">
        <!-- 智能搜尋框 -->
        <div class="flex gap-2">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="🔍 智能搜尋：標籤名稱/說明..." 
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary focus:border-transparent text-base">
            </div>
            <button type="submit" 
                    class="bg-primary hover:bg-primary-dark text-white font-medium py-2 px-6 rounded-lg whitespace-nowrap">
                搜尋
            </button>
            @if(request('search'))
                <a href="{{ route('tenant.tags.index', ['type' => $type ?? 'project']) }}" 
                   class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-medium py-2 px-6 rounded-lg whitespace-nowrap">
                    清除
                </a>
            @endif
        </div>
    </form>
</div>

<!-- 資料表格 -->
<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th class="px-3 py-1 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">操作</th>
                <th class="px-6 py-1 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">排序</th>
                <th class="px-6 py-1 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">標籤名稱</th>
                <th class="px-6 py-1 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">顏色</th>
                <th class="px-6 py-1 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">說明</th>
                <th class="px-6 py-1 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">狀態</th>
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($tags as $tag)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-center space-x-2">
                        @if($tag->is_system)
                            <span class="text-gray-400 text-xs">🔒 系統</span>
                        @else
                        <a href="{{ route('tenant.tags.edit', $tag) }}"
                           class="text-primary hover:text-primary-dark font-medium">
                            編輯
                        </a>
                        @endif
                        @if($type === 'project_status')
                            <form action="{{ route('tenant.tags.set-default-status', $tag) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                        class="font-medium {{ $defaultStatusId == $tag->id ? 'text-green-600 dark:text-green-400 cursor-default' : 'text-gray-500 hover:text-green-600 dark:text-gray-400 dark:hover:text-green-400' }}"
                                        {{ $defaultStatusId == $tag->id ? 'disabled' : '' }}
                                        title="{{ $defaultStatusId == $tag->id ? '目前預設' : '設為預設' }}">
                                    {{ $defaultStatusId == $tag->id ? '★ 預設' : '☆ 設預設' }}
                                </button>
                            </form>
                        @endif
                        @if($tag->is_system)
                            <span class="text-gray-400 text-xs" title="系統內建，無法刪除">🔒</span>
                        @else
                        <form action="{{ route('tenant.tags.destroy', $tag) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 font-medium">
                                刪除
                            </button>
                        </form>
                        @endif
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                        <form action="{{ route('tenant.tags.sort', $tag) }}" method="POST" class="flex items-center gap-1">
                            @csrf
                            @method('PATCH')
                            <input type="number" name="sort_order" value="{{ $tag->sort_order }}" min="0"
                                   class="w-14 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded px-1 py-0.5 text-sm text-center">
                            <button type="submit" class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400">✓</button>
                        </form>
                    </td>
                    <td class="px-6 py-2 whitespace-nowrap">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium" 
                              style="background-color: {{ $tag->color }}20; color: {{ $tag->color }};">
                            {{ $tag->name }}
                        </span>
                    </td>
                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                        <div class="flex items-center">
                            <div class="w-6 h-6 rounded" style="background-color: {{ $tag->color }};"></div>
                            <span class="ml-2">{{ $tag->color }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-2 text-sm text-gray-500 dark:text-gray-400">
                        {{ $tag->description ?? '-' }}
                    </td>
                    <td class="px-6 py-2 whitespace-nowrap text-center">
                        <form action="{{ route('tenant.tags.toggle-active', $tag) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full cursor-pointer hover:opacity-80 {{ $tag->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}"
                                    title="{{ $tag->is_active ? '點擊停用' : '點擊啟用' }}">
                                {{ $tag->is_active ? '啟用' : '停用' }}
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-2 text-center text-sm text-gray-500 dark:text-gray-400">
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
