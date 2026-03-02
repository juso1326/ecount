@extends('layouts.tenant')

@section('title', '廠商支付記錄')

@section('page-title', '廠商支付記錄')

@section('content')
<!-- 頁面標題 -->
<div class="mb-2 flex justify-between items-center">
    <a href="{{ route('tenant.salaries.index', ['year' => $year, 'month' => $month]) }}" 
       class="text-primary hover:underline">
        返回成員薪資
    </a>
</div>

@if(session('success'))
<div class="mb-4 px-4 py-3 rounded-lg bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-300 text-sm">
    {{ session('success') }}
</div>
@endif

<!-- 月份選擇與導航 -->
<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-4 mb-6">
    <div class="flex items-center justify-between">
        <!-- 上個月按鈕 -->
        <a href="{{ route('tenant.salaries.vendors', ['year' => $year, 'month' => $month, 'nav' => 'prev']) }}" 
           class="flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-primary transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            <span class="hidden sm:inline">上個月</span>
        </a>
        
        <!-- 月份選擇表單 -->
        <form method="GET" action="{{ route('tenant.salaries.vendors') }}" class="flex items-center gap-3">
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
        <a href="{{ route('tenant.salaries.vendors', ['year' => $year, 'month' => $month, 'nav' => 'next']) }}" 
           class="flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-primary transition">
            <span class="hidden sm:inline">下個月</span>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>
    @if(!empty($period['label']))
    <div class="mt-2 text-center text-xs text-gray-400 dark:text-gray-500">{{ $period['label'] }}</div>
    @endif
</div>

<!-- 統計摘要 -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-4">
        <div class="text-sm text-gray-500 dark:text-gray-400">總支付金額</div>
        <div class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
            ${{ fmt_num($total) }}
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-4">
        <div class="text-sm text-gray-500 dark:text-gray-400">已撥款</div>
        <div class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">
            ${{ fmt_num($paid_total) }}
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-4">
        <div class="text-sm text-gray-500 dark:text-gray-400">未撥款</div>
        <div class="text-2xl font-bold text-red-600 dark:text-red-400 mt-1">
            ${{ fmt_num($unpaid_total) }}
        </div>
    </div>
</div>

<!-- 廠商列表 -->
@if($payments->count() > 0)
    @foreach($payments as $companyId => $data)
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 mb-4">
        <!-- 廠商標題 -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-750">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $data['company']->name ?? '未知廠商' }}
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        統一編號：{{ $data['company']->tax_id ?? '-' }}
                    </p>
                </div>
                <div class="text-right">
                    <div class="text-lg font-bold text-gray-900 dark:text-white">
                        ${{ fmt_num($data['total']) }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        已付：${{ fmt_num($data['paid_total']) }} | 
                        未付：${{ fmt_num($data['unpaid_total']) }}
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 支付明細 -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">日期</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">專案</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">內容</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">金額</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">狀態</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">操作</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($data['items'] as $payment)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            {{ format_date($payment->payment_date) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            {{ $payment->project->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                            {{ $payment->content }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 dark:text-white">
                            ${{ fmt_num($payment->amount) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                            @if($payment->is_salary_paid)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    已撥款
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                    未撥款
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                            @if($payment->is_salary_paid)
                                <span class="text-gray-400 text-xs">
                                    {{ format_date($payment->salary_paid_at) }}
                                </span>
                            @else
                                <form method="POST" action="{{ route('tenant.salaries.vendors.confirm', $payment) }}" onsubmit="return confirm('確認撥款？')">
                                    @csrf
                                    <input type="hidden" name="year" value="{{ $year }}">
                                    <input type="hidden" name="month" value="{{ $month }}">
                                    <button type="submit" class="text-primary hover:text-primary-dark font-medium">
                                        確認撥款
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endforeach
@else
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-8 text-center">
        <p class="text-gray-500 dark:text-gray-400">本月無廠商支付記錄</p>
    </div>
@endif
@endsection
