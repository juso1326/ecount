@extends('layouts.tenant')

@section('title', '月結匯款彙總表')
@section('page-title', '月結匯款彙總表')

@section('content')
<!-- 頂部操作列 -->
<div class="mb-3 flex justify-between items-center">
    <a href="{{ route('tenant.salaries.index', ['year' => $year, 'month' => $month]) }}"
       class="text-primary hover:underline text-sm">← 返回薪資總覽</a>
    <button onclick="window.print()"
            class="flex items-center gap-2 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg text-sm transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
        </svg>
        列印
    </button>
</div>

<!-- 月份導航 -->
<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-4 mb-4 no-print">
    <div class="flex items-center justify-between">
        <a href="{{ route('tenant.salaries.disbursement', ['year' => $year, 'month' => $month, 'nav' => 'prev']) }}"
           class="flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-primary transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            上個月
        </a>
        <form method="GET" action="{{ route('tenant.salaries.disbursement') }}" class="flex items-center gap-3">
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
        <a href="{{ route('tenant.salaries.disbursement', ['year' => $year, 'month' => $month, 'nav' => 'next']) }}"
           class="flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-primary transition">
            下個月
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>
</div>

<!-- 彙總表 -->
<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center print-title">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white">
            {{ $period['label'] }} &nbsp;薪資匯款彙總表
        </h2>
        <span class="text-sm text-gray-500 dark:text-gray-400">共 {{ count($salaries) }} 人</span>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-3 py-2 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="width:30px">序號</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">姓名</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">身分字號</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">銀行</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">分行</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">帳號</th>
                    <th class="px-3 py-2 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">應付金額</th>
                    <th class="px-3 py-2 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">撥款狀態</th>
                    <th class="px-3 py-2 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase no-print">操作</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($salaries as $i => $salary)
                @php
                    $isPaid = $salary['items']->where('is_salary_paid', true)->count() > 0;
                    $bank = $salary['bank'] ?? null;
                @endphp
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition {{ $isPaid ? 'opacity-60' : '' }}">
                    <td class="px-3 py-2 text-sm text-center text-gray-500 dark:text-gray-400">{{ $i + 1 }}</td>
                    <td class="px-3 py-2 text-sm font-medium text-gray-900 dark:text-white">
                        {{ $salary['user']->name }}
                        @if($salary['user']->position)
                            <div class="text-xs text-gray-400">{{ $salary['user']->position }}</div>
                        @endif
                    </td>
                    <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">
                        {{ $salary['user']->id_number ?? '—' }}
                    </td>
                    <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">
                        {{ $bank->bank_name ?? '—' }}
                    </td>
                    <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">
                        {{ $bank->bank_branch ?? '—' }}
                    </td>
                    <td class="px-3 py-2 text-sm font-mono text-gray-900 dark:text-white">
                        {{ $bank->bank_account ?? '—' }}
                    </td>
                    <td class="px-3 py-2 text-sm text-right font-semibold text-gray-900 dark:text-white">
                        ${{ fmt_num($salary['total']) }}
                    </td>
                    <td class="px-3 py-2 text-sm text-center">
                        @if($isPaid)
                            <span class="px-2 py-0.5 inline-flex text-xs font-semibold rounded-full bg-green-100 text-green-800">已撥款</span>
                        @else
                            <span class="px-2 py-0.5 inline-flex text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">未撥款</span>
                        @endif
                    </td>
                    <td class="px-3 py-2 text-sm text-center no-print">
                        <a href="{{ route('tenant.salaries.show', ['user' => $salary['user']->id, 'year' => $year, 'month' => $month]) }}"
                           class="text-blue-600 hover:text-blue-800 dark:text-blue-400">查看明細</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400 text-sm">本月無薪資記錄</td>
                </tr>
                @endforelse
            </tbody>
            @if(count($salaries) > 0)
            <tfoot class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <td colspan="6" class="px-3 py-2 text-sm font-bold text-gray-900 dark:text-white text-right">合計</td>
                    <td class="px-3 py-2 text-sm font-bold text-right text-blue-600 dark:text-blue-400">
                        ${{ fmt_num($total) }}
                    </td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection

@push('styles')
<style>
@media print {
    .no-print { display: none !important; }
    .sidebar, nav, header, footer { display: none !important; }
    body { background: white !important; }
    .bg-white { box-shadow: none !important; border: 1px solid #ccc !important; }
    .print-title h2 { font-size: 16px; font-weight: bold; }
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #ccc; padding: 4px 8px; font-size: 12px; }
    th { background: #f5f5f5 !important; }
}
</style>
@endpush
