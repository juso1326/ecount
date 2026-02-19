@extends('layouts.tenant')

@section('title', '財務綜合分析')

@section('page-title', '財務綜合分析')

@section('content')
<div class="mb-4">
    <div class="flex justify-between items-center">
        <form method="GET" class="flex gap-2">
            <select name="year" onchange="this.form.submit()" 
                    class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2">
                @for($y = date('Y'); $y >= 2020; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }} 年</option>
                @endfor
            </select>
        </form>
    </div>
</div>

<!-- 總覽卡片 -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">總收入</div>
        <div class="text-2xl font-bold text-green-600 dark:text-green-400">
            NT$ {{ number_format($summary['total_revenue'], 0) }}
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">總支出</div>
        <div class="text-2xl font-bold text-red-600 dark:text-red-400">
            NT$ {{ number_format($summary['total_expense'], 0) }}
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">淨利潤</div>
        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
            NT$ {{ number_format($summary['net_profit'], 0) }}
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">預計帳回款</div>
        <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
            NT$ {{ number_format($summary['expected_revenue'], 0) }}
        </div>
    </div>
</div>

<!-- 圖表區 -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- 每月營收 vs. 支出趨勢 -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">每月營收 vs. 支出趨勢</h2>
        <div style="position: relative; height: 300px;">
            <canvas id="monthlyTrendsChart"></canvas>
        </div>
    </div>
    
    <!-- 支出比例分析 -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">支出比例分析</h2>
        <div style="height: 300px;">
            <canvas id="expenseBreakdownChart"></canvas>
        </div>
    </div>
</div>

<!-- 明細表格 -->
<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">月度明細</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">月份</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">營收</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">支出</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">利潤</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($monthlyTrends as $trend)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $trend['month'] }}月</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-green-600 dark:text-green-400">
                        NT$ {{ number_format($trend['revenue'], 0) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-red-600 dark:text-red-400">
                        NT$ {{ number_format($trend['expense'], 0) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold {{ $trend['profit'] >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                        NT$ {{ number_format($trend['profit'], 0) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// 每月趨勢圖
const monthlyCtx = document.getElementById('monthlyTrendsChart').getContext('2d');
new Chart(monthlyCtx, {
    type: 'line',
    data: {
        labels: @json(array_column($monthlyTrends, 'month')).map(m => m + '月'),
        datasets: [{
            label: '營收',
            data: @json(array_column($monthlyTrends, 'revenue')),
            borderColor: 'rgb(34, 197, 94)',
            backgroundColor: 'rgba(34, 197, 94, 0.1)',
            tension: 0.4
        }, {
            label: '支出',
            data: @json(array_column($monthlyTrends, 'expense')),
            borderColor: 'rgb(239, 68, 68)',
            backgroundColor: 'rgba(239, 68, 68, 0.1)',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        aspectRatio: 2,
        plugins: {
            legend: { position: 'top' }
        },
        scales: {
            x: {
                grid: { display: false }
            },
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'NT$ ' + value.toLocaleString();
                    }
                }
            }
        }
    }
});

// 支出比例圖
const expenseCtx = document.getElementById('expenseBreakdownChart').getContext('2d');
new Chart(expenseCtx, {
    type: 'pie',
    data: {
        labels: @json($expenseBreakdown->pluck('type')),
        datasets: [{
            data: @json($expenseBreakdown->pluck('total')),
            backgroundColor: [
                'rgb(59, 130, 246)', 'rgb(34, 197, 94)', 'rgb(251, 191, 36)',
                'rgb(239, 68, 68)', 'rgb(168, 85, 247)', 'rgb(236, 72, 153)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'right' }
        }
    }
});
</script>
@endsection
