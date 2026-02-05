@extends('layouts.tenant')

@section('title', '支出項目管理')

@section('page-title', '支出項目管理')

@section('content')
<!-- 麵包屑 -->
<div class="mb-4">
    <p class="text-sm text-gray-600 dark:text-gray-400">財務設定 &gt; 支出項目管理</p>
</div>

<!-- 頁面標題與按鈕 -->
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">支出項目管理</h1>
    <a href="{{ route('tenant.expense-categories.create') }}" 
       class="bg-primary hover:bg-primary-dark text-white font-medium py-2 px-4 rounded-lg shadow-sm">
        + 新增支出項目
    </a>
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
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">代碼</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">名稱</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">說明</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">排序</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">狀態</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">操作</th>
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($categories as $category)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                        {{ $category->code }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                        {{ $category->name }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                        {{ $category->description ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900 dark:text-white">
                        {{ $category->sort_order }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $category->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $category->is_active ? '啟用' : '停用' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <a href="{{ route('tenant.expense-categories.edit', $category) }}" 
                           class="text-primary hover:text-primary-dark mr-3">
                            編輯
                        </a>
                        <form action="{{ route('tenant.expense-categories.destroy', $category) }}" 
                              method="POST" 
                              class="inline"
                              onsubmit="return confirm('確定要刪除此支出項目嗎？');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">
                                刪除
                            </button>
                        </form>
                    </td>
                </tr>
                
                <!-- 顯示子分類 -->
                @if($category->children->count() > 0)
                    @foreach($category->children as $child)
                        <tr class="bg-gray-50 dark:bg-gray-750">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                <span class="ml-6">└ {{ $child->code }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $child->name }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $child->description ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900 dark:text-white">
                                {{ $child->sort_order }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $child->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $child->is_active ? '啟用' : '停用' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <a href="{{ route('tenant.expense-categories.edit', $child) }}" 
                                   class="text-primary hover:text-primary-dark mr-3">
                                    編輯
                                </a>
                                <form action="{{ route('tenant.expense-categories.destroy', $child) }}" 
                                      method="POST" 
                                      class="inline"
                                      onsubmit="return confirm('確定要刪除此支出項目嗎？');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        刪除
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                @endif
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                        目前沒有支出項目資料
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    <p class="text-sm text-gray-600 dark:text-gray-400">
        💡 提示：支出項目用於分類管理各種支出，可設定層級結構方便細分管理。
    </p>
</div>
@endsection
