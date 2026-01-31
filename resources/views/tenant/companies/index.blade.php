@extends('layouts.tenant')

@section('title', '客戶/廠商管理')

@section('page-title', '客戶/廠商管理')

@section('content')
<!-- 麵包屑 -->
<div class="mb-4">
    <p class="text-sm text-gray-600 dark:text-gray-400">專案帳戶管理 &gt; 客戶/廠商管理</p>
</div>

<!-- 頁面標題與按鈕 -->
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">客戶/廠商管理</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
            第 {{ $companies->currentPage() }} / {{ $companies->lastPage() }} 頁，每頁15筆，共{{ $companies->total() }}筆
        </p>
    </div>
    <a href="{{ route('tenant.companies.create') }}" 
       class="bg-primary hover:bg-primary-dark text-white font-medium py-2 px-4 rounded-lg shadow-sm">
        + 新增客戶/廠商
    </a>
</div>

<!-- 搜尋與篩選 -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
    <form method="GET" action="{{ route('tenant.companies.index') }}" class="flex gap-4 items-end">
        <!-- 類型篩選 -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">類型</label>
            <div class="flex gap-4">
                <label class="flex items-center">
                    <input type="checkbox" name="is_client" value="1" {{ request('is_client') ? 'checked' : '' }}
                           class="rounded border-gray-300 dark:border-gray-600 text-primary focus:ring-primary">
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">客戶</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="is_outsource" value="1" {{ request('is_outsource') ? 'checked' : '' }}
                           class="rounded border-gray-300 dark:border-gray-600 text-primary focus:ring-primary">
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">外製</span>
                </label>
            </div>
        </div>

        <!-- 搜尋框 -->
        <div class="flex-1">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="搜尋名稱、簡稱、統編..." 
                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
        </div>
        
        <button type="submit" 
                class="bg-primary hover:bg-primary-dark text-white font-medium py-2 px-6 rounded-lg">
            搜尋
        </button>
        
        @if(request()->hasAny(['search', 'is_client', 'is_outsource']))
            <a href="{{ route('tenant.companies.index') }}" 
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
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">編輯</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">類型</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">客戶</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">外製</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">名稱</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">簡稱</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">統編</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">電話</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">地址</th>
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($companies as $company)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <a href="{{ route('tenant.companies.edit', $company) }}" 
                       class="text-primary hover:text-primary-dark font-medium">
                        編輯
                    </a>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                    {{ $company->type === 'company' ? '公司' : '個人' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                    {{ $company->is_client ? '是' : '' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                    {{ $company->is_outsource ? '是' : '' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                    {{ $company->name }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                    {{ $company->short_name }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                    {{ $company->tax_id }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                    {{ $company->phone }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                    {{ $company->address }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                    沒有找到任何客戶/廠商資料
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- 分頁 -->
<div class="mt-6">
    {{ $companies->appends(request()->except('page'))->links() }}
</div>
@endsection
