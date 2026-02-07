@extends('layouts.tenant')

@section('title', '外製成本回收報表')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-semibold text-gray-900 dark:text-white">外製成本回收報表</h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">已付外包成本與應收款項回收狀況追蹤</p>
        </div>
        
        <!-- 篩選器 -->
        <form method="GET" action="{{ route('tenant.reports.financial.outsource-cost-recovery') }}" class="flex items-center space-x-3">
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
                <label for="project_id" class="text-sm font-medium text-gray-700 dark:text-gray-300">專案：</label>
                <select name="project_id" id="project_id" 
                        class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    <option value="">全部專案</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ $project->id == $projectId ? 'selected' : '' }}>
                            {{ $project->code }} - {{ $project->name }}
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
    <!-- 已付外包成本 -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg border-l-4 border-purple-500">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                            已付外包成本
                        </dt>
                        <dd class="text-2xl font-bold text-gray-900 dark:text-white">
                            ${{ number_format($summary['total_paid_outsource'], 2) }}
                        </dd>
                        <dd class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            {{ $summary['total_projects'] }} 個專案
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- 已回收金額 -->
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
                            已回收金額
                        </dt>
                        <dd class="text-2xl font-bold text-green-600 dark:text-green-400">
                            ${{ number_format($summary['total_received'], 2) }}
                        </dd>
                        <dd class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            回收率：{{ number_format($summary['overall_recovery_rate'], 1) }}%
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- 未回收成本 -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg border-l-4 border-orange-500">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                            未回收成本
                        </dt>
                        <dd class="text-2xl font-bold text-orange-600 dark:text-orange-400">
                            ${{ number_format($summary['total_cost_not_recovered'], 2) }}
                        </dd>
                        <dd class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            未收：${{ number_format($summary['total_unpaid_receivable'], 2) }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- 高風險專案 -->
    <div class="bg-gradient-to-br from-red-500 to-red-600 overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-white/90 truncate">
                            高風險專案
                        </dt>
                        <dd class="text-2xl font-bold text-white">
                            {{ $summary['high_risk_count'] }}
                        </dd>
                        <dd class="text-xs text-white/80 mt-1">
                            中風險：{{ $summary['medium_risk_count'] }} 個
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 專案明細表格 -->
<div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">專案明細</h2>
    </div>
    
    @if($projectSummaries->count() > 0)
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">專案</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">已付外包</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">應收總額</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">已收金額</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">回收率</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">未回收成本</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">發票狀態</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">風險等級</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($projectSummaries as $data)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 {{ $data['risk_level'] === 'high' ? 'bg-red-50 dark:bg-red-900/10' : '' }}">
                    <td class="px-6 py-4 text-sm">
                        <div class="font-medium text-gray-900 dark:text-white">{{ $data['project']->code }}</div>
                        <div class="text-gray-500 dark:text-gray-400 text-xs">{{ $data['project']->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-purple-600 dark:text-purple-400">
                        ${{ number_format($data['paid_outsource'], 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 dark:text-white">
                        ${{ number_format($data['total_receivable'], 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-green-600 dark:text-green-400">
                        ${{ number_format($data['total_received'], 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                        <span class="font-semibold {{ $data['recovery_rate'] >= 100 ? 'text-green-600' : ($data['recovery_rate'] >= 50 ? 'text-orange-600' : 'text-red-600') }}">
                            {{ number_format($data['recovery_rate'], 1) }}%
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold {{ $data['cost_not_recovered'] > 0 ? 'text-orange-600 dark:text-orange-400' : 'text-gray-500' }}">
                        ${{ number_format($data['cost_not_recovered'], 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        @if($data['has_invoice'])
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                已開發票
                            </span>
                            @if($data['unpaid_receivable'] > 0)
                                <div class="text-xs text-yellow-600 dark:text-yellow-400 mt-1">
                                    未收：${{ number_format($data['unpaid_receivable'], 2) }}
                                </div>
                            @endif
                        @elseif($data['uninvoiced_amount'] > 0)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                                未開發票
                            </span>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                ${{ number_format($data['uninvoiced_amount'], 2) }}
                            </div>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300">
                                無應收
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        @if($data['risk_level'] === 'high')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                高風險
                            </span>
                        @elseif($data['risk_level'] === 'medium')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300">
                                中風險
                            </span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                低風險
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
        <p class="mt-2">目前沒有外包成本支出記錄</p>
    </div>
    @endif
</div>

<!-- 說明卡片 -->
<div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- 風險等級說明 -->
    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
        <div class="flex items-start space-x-3">
            <svg class="w-5 h-5 text-red-600 dark:text-red-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <div class="text-sm text-red-800 dark:text-red-400">
                <h4 class="font-semibold mb-2">風險等級定義：</h4>
                <ul class="space-y-1 list-disc list-inside">
                    <li><strong class="text-red-600">高風險</strong>：已付外包成本但完全沒開發票</li>
                    <li><strong class="text-orange-600">中風險</strong>：未收款超過外包成本50%</li>
                    <li><strong class="text-green-600">低風險</strong>：已開發票且回收狀況良好</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- 計算說明 -->
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
        <div class="flex items-start space-x-3">
            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="text-sm text-blue-800 dark:text-blue-400">
                <h4 class="font-semibold mb-2">計算說明：</h4>
                <ul class="space-y-1 list-disc list-inside">
                    <li><strong>回收率</strong> = 已收金額 ÷ 已付外包成本 × 100%</li>
                    <li><strong>未回收成本</strong> = 已付外包成本 - 已收金額</li>
                    <li>目標：回收率應達 100% 以上才能回本</li>
                    <li>建議：優先追蹤高風險與低回收率專案</li>
                </ul>
            </div>
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
