@extends('layouts.tenant')

@section('title', '薪資管理')

@section('page-title', '薪資管理')

@section('content')
<!-- 麵包屑 -->
<div class="mb-4">
    <p class="text-sm text-gray-600 dark:text-gray-400">財務管理 &gt; 薪資管理</p>
</div>

<!-- 頁面標題 -->
<div class="mb-2 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">薪資總表</h1>
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
                @for($y = date('Y'); $y >= 2014; $y--)
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

<!-- 薪資列表 -->
<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th class="px-6 py-1 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">員工</th>
                <th class="px-6 py-1 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">基本薪資</th>
                <th class="px-6 py-1 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">加項</th>
                <th class="px-6 py-1 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">扣項</th>
                <th class="px-6 py-1 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">總計</th>
                <th class="px-6 py-1 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">狀態</th>
                <th class="px-6 py-1 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">操作</th>
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($salaries as $salary)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                <td class="px-6 py-2 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $salary['user']->name }}</div>
                </td>
                <td class="px-6 py-2 whitespace-nowrap text-right text-sm text-gray-900 dark:text-white">
                    ${{ number_format($salary['base_salary'], 0) }}
                </td>
                <td class="px-6 py-2 whitespace-nowrap text-right text-sm text-green-600">
                    @if($salary['additions'] > 0)
                        +${{ number_format($salary['additions'], 0) }}
                    @else
                        -
                    @endif
                </td>
                <td class="px-6 py-2 whitespace-nowrap text-right text-sm text-red-600">
                    @if($salary['deductions'] > 0)
                        -${{ number_format($salary['deductions'], 0) }}
                    @else
                        -
                    @endif
                </td>
                <td class="px-6 py-2 whitespace-nowrap text-right text-sm font-bold text-gray-900 dark:text-white">
                    ${{ number_format($salary['total'], 0) }}
                </td>
                <td class="px-6 py-2 whitespace-nowrap text-center">
                    @if($salary['items']->where('is_salary_paid', true)->count() > 0)
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">已撥款</span>
                    @else
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">未撥款</span>
                    @endif
                </td>
                <td class="px-6 py-2 whitespace-nowrap text-center text-sm">
                    <a href="{{ route('tenant.salaries.show', ['user' => $salary['user'], 'year' => $year, 'month' => $month]) }}" 
                       class="text-primary hover:text-primary-dark">
                        查看明細
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                    本月無薪資記錄
                </td>
            </tr>
            @endforelse
        </tbody>
        @if($salaries)
        <tfoot class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <td class="px-6 py-1 text-sm font-bold text-gray-900 dark:text-white">總計</td>
                <td colspan="3"></td>
                <td class="px-6 py-1 text-right text-sm font-bold text-gray-900 dark:text-white">
                    ${{ number_format($total, 0) }}
                </td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>
@endsection
