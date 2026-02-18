@extends('layouts.tenant')

@section('title', '客戶/廠商管理')

@section('page-title', '客戶/廠商管理')

@section('content')
<div class="mb-2 flex justify-end items-center">
    <a href="{{ route('tenant.companies.create') }}" 
       class="bg-primary hover:bg-primary-dark text-white font-medium py-2 px-4 rounded-lg shadow-sm">
        + 新增客戶/廠商
    </a>
</div>

<!-- 搜尋與篩選 -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-2">
    <form method="GET" action="{{ route('tenant.companies.index') }}" class="flex gap-4 items-end">
        <!-- 搜尋框 -->
        <div class="flex-1">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="🔍 聰明尋找：搜尋名稱、簡稱、統編..." 
                   class="w-full border-2 border-primary/30 dark:border-primary/50 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-base focus:ring-2 focus:ring-primary focus:border-primary">
        </div>

        <!-- 類型篩選 -->
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
                <th class="px-6 py-1 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">查看</th>
                <th class="px-6 py-1 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">類型</th>
                <th class="px-6 py-1 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">客戶</th>
                <th class="px-6 py-1 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">外製</th>
                <th class="px-6 py-1 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">名稱</th>
                <th class="px-6 py-1 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">簡稱</th>
                <th class="px-6 py-1 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">統編</th>
                <th class="px-6 py-1 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">電話</th>
                <th class="px-6 py-1 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">地址</th>
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($companies as $company)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                <td class="px-6 py-2 whitespace-nowrap text-sm">
                    <a href="{{ route('tenant.companies.show', $company) }}" 
                       class="text-primary hover:text-primary-dark font-medium">
                        查看
                    </a>
                    <a href="{{ route('tenant.companies.edit', $company) }}" 
                       class="ml-1 text-primary hover:text-primary-dark font-medium">
                        編輯
                    </a>
                </td>
                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                    {{ $company->type === 'company' ? '公司' : '個人' }}
                </td>
                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                    {{ $company->is_client ? '是' : '' }}
                </td>
                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                    {{ $company->is_outsource ? '是' : '' }}
                </td>
                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                    {{ $company->name }}
                </td>
                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                    {{ $company->short_name }}
                </td>
                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                    {{ $company->tax_id }}
                </td>
                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                    {{ $company->phone }}
                </td>
                <td class="px-6 py-2 text-sm text-gray-500 dark:text-gray-400">
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
