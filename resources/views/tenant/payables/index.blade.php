@extends('layouts.tenant')

@section('title', '應付帳款管理')

@section('page-title', '應付帳款管理')

@section('content')
<!-- 第一行：分頁資訊 + 操作按鈕 -->
<div class="mb-2 flex justify-between items-center">
    <!-- 左側：分頁資訊 -->
    <div class="text-sm text-gray-600 dark:text-gray-400">
        @if($payables->total() > 0)
            顯示第 <span class="font-medium">{{ $payables->firstItem() }}</span> 
            到 <span class="font-medium">{{ $payables->lastItem() }}</span> 筆，
            共 <span class="font-medium">{{ number_format($payables->total()) }}</span> 筆
        @else
            <span>無資料</span>
        @endif
    </div>
    
    <!-- 右側：操作按鈕 -->
    <div class="flex gap-2">
        @if($payables->total() > 0)
        <a href="{{ route('tenant.payables.export', request()->all()) }}" 
           class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-medium px-4 py-2 rounded-lg transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            匯出
        </a>
        @endif
        <a href="{{ route('tenant.payables.create') }}" 
           class="bg-primary hover:bg-primary-dark text-white font-medium py-2 px-4 rounded-lg shadow-sm whitespace-nowrap">
            + 新增應付帳款
        </a>
    </div>
</div>

<!-- 第二行：付款提醒 -->
@if($overduePayables > 0 || $dueSoon7Days > 0)
<div class="mb-2 bg-gradient-to-r from-red-50 to-orange-50 dark:from-red-900/20 dark:to-orange-900/20 border border-red-200 dark:border-red-700 rounded-lg px-4 py-2">
    <div class="flex items-center gap-3">
        <svg class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <div class="flex items-center gap-4 flex-wrap text-sm">
            <span class="font-semibold text-red-800 dark:text-red-300">付款提醒：</span>
            @if($overduePayables > 0)
            <a href="{{ route('tenant.payables.index', ['status' => 'overdue']) }}" class="flex items-center gap-1.5 hover:opacity-80 transition-opacity">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-600 text-white">逾期</span>
                <span class="text-red-700 dark:text-red-300">有 <strong>{{ $overduePayables }}</strong> 筆已逾期 →</span>
            </a>
            @endif
            @if($dueSoon7Days > 0)
            <a href="{{ route('tenant.payables.index', ['due_filter' => '7']) }}" class="flex items-center gap-1.5 hover:opacity-80 transition-opacity">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-500 text-white">7天內</span>
                <span class="text-orange-700 dark:text-orange-300">有 <strong>{{ $dueSoon7Days }}</strong> 筆即將到期 →</span>
            </a>
            @endif
            @if($dueSoon30Days > 0)
            <a href="{{ route('tenant.payables.index', ['due_filter' => '30']) }}" class="flex items-center gap-1.5 hover:opacity-80 transition-opacity">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-500 text-white">30天內</span>
                <span class="text-yellow-700 dark:text-yellow-300">有 <strong>{{ $dueSoon30Days }}</strong> 筆將到期 →</span>
            </a>
            @endif
        </div>
    </div>
</div>
@endif

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-1 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<!-- 搜尋與篩選 -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-2">
    <form method="GET" action="{{ route('tenant.payables.index') }}" class="space-y-4">
        <!-- 智能搜尋框 -->
        <div class="flex gap-2">
            <div class="flex-1">
                <input type="text" name="smart_search" value="{{ request('smart_search') }}" 
                       placeholder="🔍 聰明尋找：單號/專案/廠商/內容..." 
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary focus:border-transparent text-base">
            </div>
            <button type="submit" 
                    class="bg-primary hover:bg-primary-dark text-white font-medium py-2 px-6 rounded-lg whitespace-nowrap">
                搜尋
            </button>
            @if(request()->hasAny(['smart_search', 'type', 'status', 'fiscal_year']))
                <a href="{{ route('tenant.payables.index') }}" 
                   class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-medium py-2 px-6 rounded-lg whitespace-nowrap">
                    清除
                </a>
            @endif
        </div>

        <!-- 進階篩選 -->
        <details class="group">
            <summary class="cursor-pointer text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-primary">
                <span class="inline-block group-open:rotate-90 transition-transform">▶</span>
                進階篩選
            </summary>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                <!-- 年度選擇器 -->
                <div>
                    <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">帳務年度</label>
                    <select name="fiscal_year" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                        <option value="">全部年度</option>
                        @foreach($availableYears as $year)
                            <option value="{{ $year }}" {{ request('fiscal_year', date('Y')) == $year ? 'selected' : '' }}>
                                {{ $year }} 年
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 類型篩選 -->
                <div>
                    <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">付款類型</label>
                    <select name="type" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                        <option value="">全部類型</option>
                        <option value="purchase" {{ request('type') === 'purchase' ? 'selected' : '' }}>採購</option>
                        <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>費用</option>
                        <option value="service" {{ request('type') === 'service' ? 'selected' : '' }}>服務</option>
                        <option value="other" {{ request('type') === 'other' ? 'selected' : '' }}>其他</option>
                    </select>
                </div>

                <!-- 狀態篩選 -->
                <div>
                    <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">付款狀態</label>
                    <select name="status" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                        <option value="">全部狀態</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>待付款</option>
                        <option value="partial" {{ request('status') === 'partial' ? 'selected' : '' }}>部分付款</option>
                        <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>已付款</option>
                        <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>逾期</option>
                    </select>
                </div>
            </div>
        </details>
    </form>
</div>

<!-- 資料表格 -->
<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th class="px-3 py-2 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="width:50px">序號</th>
                <th class="px-3 py-2 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="width:80px">操作</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="min-width:80px">負責人</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="min-width:90px">日期</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="min-width:60px">類型</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="min-width:100px">對象</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="min-width:180px">專案/內容</th>
                <th class="px-3 py-2 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="min-width:90px">金額</th>
                <th class="px-3 py-2 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="min-width:70px">扣抵</th>
                <th class="px-3 py-2 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="min-width:90px">實付</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="min-width:90px">付款日</th>
                <th class="px-3 py-2 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="min-width:70px">狀態</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="min-width:90px">發票日</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="min-width:100px">發票號</th>
            </tr>
            @if($payables->total() > 0)
            <tr class="bg-blue-50 dark:bg-blue-900/30">
                <td colspan="7" class="px-4 py-2 text-right text-sm font-bold text-gray-900 dark:text-gray-100">
                    總計（{{ $payables->total() }}筆）：
                </td>
                <td class="px-3 py-2 text-right text-sm font-bold text-red-600 dark:text-red-400">
                    NT$ {{ number_format($totalAmount, 0) }}
                </td>
                <td class="px-3 py-2 text-right text-sm font-bold text-orange-600 dark:text-orange-400">
                    NT$ {{ number_format($totalDeduction, 0) }}
                </td>
                <td class="px-3 py-2 text-right text-sm font-bold text-green-600 dark:text-green-400">
                    NT$ {{ number_format($totalPaid, 0) }}
                </td>
                <td colspan="4"></td>
            </tr>
            @endif
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($payables as $index => $payable)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    <!-- 序號 -->
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-center text-gray-900 dark:text-gray-100">
                        {{ ($payables->currentPage() - 1) * $payables->perPage() + $index + 1 }}
                    </td>
                    <!-- 操作 -->
                    <td class="px-3 py-2 whitespace-nowrap text-center text-xs font-medium space-x-1">
                        <a href="{{ route('tenant.payables.edit', $payable) }}" 
                           class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300">編輯</a>
                        @if($payable->status !== 'paid' && $payable->remaining_amount > 0)
                            <a href="{{ route('tenant.payables.quick-pay', $payable) }}"
                               class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300">入帳</a>
                        @endif
                    </td>
                    <!-- 負責人 -->
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                        {{ $payable->responsibleUser?->name ?? '-' }}
                    </td>
                    <!-- 日期 -->
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                        @date($payable->payment_date)
                    </td>
                    <!-- 類型 -->
                    <td class="px-3 py-2 whitespace-nowrap text-xs">
                        @if($payable->payee_type === 'member' || $payable->payee_type === 'user')
                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">成員</span>
                        @elseif($payable->payee_type === 'vendor' || $payable->payee_type === 'company')
                            <span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">外包</span>
                        @elseif($payable->payee_type === 'expense')
                            <span class="px-2 py-1 text-xs rounded-full bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">採購</span>
                        @else
                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">其他</span>
                        @endif
                    </td>
                    <!-- 對象 -->
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                        @if($payable->payee_type === 'user')
                            {{ $payable->payeeUser?->name ?? '-' }}
                        @else
                            {{ $payable->payeeCompany?->short_name ?? $payable->payeeCompany?->name ?? $payable->company?->short_name ?? $payable->company?->name ?? '-' }}
                        @endif
                    </td>
                    <!-- 專案/內容 -->
                    <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300 max-w-xs">
                        @if($payable->project && $payable->content)
                            {{ $payable->project->name }} : {{ Str::limit($payable->content, 20) }}
                        @else
                            {{ $payable->project?->name ?? '' }}{{ $payable->content ? Str::limit($payable->content, 25) : '' }}
                        @endif
                    </td>
                    <!-- 金額 -->
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-right text-red-600 dark:text-red-400 font-medium">
                        NT$ {{ number_format($payable->amount, 0) }}
                    </td>
                    <!-- 扣抵 -->
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-right text-orange-600 dark:text-orange-400">
                        NT$ {{ number_format($payable->deduction ?? 0, 0) }}
                    </td>
                    <!-- 實付 -->
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-right text-green-600 dark:text-green-400">
                        NT$ {{ number_format($payable->paid_amount ?? 0, 0) }}
                    </td>
                    <!-- 付款日 -->
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        @date($payable->paid_date)
                    </td>
                    <!-- 狀態 -->
                    <td class="px-3 py-2 whitespace-nowrap text-center">
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
                    <!-- 發票日 -->
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        @date($payable->invoice_date)
                    </td>
                    <!-- 發票號 -->
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                        {{ $payable->invoice_no ?? '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="14" class="px-4 py-1 text-center text-gray-500 dark:text-gray-400 text-sm">
                        目前沒有應付帳款資料
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- 分頁導航 -->
@if($payables->hasPages())
<div class="mt-6">
    {{ $payables->withQueryString()->links() }}
</div>
@endif
@endsection
