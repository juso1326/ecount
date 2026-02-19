@extends('layouts.tenant')

@section('title', '薪資管理')

@section('page-title', '薪資管理')

@section('content')
<!-- 頁面標題 -->
<div class="mb-2 flex justify-between items-center">
    <div>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $period['label'] }}</p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('tenant.salaries.vendors', ['year' => $year, 'month' => $month]) }}" 
           class="flex items-center gap-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <span>廠商支付記錄</span>
        </a>
    </div>
</div>

<!-- 月份選擇與導航 -->
<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-4 mb-2">
    <div class="flex items-center justify-between">
        <!-- 上個月按鈕 -->
        <a href="{{ route('tenant.salaries.index', ['year' => $year, 'month' => $month, 'nav' => 'prev']) }}" 
           class="flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-primary transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            <span class="hidden sm:inline">上個月</span>
        </a>
        
        <!-- 月份選擇表單 -->
        <form method="GET" action="{{ route('tenant.salaries.index') }}" class="flex items-center gap-3">
            <select name="year" onchange="this.form.submit()" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                @for($y = $endYear; $y >= $startYear; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}年</option>
                @endfor
            </select>
            <select name="month" onchange="this.form.submit()" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}" {{ $month == str_pad($m, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>{{ $m }}月</option>
                @endfor
            </select>
        </form>
        
        <!-- 下個月按鈕 -->
        <a href="{{ route('tenant.salaries.index', ['year' => $year, 'month' => $month, 'nav' => 'next']) }}" 
           class="flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-primary transition">
            <span class="hidden sm:inline">下個月</span>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>
</div>

<!-- 統計卡片 -->
@if($salaries && count($salaries) > 0)
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-4 text-white shadow-lg">
        <div class="text-sm opacity-90">員工人數</div>
        <div class="text-3xl font-bold mt-1">{{ count($salaries) }}</div>
        <div class="text-xs opacity-75 mt-1">人</div>
    </div>
    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-4 text-white shadow-lg">
        <div class="text-sm opacity-90">基本薪資</div>
        <div class="text-3xl font-bold mt-1">${{ number_format(collect($salaries)->sum('base_salary'), 0) }}</div>
        <div class="text-xs opacity-75 mt-1">元</div>
    </div>
    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg p-4 text-white shadow-lg">
        <div class="text-sm opacity-90">加扣項</div>
        <div class="text-3xl font-bold mt-1">${{ number_format(collect($salaries)->sum('additions') - collect($salaries)->sum('deductions'), 0) }}</div>
        <div class="text-xs opacity-75 mt-1">
            <span class="text-green-200">+${{ number_format(collect($salaries)->sum('additions'), 0) }}</span> / 
            <span class="text-red-200">-${{ number_format(collect($salaries)->sum('deductions'), 0) }}</span>
        </div>
    </div>
    <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg p-4 text-white shadow-lg">
        <div class="text-sm opacity-90">薪資總額</div>
        <div class="text-3xl font-bold mt-1">${{ number_format($total, 0) }}</div>
        <div class="text-xs opacity-75 mt-1">元</div>
    </div>
</div>
@endif

<!-- 薪資列表 -->
<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">員工</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">基本薪資</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">加項</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">扣項</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">實領薪資</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">狀態</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">操作</th>
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($salaries as $salary)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-bold">
                            {{ mb_substr($salary['user']->name, 0, 1) }}
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $salary['user']->name }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">帳號開啟: {{ $salary['user']->created_at->format('Y-m-d') }}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right">
                    <div class="text-sm font-semibold text-gray-900 dark:text-white">${{ number_format($salary['base_salary'], 0) }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right">
                    @if($salary['additions'] > 0)
                        <div class="text-sm font-semibold text-green-600">+${{ number_format($salary['additions'], 0) }}</div>
                    @else
                        <div class="text-sm text-gray-400">-</div>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right">
                    @if($salary['deductions'] > 0)
                        <div class="text-sm font-semibold text-red-600">-${{ number_format($salary['deductions'], 0) }}</div>
                    @else
                        <div class="text-sm text-gray-400">-</div>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right">
                    <div class="text-base font-bold text-blue-600">${{ number_format($salary['total'], 0) }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                    @if($salary['items']->where('is_salary_paid', true)->count() > 0)
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            已撥款
                        </span>
                    @else
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                            </svg>
                            未撥款
                        </span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                    <a href="{{ route('tenant.salaries.show', ['user' => $salary['user'], 'year' => $year, 'month' => $month]) }}" 
                       class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                        查看明細
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-16 text-center">
                    <div class="flex flex-col items-center justify-center">
                        <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">本月無薪資記錄</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">請先建立員工薪資資料</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
        @if($salaries && count($salaries) > 0)
        <tfoot class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800">
            <tr>
                <td class="px-6 py-4 text-sm font-bold text-gray-900 dark:text-white">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                        </svg>
                        總計 ({{ count($salaries) }} 人)
                    </div>
                </td>
                <td class="px-6 py-4 text-right text-sm font-bold text-gray-900 dark:text-white">
                    ${{ number_format(collect($salaries)->sum('base_salary'), 0) }}
                </td>
                <td class="px-6 py-4 text-right text-sm font-bold text-green-600">
                    @if(collect($salaries)->sum('additions') > 0)
                        +${{ number_format(collect($salaries)->sum('additions'), 0) }}
                    @else
                        -
                    @endif
                </td>
                <td class="px-6 py-4 text-right text-sm font-bold text-red-600">
                    @if(collect($salaries)->sum('deductions') > 0)
                        -${{ number_format(collect($salaries)->sum('deductions'), 0) }}
                    @else
                        -
                    @endif
                </td>
                <td class="px-6 py-4 text-right">
                    <div class="text-lg font-bold text-blue-600">${{ number_format($total, 0) }}</div>
                </td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>
@endsection
