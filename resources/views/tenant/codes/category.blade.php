@extends('layouts.tenant')

@section('title', $categoryName . ' - 代碼管理')

@section('page-title', $categoryName)

@section('content')
<!-- 麵包屑 -->
<div class="mb-4">
    <p class="text-sm text-gray-600 dark:text-gray-400">專案帳戶管理 &gt; 代碼管理 &gt; {{ $categoryName }}</p>
</div>

<!-- 頁面標題與按鈕 -->
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $categoryName }}</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
            第 {{ $codes->currentPage() }} / {{ $codes->lastPage() }} 頁，每頁15筆，共{{ $codes->total() }}筆
        </p>
    </div>
    <a href="{{ route('tenant.codes.create', $category) }}" 
       class="bg-primary hover:bg-primary-dark text-white font-medium py-2 px-4 rounded-lg shadow-sm">
        + 新增代碼
    </a>
</div>

<!-- 搜尋與篩選 -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
    <form method="GET" class="flex gap-4 items-end">
        <!-- 搜尋框 -->
        <div class="flex-1">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="搜尋代碼或名稱..." 
                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
        </div>
        
        <!-- 狀態篩選 -->
        <div>
            <select name="status" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary">
                <option value="">全部狀態</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>啟用</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>停用</option>
            </select>
        </div>
        
        <button type="submit" 
                class="bg-primary hover:bg-primary-dark text-white font-medium py-2 px-6 rounded-lg">
            搜尋
        </button>
        
        @if(request()->hasAny(['search', 'status']))
            <a href="{{ route('tenant.codes.category', $category) }}" 
               class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-medium py-2 px-6 rounded-lg">
                清除
            </a>
        @endif
    </form>
</div>


<!-- 資料表格 -->
<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">代碼</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">名稱</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">排序</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">狀態</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">說明</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">操作</th>
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($codes as $code)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $code->code }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="text-sm text-gray-900 dark:text-white">{{ $code->name }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $code->sort_order }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($code->is_active)
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">啟用</span>
                    @else
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100">停用</span>
                    @endif
                </td>
                <td class="px-6 py-4">
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $code->description ?: '-' }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex space-x-3">
                        <a href="{{ route('tenant.codes.edit', [$category, $code]) }}" 
                           class="text-primary hover:text-primary-dark">
                            編輯
                        </a>
                        <form method="POST" action="{{ route('tenant.codes.destroy', [$category, $code]) }}" 
                              onsubmit="return confirm('確定要刪除此代碼嗎？');"
                              class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                刪除
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-12 text-center">
                    <p class="text-gray-500 dark:text-gray-400">目前沒有代碼資料</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($codes->hasPages())
        <div class="px-4 py-6 md:px-6 xl:px-7.5">
            {{ $codes->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
