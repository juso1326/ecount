@extends('layouts.tenant')

@section('title', '應收未收報表')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-semibold text-gray-900 dark:text-white">應收未收報表</h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">已開發票請款但尚未收到款項的應收帳款</p>
        </div>
        
        <!-- 篩選器 -->
        <form method="GET" action="{{ route('tenant.reports.financial.unpaid-receivables') }}" class="flex items-center space-x-3">
            <div class="flex items-center space-x-2">
                <label for="fiscal_year" class="text-sm font-medium text-gray-700 dark:text-gray-300">會計年度：</label>
                <select name="fiscal_year" id="fiscal_year" 
                        class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}" {{ $year == $fiscalYear ? 'selected' : '' }}>
                            {{ $year }} 年度
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="flex items-center space-x-2">
                <label for="company_id" class="text-sm font-medium text-gray-700 dark:text-gray-300">客戶：</label>
                <select name="company_id" id="company_id" 
                        class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    <option value="">全部客戶</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ $company->id == $companyId ? 'selected' : '' }}>
                            {{ $company->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition">
                查詢
            </button>
        </form>
    </div>
</div>

<!-- 總覽統計卡片 -->
<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-6">
    <!-- 總筆數 -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg border-l-4 border-blue-500">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                            未收筆數
                        </dt>
                        <dd class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $summary['total_count'] }}
                        </dd>
                        <dd class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            已開發票
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- 未收總額 -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg border-l-4 border-yellow-500">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                            未收總額
                        </dt>
                        <dd class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
                            ${{ number_format($summary['total_unpaid'], 2) }}
                        </dd>
                        <dd class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            應收：${{ number_format($summary['total_amount'], 2) }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- 逾期未收 -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg border-l-4 border-red-500">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                            逾期未收
                        </dt>
                        <dd class="text-2xl font-bold text-red-600 dark:text-red-400">
                            ${{ number_format($summary['overdue_unpaid'], 2) }}
                        </dd>
                        <dd class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            {{ $summary['overdue_count'] }} 筆
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- 未到期未收 -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg border-l-4 border-green-500">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                            未到期
                        </dt>
                        <dd class="text-2xl font-bold text-green-600 dark:text-green-400">
                            ${{ number_format($summary['upcoming_unpaid'], 2) }}
                        </dd>
                        <dd class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            {{ $summary['upcoming_count'] }} 筆
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 客戶統計摘要 -->
@if($companySummary->count() > 0)
<div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">客戶未收統計</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">客戶名稱</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">筆數</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">應收總額</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">已收金額</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">未收金額</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($companySummary as $summary)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                        {{ $summary['company_name'] }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-white">
                        {{ $summary['count'] }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 dark:text-white">
                        ${{ number_format($summary['total_amount'], 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-green-600 dark:text-green-400">
                        ${{ number_format($summary['total_received'], 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-yellow-600 dark:text-yellow-400">
                        ${{ number_format($summary['total_unpaid'], 2) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<!-- 明細表格 -->
<div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">應收未收明細</h2>
    </div>
    
    @if($unpaidReceivables->count() > 0)
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">發票號碼</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">客戶</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">專案</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">發票日期</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">到期日</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">應收金額</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">已收金額</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">未收金額</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">狀態</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($unpaidReceivables as $receivable)
                @php
                    $unpaidAmount = $receivable->amount - $receivable->received_amount;
                    $isOverdue = $receivable->due_date && $receivable->due_date < now();
                    $daysDiff = $receivable->due_date ? now()->diffInDays($receivable->due_date, false) : null;
                @endphp
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 {{ $isOverdue ? 'bg-red-50 dark:bg-red-900/10' : '' }}">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                        {{ $receivable->invoice_no }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                        {{ $receivable->company->name ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                        {{ $receivable->project->name ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ format_date($receivable->invoice_date) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm {{ $isOverdue ? 'text-red-600 dark:text-red-400 font-semibold' : 'text-gray-500 dark:text-gray-400' }}">
                        @if($receivable->due_date)
                            {{ format_date($receivable->due_date) }}
                            @if($daysDiff !== null)
                                <div class="text-xs {{ $isOverdue ? 'text-red-500' : 'text-gray-400' }}">
                                    {{ $isOverdue ? '逾期 ' . abs($daysDiff) . ' 天' : '還有 ' . $daysDiff . ' 天' }}
                                </div>
                            @endif
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 dark:text-white">
                        ${{ number_format($receivable->amount, 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-green-600 dark:text-green-400">
                        ${{ number_format($receivable->received_amount, 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-yellow-600 dark:text-yellow-400">
                        ${{ number_format($unpaidAmount, 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        @if($isOverdue)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                逾期
                            </span>
                        @elseif($receivable->status === 'partial')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                                部分收款
                            </span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                未收款
                            </span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="p-8 text-center text-gray-500 dark:text-gray-400">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <p class="mt-2">太好了！目前沒有應收未收的帳款</p>
    </div>
    @endif
</div>

<!-- 說明卡片 -->
<div class="mt-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
    <div class="flex items-start space-x-3">
        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <div class="text-sm text-blue-800 dark:text-blue-400">
            <h4 class="font-semibold mb-2">報表說明：</h4>
            <ul class="space-y-1 list-disc list-inside">
                <li>僅列出<strong>已開發票</strong>但<strong>尚未完全收款</strong>的應收帳款</li>
                <li>逾期帳款會以紅色背景標示，並顯示逾期天數</li>
                <li>客戶統計依未收金額由大到小排序</li>
                <li>建議優先追蹤逾期帳款和高額未收款客戶</li>
            </ul>
        </div>
    </div>
</div>

<!-- 返回按鈕 -->
<div class="mt-6">
    <a href="{{ route('tenant.reports.financial') }}" 
       class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        返回報表首頁
    </a>
</div>
@endsection
