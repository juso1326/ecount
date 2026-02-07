@extends('layouts.tenant')

@section('title', '財務報表')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-semibold text-gray-900 dark:text-white">財務報表</h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">查看應收應付、收支統計與財務分析</p>
    </div>
    
    <!-- 會計年度選擇器 -->
    <form method="GET" action="{{ route('tenant.reports.financial') }}" class="flex items-center space-x-3">
        <label for="fiscal_year" class="text-sm font-medium text-gray-700 dark:text-gray-300">會計年度：</label>
        <select name="fiscal_year" id="fiscal_year" 
                onchange="this.form.submit()"
                class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
            @foreach($availableYears as $year)
                <option value="{{ $year }}" {{ $year == $fiscalYear ? 'selected' : '' }}>
                    {{ $year }} 年度
                </option>
            @endforeach
        </select>
    </form>
</div>

<!-- 快速統計 -->
<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
    <!-- 應收總額 -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                            應收總額
                        </dt>
                        <dd class="text-lg font-semibold text-gray-900 dark:text-white">
                            ${{ number_format($summary['total_receivable'], 2) }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- 已收金額 -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                            已收金額
                        </dt>
                        <dd class="text-lg font-semibold text-gray-900 dark:text-white">
                            ${{ number_format($summary['total_received'], 2) }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- 應付總額 -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                            應付總額
                        </dt>
                        <dd class="text-lg font-semibold text-gray-900 dark:text-white">
                            ${{ number_format($summary['total_payable'], 2) }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- 淨收入 -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                            淨收入
                        </dt>
                        <dd class="text-lg font-semibold {{ $summary['net_income'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            ${{ number_format($summary['net_income'], 2) }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 年度統計說明卡片 -->
<div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
    <div class="flex items-start space-x-3">
        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <div>
            <h4 class="text-sm font-semibold text-blue-900 dark:text-blue-300 mb-1">會計年度統計</h4>
            <p class="text-sm text-blue-800 dark:text-blue-400">
                目前顯示 <strong>{{ $fiscalYear }} 年度</strong>的財務數據。所有統計均依據帳務年度欄位進行計算，確保跨年度帳款正確歸類。
            </p>
        </div>
    </div>
</div>

<!-- 報表功能說明 -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">📊 報表功能說明</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600 dark:text-gray-400">
        <div class="flex items-start space-x-2">
            <span class="text-green-600 dark:text-green-400">✓</span>
            <span>依會計年度統計應收應付金額</span>
        </div>
        <div class="flex items-start space-x-2">
            <span class="text-green-600 dark:text-green-400">✓</span>
            <span>即時計算已收款與未收款</span>
        </div>
        <div class="flex items-start space-x-2">
            <span class="text-green-600 dark:text-green-400">✓</span>
            <span>淨收入損益分析</span>
        </div>
        <div class="flex items-start space-x-2">
            <span class="text-green-600 dark:text-green-400">✓</span>
            <span>支援多年度資料檢視</span>
        </div>
    </div>
</div>

<!-- 報表功能卡片 -->
<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-2">
    <!-- 總支出報表 -->
    <a href="{{ route('tenant.reports.financial.total-expenses', ['fiscal_year' => $fiscalYear]) }}" 
       class="block bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-md transition-shadow duration-200">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-red-100 dark:bg-red-900 rounded-md p-3">
                        <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">總支出報表</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">員工薪資、外包勞務與其他支出統計分析</p>
            <div class="mt-3 flex items-center text-xs text-gray-500 dark:text-gray-400">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                支援區分成員與外包
            </div>
        </div>
    </a>

    <!-- 佔位卡片 - 未來功能 -->
    <div class="block bg-gray-50 dark:bg-gray-900 rounded-lg shadow border-2 border-dashed border-gray-300 dark:border-gray-700">
        <div class="p-6 opacity-50">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-gray-200 dark:bg-gray-800 rounded-md p-3">
                        <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                        </svg>
                    </div>
                </div>
            </div>
            <h3 class="text-lg font-semibold text-gray-500 dark:text-gray-600 mb-2">更多報表</h3>
            <p class="text-sm text-gray-400 dark:text-gray-600">即將推出...</p>
        </div>
    </div>
</div>
@endsection
