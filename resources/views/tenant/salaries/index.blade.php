@extends('layouts.tenant')

@section('title', '薪資管理')

@section('page-title', '薪資管理')

@section('content')
<!-- 麵包屑 -->
<div class="mb-4">
    <p class="text-sm text-gray-600 dark:text-gray-400">財務管理 &gt; 薪資管理</p>
</div>

<!-- 頁面標題 -->
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">薪資總表</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $period['label'] }}</p>
    </div>
</div>

<!-- 月份選擇 -->
<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-4 mb-6">
    <form method="GET" action="{{ route('tenant.salaries.index') }}" class="flex items-center gap-4">
        <div>
            <select name="year" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2">
                @for($y = date('Y'); $y >= 2020; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}年</option>
                @endfor
            </select>
        </div>
        <div>
            <select name="month" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2">
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}" {{ $month == str_pad($m, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>{{ $m }}月</option>
                @endfor
            </select>
        </div>
        <button type="submit" class="bg-primary hover:bg-primary-dark text-white px-6 py-2 rounded-lg transition">
            查詢
        </button>
    </form>
</div>

<!-- 薪資列表 -->
<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">員工</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">基本薪資</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">加項</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">扣項</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">總計</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">狀態</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">操作</th>
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($salaries as $salary)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $salary['user']->name }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900 dark:text-white">
                    ${{ number_format($salary['base_salary'], 0) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-green-600">
                    @if($salary['additions'] > 0)
                        +${{ number_format($salary['additions'], 0) }}
                    @else
                        -
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-red-600">
                    @if($salary['deductions'] > 0)
                        -${{ number_format($salary['deductions'], 0) }}
                    @else
                        -
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-gray-900 dark:text-white">
                    ${{ number_format($salary['total'], 0) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                    @if($salary['items']->where('is_salary_paid', true)->count() > 0)
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">已撥款</span>
                    @else
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">未撥款</span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
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
                <td class="px-6 py-3 text-sm font-bold text-gray-900 dark:text-white">總計</td>
                <td colspan="3"></td>
                <td class="px-6 py-3 text-right text-sm font-bold text-gray-900 dark:text-white">
                    ${{ number_format($total, 0) }}
                </td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>
@endsection
