@extends('layouts.tenant')

@section('title', '稅款設定')

@section('content')
<!-- 分頁資訊與操作按鈕 -->
<div class="mb-2 flex justify-between items-center">
    <div class="text-sm text-gray-600 dark:text-gray-400">
        @if($taxSettings->total() > 0)
            顯示第 <span class="font-medium">{{ $taxSettings->firstItem() }}</span> 
            到 <span class="font-medium">{{ $taxSettings->lastItem() }}</span> 筆，
            共 <span class="font-medium">{{ number_format($taxSettings->total()) }}</span> 筆
        @else
            <span>無資料</span>
        @endif
    </div>
    <a href="{{ route('tenant.tax-settings.create') }}" 
       class="bg-primary hover:bg-primary-dark text-white font-medium py-2 px-4 rounded-lg shadow-sm">
        + 新增稅率
    </a>
</div>

@if(session('success'))
    <div class="mb-2 bg-green-100 border border-green-400 text-green-700 px-4 py-1 rounded">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-2 bg-red-100 border border-red-400 text-red-700 px-4 py-1 rounded">
        {{ session('error') }}
    </div>
@endif

<!-- 智能搜尋 -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-2">
    <form method="GET" class="space-y-4">
        <div class="flex gap-2">
            <div class="flex-1">
                <input type="text" name="search" 
                       value="{{ request('search') }}"
                       placeholder="🔍 智能搜尋..." 
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary focus:border-transparent text-base">
            </div>
            <button class="bg-primary hover:bg-primary-dark text-white font-medium py-2 px-6 rounded-lg">搜尋</button>
            @if(request('search'))
                <a href="{{ route('tenant.tax-settings.index') }}" class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-medium py-2 px-6 rounded-lg">清除</a>
            @endif
        </div>
    </form>
</div>

<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">操作</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">名稱</th>
                <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">稅率</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">說明</th>
                <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">預設</th>
                <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">狀態</th>
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($taxSettings as $tax)
                @if(!request('search') || 
                    stripos($tax->name, request('search')) !== false || 
                    stripos($tax->description, request('search')) !== false)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    <td class="px-4 py-2 whitespace-nowrap text-center text-xs font-medium space-x-2">
                        <a href="{{ route('tenant.tax-settings.edit', $tax) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">編輯</a>
                        <form action="{{ route('tenant.tax-settings.destroy', $tax) }}" method="POST" class="inline" 
                              onsubmit="return confirm('確定要刪除此稅率設定嗎？');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">刪除</button>
                        </form>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                        {{ $tax->name }}
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-center text-sm text-gray-900 dark:text-white">
                        <span class="font-semibold">{{ $tax->rate }}%</span>
                    </td>
                    <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">
                        {{ $tax->description ?? '-' }}
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-center">
                        @if($tax->is_default)
                            <span class="px-2 inline-flex text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                預設
                            </span>
                        @else
                            <form action="{{ route('tenant.tax-settings.set-default', $tax) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-xs text-primary hover:underline">設為預設</button>
                            </form>
                        @endif
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-center">
                        <span class="px-2 inline-flex text-xs font-semibold rounded-full {{ $tax->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $tax->is_active ? '啟用' : '停用' }}
                        </span>
                    </td>
                </tr>
                @endif
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-2 text-center text-sm text-gray-500 dark:text-gray-400">
                        目前沒有稅率設定
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    <p class="text-sm text-gray-600 dark:text-gray-400">
        💡 提示：設定常用的稅率（如營業稅 5%），可在新增交易時快速選用。
    </p>
</div>
@endsection
