@extends('layouts.tenant')

@section('title', '應付帳款管理')

@section('page-title', '應付帳款管理')

@section('content')
<!-- 付款提醒 -->
@if($overduePayables > 0 || $dueSoon7Days > 0)
<div class="mb-4 bg-gradient-to-r from-red-50 to-orange-50 dark:from-red-900/20 dark:to-orange-900/20 border border-red-200 dark:border-red-700 rounded-lg p-4">
    <div class="flex items-start gap-3">
        <svg class="w-6 h-6 text-red-600 dark:text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <div class="flex-1">
            <h3 class="text-sm font-semibold text-red-800 dark:text-red-300 mb-2">付款提醒</h3>
            <div class="space-y-1 text-sm">
                @if($overduePayables > 0)
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-600 text-white">
                        逾期
                    </span>
                    <span class="text-red-700 dark:text-red-300">
                        有 <strong class="text-lg">{{ $overduePayables }}</strong> 筆應付帳款已逾期，請盡快處理
                    </span>
                </div>
                @endif
                @if($dueSoon7Days > 0)
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-500 text-white">
                        7天內
                    </span>
                    <span class="text-orange-700 dark:text-orange-300">
                        有 <strong class="text-lg">{{ $dueSoon7Days }}</strong> 筆應付帳款即將到期
                    </span>
                </div>
                @endif
                @if($dueSoon30Days > 0)
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-500 text-white">
                        30天內
                    </span>
                    <span class="text-yellow-700 dark:text-yellow-300">
                        有 <strong>{{ $dueSoon30Days }}</strong> 筆應付帳款將在一個月內到期
                    </span>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

<div class="mb-2 flex justify-end items-center">
    <a href="{{ route('tenant.payables.create') }}" 
       class="bg-primary hover:bg-primary-dark text-white font-medium py-2 px-4 rounded-lg shadow-sm">
        + 新增應付帳款
    </a>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-1 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<!-- 搜尋與篩選 -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-2">
    <form method="GET" action="{{ route('tenant.payables.index') }}">
        <!-- 年度選擇器 -->
        <div class="flex items-center gap-3 mb-4">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">帳務年度：</label>
            <select name="fiscal_year" 
                    onchange="this.form.submit()"
                    class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
                <option value="">全部年度</option>
                @foreach($availableYears as $year)
                    <option value="{{ $year }}" {{ request('fiscal_year', date('Y')) == $year ? 'selected' : '' }}>
                        {{ $year }} 年
                    </option>
                @endforeach
            </select>
            @if(request('fiscal_year'))
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    目前顯示：<span class="font-semibold text-primary">{{ request('fiscal_year') }} 年度</span> 的應付帳款
                </span>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="hidden" name="fiscal_year" value="{{ request('fiscal_year') }}">
            
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="搜尋單號、專案代碼/名稱、廠商、內容..." 
                   class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
            
            <select name="type" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2">
                <option value="">全部類型</option>
                <option value="purchase" {{ request('type') === 'purchase' ? 'selected' : '' }}>採購</option>
                <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>費用</option>
                <option value="service" {{ request('type') === 'service' ? 'selected' : '' }}>服務</option>
                <option value="other" {{ request('type') === 'other' ? 'selected' : '' }}>其他</option>
            </select>

            <select name="status" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2">
                <option value="">全部狀態</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>待付款</option>
                <option value="partial" {{ request('status') === 'partial' ? 'selected' : '' }}>部分付款</option>
                <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>已付款</option>
                <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>逾期</option>
            </select>

            <div class="flex gap-2">
                <button type="submit" class="bg-primary hover:bg-primary-dark text-white font-medium py-2 px-4 rounded-lg flex-1">
                    搜尋
                </button>
                @if(request()->hasAny(['search', 'type', 'status', 'fiscal_year']))
                    <a href="{{ route('tenant.payables.index') }}" 
                       class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded-lg">清除</a>
                @endif
            </div>
        </div>
    </form>
</div>

<!-- 資料表格 -->
<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">付款日期</th>
                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">單號</th>
                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">類型</th>
                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">廠商</th>
                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">內容</th>
                <th class="px-4 py-2 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">應付金額</th>
                <th class="px-4 py-2 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">未付金額</th>
                <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">狀態</th>
                <th class="px-3 py-2 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">操作</th>
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($payables as $payable)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    <td class="px-4 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">
                        {{ $payable->payment_date->format('Y/m/d') }}
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-xs font-medium text-gray-900 dark:text-gray-100">
                        {{ $payable->payment_no }}
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-xs">
                        @if($payable->type === 'purchase')
                            <span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800">採購</span>
                        @elseif($payable->type === 'expense')
                            <span class="px-2 py-1 text-xs rounded-full bg-orange-100 text-orange-800">費用</span>
                        @elseif($payable->type === 'service')
                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">服務</span>
                        @else
                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">其他</span>
                        @endif
                    </td>
                    <td class="px-4 py-2 text-xs text-gray-900 dark:text-gray-100">
                        {{ $payable->company ? $payable->company->name : '-' }}
                    </td>
                    <td class="px-4 py-2 text-xs text-gray-700 dark:text-gray-300 max-w-xs">
                        {{ Str::limit($payable->content, 20) }}
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-xs text-right text-red-600 dark:text-red-400 font-medium">
                        NT$ {{ number_format($payable->amount, 0) }}
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-xs text-right {{ $payable->remaining_amount > 0 ? 'text-red-600 dark:text-red-400 font-semibold' : 'text-gray-400' }}">
                        NT$ {{ number_format($payable->remaining_amount, 0) }}
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-center">
                        @if($payable->status === 'paid')
                            <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full bg-green-100 text-green-800">已付</span>
                        @elseif($payable->status === 'partial')
                            <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full bg-blue-100 text-blue-800">部分</span>
                        @elseif($payable->status === 'overdue')
                            <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full bg-red-100 text-red-800">逾期</span>
                        @else
                            <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">待付</span>
                        @endif
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap text-center text-xs font-medium space-x-1">
                        <a href="{{ route('tenant.payables.show', $payable) }}" 
                           class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">詳細</a>
                        <a href="{{ route('tenant.payables.edit', $payable) }}" 
                           class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300">編輯</a>
                        
                        @if($payable->status !== 'paid' && $payable->remaining_amount > 0)
                            <a href="{{ route('tenant.payables.quick-pay', $payable) }}"
                               class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300">付款</a>
                        @endif
                        
                        <form action="{{ route('tenant.payables.destroy', $payable) }}" 
                              method="POST" class="inline"
                              onsubmit="return confirm('確定要刪除此應付帳款嗎？');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">刪除</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="px-4 py-1 text-center text-gray-500 dark:text-gray-400 text-sm">
                        目前沒有應付帳款資料
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if($payables->count() > 0)
        <tfoot class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <td colspan="5" class="px-4 py-2 text-right text-xs font-semibold text-gray-900 dark:text-gray-100">總計：</td>
                <td class="px-4 py-2 text-right text-xs font-bold text-red-600 dark:text-red-400">
                    NT$ {{ number_format($totalAmount, 0) }}
                </td>
                <td class="px-4 py-2 text-right text-xs font-bold text-red-600 dark:text-red-400">
                    NT$ {{ number_format($totalAmount - $totalPaid, 0) }}
                </td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>

<div class="mt-6">
    {{ $payables->withQueryString()->links() }}
</div>
@endsection
