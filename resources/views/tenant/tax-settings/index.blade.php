@extends('layouts.tenant')

@section('title', '稅款設定')

@section('content')
<div class="mb-2 flex justify-end items-center">
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

<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th class="px-6 py-1 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">名稱</th>
                <th class="px-6 py-1 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">稅率</th>
                <th class="px-6 py-1 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">說明</th>
                <th class="px-6 py-1 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">預設</th>
                <th class="px-6 py-1 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">狀態</th>
                <th class="px-6 py-1 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">操作</th>
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($taxSettings as $tax)
                <tr>
                    <td class="px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                        {{ $tax->name }}
                    </td>
                    <td class="px-6 py-2 whitespace-nowrap text-center text-sm text-gray-900 dark:text-white">
                        <span class="font-semibold">{{ $tax->rate }}%</span>
                    </td>
                    <td class="px-6 py-2 text-sm text-gray-500 dark:text-gray-400">
                        {{ $tax->description ?? '-' }}
                    </td>
                    <td class="px-6 py-2 whitespace-nowrap text-center">
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
                    <td class="px-6 py-2 whitespace-nowrap text-center">
                        <span class="px-2 inline-flex text-xs font-semibold rounded-full {{ $tax->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $tax->is_active ? '啟用' : '停用' }}
                        </span>
                    </td>
                    <td class="px-6 py-2 whitespace-nowrap text-center text-sm font-medium">
                        <a href="{{ route('tenant.tax-settings.edit', $tax) }}" class="text-primary hover:text-primary-dark mr-3">編輯</a>
                        <form action="{{ route('tenant.tax-settings.destroy', $tax) }}" method="POST" class="inline" 
                              onsubmit="return confirm('確定要刪除此稅率設定嗎？');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">刪除</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-2 text-center text-sm text-gray-500 dark:text-gray-400">
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
