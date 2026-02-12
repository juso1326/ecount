@extends('layouts.tenant')

@section('title', '儀表板')

@section('page-title', '儀表板')

@section('content')
<!-- Page Header -->
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">儀表板</h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">歡迎回來，{{ auth()->user()->name }}</p>
    </div>
    <div class="flex items-center space-x-3">
        <!-- 年度選擇器 -->
        <form method="GET" action="{{ route('tenant.dashboard') }}" class="flex items-center space-x-2">
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
        <div class="text-sm text-gray-500 dark:text-gray-400">
            {{ now()->format('Y-m-d H:i') }}
        </div>
    </div>
</div>

<!-- System Announcement -->
@if($announcement)
<div class="mb-6 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 shadow-sm" x-data="{ editing: false }">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
            <svg class="w-6 h-6 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
            </svg>
            系統公告
        </h2>
        @if(auth()->user()->hasAnyRole(['admin', 'manager']))
        <button @click="editing = !editing" 
                class="text-sm text-primary hover:text-primary-dark font-medium" 
                x-text="editing ? '取消' : '編輯'">
            編輯
        </button>
        @endif
    </div>

    <!-- Display Mode -->
    <div x-show="!editing" class="prose dark:prose-invert max-w-none">
        <div class="text-gray-700 dark:text-gray-300">{!! nl2br(e($announcement->content)) !!}</div>
        @if($announcement->updated_at)
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-4">
            最後更新：{{ $announcement->updated_at->format('Y-m-d H:i') }}
            @if($announcement->updater)
                by {{ $announcement->updater->name }}
            @endif
        </p>
        @endif
    </div>

    <!-- Edit Mode -->
    @if(auth()->user()->hasAnyRole(['admin', 'manager']))
    <form x-show="editing" method="POST" action="{{ route('tenant.dashboard.announcement') }}" x-cloak>
        @csrf
        <textarea 
            name="content" 
            rows="6" 
            class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent"
            placeholder="輸入系統公告內容...">{{ $announcement->content }}</textarea>
        <div class="mt-4 flex justify-end space-x-3">
            <button type="button" @click="editing = false" 
                    class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                取消
            </button>
            <button type="submit" 
                    class="px-4 py-2 rounded-lg bg-primary text-white hover:bg-primary-dark">
                儲存
            </button>
        </div>
    </form>
    @endif
</div>
@endif

<!-- 財務快速概覽 -->
<div class="mb-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">財務概覽（{{ $fiscalYear }} 年度）</h2>
        <a href="{{ route('tenant.reports.financial', ['fiscal_year' => $fiscalYear]) }}" 
           class="text-sm text-primary hover:text-primary-dark font-medium">
            查看完整報表 →
        </a>
    </div>
    
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <!-- 應收總額 -->
        <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">應收總額</h3>
                <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="text-2xl font-bold text-gray-900 dark:text-white">
                ${{ number_format($financialStats['total_receivable'], 0) }}
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                已收：${{ number_format($financialStats['total_received'], 0) }}
            </p>
            @if($financialStats['unpaid_receivables'] > 0)
            <a href="{{ route('tenant.reports.financial.unpaid-receivables', ['fiscal_year' => $fiscalYear]) }}" 
               class="text-xs text-yellow-600 dark:text-yellow-400 hover:underline mt-1 block">
                未收：${{ number_format($financialStats['unpaid_receivables'], 0) }}（{{ $financialStats['unpaid_count'] }} 筆）
            </a>
            @endif
        </div>

        <!-- 應付總額 -->
        <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">應付總額</h3>
                <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <div class="text-2xl font-bold text-gray-900 dark:text-white">
                ${{ number_format($financialStats['total_payable'], 0) }}
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                已付：${{ number_format($financialStats['total_paid'], 0) }}
            </p>
            @if($financialStats['unpaid_payables'] > 0)
            <p class="text-xs text-orange-600 dark:text-orange-400 mt-1">
                未付：${{ number_format($financialStats['unpaid_payables'], 0) }}
            </p>
            @endif
        </div>

        <!-- 淨收入 -->
        <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">淨收入</h3>
                <svg class="w-8 h-8 {{ $financialStats['net_income'] >= 0 ? 'text-blue-500' : 'text-red-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
            </div>
            <div class="text-2xl font-bold {{ $financialStats['net_income'] >= 0 ? 'text-blue-600 dark:text-blue-400' : 'text-red-600 dark:text-red-400' }}">
                ${{ number_format($financialStats['net_income'], 0) }}
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                毛利率：{{ number_format($financialStats['profit_margin'], 1) }}%
            </p>
            <a href="{{ route('tenant.reports.financial.project-analysis', ['fiscal_year' => $fiscalYear]) }}" 
               class="text-xs text-blue-600 dark:text-blue-400 hover:underline mt-1 block">
                查看專案分析 →
            </a>
        </div>

        <!-- 支出結構 -->
        <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">支出結構</h3>
                <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
            <div class="text-2xl font-bold text-gray-900 dark:text-white">
                ${{ number_format($financialStats['total_paid'], 0) }}
            </div>
            <div class="mt-2 space-y-1 text-xs">
                <p class="text-blue-600 dark:text-blue-400">
                    員工：${{ number_format($financialStats['employee_salary'], 0) }}
                </p>
                <p class="text-purple-600 dark:text-purple-400">
                    外包：${{ number_format($financialStats['outsource_cost'], 0) }}
                </p>
            </div>
            <a href="{{ route('tenant.reports.financial.total-expenses', ['fiscal_year' => $fiscalYear]) }}" 
               class="text-xs text-purple-600 dark:text-purple-400 hover:underline mt-1 block">
                查看詳細支出 →
            </a>
        </div>
    </div>
</div>

<!-- 快速報表入口 -->
<div class="mb-6">
    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">常用報表</h2>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <!-- 應收未收 -->
        <a href="{{ route('tenant.reports.financial.unpaid-receivables', ['fiscal_year' => $fiscalYear]) }}" 
           class="group block rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4 shadow-sm hover:shadow-md transition-all hover:border-yellow-300 dark:hover:border-yellow-600">
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0 bg-yellow-100 dark:bg-yellow-900 rounded-lg p-2">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-yellow-600 dark:group-hover:text-yellow-400">
                        應收未收
                    </p>
                    @if($financialStats['unpaid_count'] > 0)
                    <p class="text-xs text-yellow-600 dark:text-yellow-400">
                        {{ $financialStats['unpaid_count'] }} 筆待追蹤
                    </p>
                    @else
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        目前無待收款
                    </p>
                    @endif
                </div>
                <svg class="w-5 h-5 text-gray-400 group-hover:text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </div>
        </a>

        <!-- 專案收支 -->
        <a href="{{ route('tenant.reports.financial.project-analysis', ['fiscal_year' => $fiscalYear]) }}" 
           class="group block rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4 shadow-sm hover:shadow-md transition-all hover:border-blue-300 dark:hover:border-blue-600">
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900 rounded-lg p-2">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400">
                        專案收支
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        成本與毛利分析
                    </p>
                </div>
                <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </div>
        </a>

        <!-- 外製成本 -->
        <a href="{{ route('tenant.reports.financial.outsource-cost-recovery', ['fiscal_year' => $fiscalYear]) }}" 
           class="group block rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4 shadow-sm hover:shadow-md transition-all hover:border-purple-300 dark:hover:border-purple-600">
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0 bg-purple-100 dark:bg-purple-900 rounded-lg p-2">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400">
                        外製成本
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        成本回收追蹤
                    </p>
                </div>
                <svg class="w-5 h-5 text-gray-400 group-hover:text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </div>
        </a>

        <!-- 總支出 -->
        <a href="{{ route('tenant.reports.financial.total-expenses', ['fiscal_year' => $fiscalYear]) }}" 
           class="group block rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4 shadow-sm hover:shadow-md transition-all hover:border-red-300 dark:hover:border-red-600">
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0 bg-red-100 dark:bg-red-900 rounded-lg p-2">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-red-600 dark:group-hover:text-red-400">
                        總支出
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        員工與外包統計
                    </p>
                </div>
                <svg class="w-5 h-5 text-gray-400 group-hover:text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </div>
        </a>
    </div>
</div>

<!-- Reports Section -->
<div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3 mb-6">
    <!-- 專案統計 -->
    <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">專案統計</h3>
            <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
        </div>
        <div class="space-y-3">
            <div class="flex justify-between items-center">
                <span class="text-gray-600 dark:text-gray-400">總專案數</span>
                <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_projects'] }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-600 dark:text-gray-400">進行中</span>
                <span class="text-lg font-semibold text-blue-600 dark:text-blue-400">{{ $activeProjects }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-600 dark:text-gray-400">已完成</span>
                <span class="text-lg font-semibold text-green-600 dark:text-green-400">{{ $stats['completed_projects'] }}</span>
            </div>
        </div>
    </div>

    <!-- 專案狀態分布圖 -->
    <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 shadow-sm">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">專案狀態分布</h3>
        <canvas id="projectStatusChart" class="max-h-48"></canvas>
    </div>

    <!-- 人員統計 -->
    <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">團隊成員</h3>
            <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
        </div>
        <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['total_users'] }}</div>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $stats['total_companies'] }} 個客戶廠商</p>
    </div>
</div>

<!-- 專案收益 TOP 5 -->
@if($projectProfitStats->count() > 0)
<div class="mb-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">專案收益 TOP 5（{{ $fiscalYear }} 年度）</h2>
    </div>
    
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">排名</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">專案名稱</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">已收金額</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">已付成本</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">淨利潤</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($projectProfitStats as $index => $data)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center justify-center w-8 h-8 rounded-full {{ $index == 0 ? 'bg-yellow-100 text-yellow-800' : ($index == 1 ? 'bg-gray-100 text-gray-800' : ($index == 2 ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800')) }} font-bold text-sm">
                            {{ $index + 1 }}
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $data['project']->name }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $data['project']->code }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-green-600 dark:text-green-400">
                        ${{ number_format($data['total_received'], 0) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-red-600 dark:text-red-400">
                        ${{ number_format($data['total_paid'], 0) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold {{ $data['profit'] >= 0 ? 'text-blue-600 dark:text-blue-400' : 'text-red-600 dark:text-red-400' }}">
                        ${{ number_format($data['profit'], 0) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<!-- Recent Projects -->
@if($recentProjects->count() > 0)
<div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">最近專案</h2>
    </div>
    <div class="p-6">
        <div class="space-y-4">
            @foreach($recentProjects as $project)
            <div class="flex items-center justify-between p-4 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-750">
                <div class="flex-1">
                    <h3 class="font-medium text-gray-900 dark:text-white">{{ $project->name }}</h3>
                    <div class="flex items-center gap-4 mt-1 text-sm text-gray-500 dark:text-gray-400">
                        <span>{{ $project->code }}</span>
                        @if($project->company)
                        <span>{{ $project->company->name }}</span>
                        @endif
                        <span>{{ $project->start_date?->format('Y-m-d') }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    @if($project->status === 'active')
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                        進行中
                    </span>
                    @elseif($project->status === 'completed')
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                        已完成
                    </span>
                    @else
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                        {{ $project->status }}
                    </span>
                    @endif
                    <a href="{{ route('tenant.projects.show', $project) }}" class="text-primary hover:text-primary-dark">
                        查看
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="mt-4 text-center">
            <a href="{{ route('tenant.projects.index') }}" class="text-primary hover:text-primary-dark font-medium">
                查看所有專案 →
            </a>
        </div>
    </div>
</div>
@endif

@endsection
