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
    <div class="flex gap-2 items-center">
        <!-- 視圖切換 -->
        <div class="flex rounded-lg border border-gray-300 dark:border-gray-600 overflow-hidden text-sm">
            <button id="btn-view-summary" onclick="setPayableView('summary')"
                class="px-3 py-1.5 font-medium transition whitespace-nowrap">
                應付總表
            </button>
            <button id="btn-view-disbursement" onclick="setPayableView('disbursement')"
                class="px-3 py-1.5 font-medium transition whitespace-nowrap border-l border-gray-300 dark:border-gray-600">
                出帳管理
            </button>
        </div>
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

<!-- 搜尋與篩選 -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-2">
    <form method="GET" action="{{ route('tenant.payables.index') }}">
        <div class="flex flex-wrap gap-2 items-center">
            <!-- 搜尋框 (半寬) -->
            <div class="w-1/2 min-w-[200px]">
                <input type="text" name="smart_search" value="{{ request('smart_search') }}" 
                       placeholder="🔍 聰明尋找：單號/專案/廠商/內容..." 
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent text-sm">
            </div>
            <!-- 年度 -->
            <select name="fiscal_year" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                <option value="">全部年度</option>
                @foreach($availableYears as $year)
                    <option value="{{ $year }}" {{ request('fiscal_year', date('Y')) == $year ? 'selected' : '' }}>{{ $year }} 年</option>
                @endforeach
            </select>
            <!-- 類型 -->
            <select name="type" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                <option value="">全部類型</option>
                <option value="purchase" {{ request('type') === 'purchase' ? 'selected' : '' }}>採購</option>
                <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>費用</option>
                <option value="service" {{ request('type') === 'service' ? 'selected' : '' }}>服務</option>
                <option value="other" {{ request('type') === 'other' ? 'selected' : '' }}>其他</option>
            </select>
            <!-- 狀態 -->
            <select name="status" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                <option value="">全部狀態</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>待付款</option>
                <option value="partial" {{ request('status') === 'partial' ? 'selected' : '' }}>部分付款</option>
                <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>已付款</option>
                <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>逾期</option>
            </select>
            <button type="submit" class="bg-primary hover:bg-primary-dark text-white font-medium py-2 px-5 rounded-lg whitespace-nowrap text-sm">搜尋</button>
            @if(request()->hasAny(['smart_search', 'type', 'status', 'fiscal_year']))
                <a href="{{ route('tenant.payables.index') }}" class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-700 dark:text-white font-medium py-2 px-4 rounded-lg whitespace-nowrap text-sm">清除</a>
            @endif
        </div>
    </form>
</div>

<!-- 資料表格 -->

{{-- ===== 應付總表 ===== --}}
<!-- 批次付款工具列 -->
<div id="batch-bar" class="hidden mb-2 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 rounded-lg px-4 py-2 flex items-center gap-3">
    <span class="text-sm font-medium text-green-800 dark:text-green-200">已選 <span id="batch-count">0</span> 筆</span>
    <button onclick="openBatchPayModal()" class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-4 py-1.5 rounded-lg">批次確認支付</button>
    <button onclick="clearBatchSelection()" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">取消選取</button>
</div>

<div id="view-summary" class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th class="px-3 py-2 text-center" style="width:36px"><input type="checkbox" id="select-all" class="rounded border-gray-300" onchange="toggleAllPayables(this)"></th>
                <th class="px-3 py-2 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="width:40px">No.</th>
                <th class="px-3 py-2 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="width:70px">操作</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="min-width:90px">日期</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="min-width:70px">負責人</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="min-width:100px">案名</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="min-width:160px">支付內容</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="min-width:60px">類別</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="min-width:100px">對象/供應商</th>
                <th class="px-3 py-2 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="min-width:50px">未稅</th>
                <th class="px-3 py-2 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="min-width:90px">應付</th>
                <th class="px-3 py-2 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="min-width:60px">狀態</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="min-width:110px">憑證發票</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="min-width:90px">發票日</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="min-width:90px">實付日</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="min-width:100px">備註</th>
            </tr>
            @if($payables->total() > 0)
            <tr class="bg-blue-50 dark:bg-blue-900/30">
                <td></td>
                <td colspan="8" class="px-4 py-2 text-right text-sm font-bold text-gray-900 dark:text-gray-100">
                    總計（{{ $payables->total() }}筆）：
                </td>
                <td class="px-3 py-2 text-right text-sm font-bold text-red-600 dark:text-red-400" colspan="2">
                    NT$ {{ fmt_num($totalAmount) }}
                </td>
                <td colspan="4"></td>
            </tr>
            @endif
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($payables as $index => $payable)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    <td class="px-3 py-2 text-center">
                        @if(!in_array($payable->status, ['paid']))
                        <input type="checkbox" class="payable-checkbox rounded border-gray-300"
                               value="{{ $payable->id }}"
                               data-amount="{{ $payable->remaining_amount }}"
                               onchange="updateBatchBar()">
                        @endif
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap text-xs text-center text-gray-500 dark:text-gray-400">
                        {{ ($payables->currentPage() - 1) * $payables->perPage() + $index + 1 }}
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap text-center text-xs font-medium space-x-1">
                        <a href="{{ route('tenant.payables.edit', $payable) }}" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400">編輯</a>
                        <button onclick="openQuickPayModal({{ $payable->id }},{{ (int)$payable->remaining_amount }},'{{ addslashes($payable->payment_no) }}','{{ addslashes($payable->payeeUser?->name ?? $payable->payeeCompany?->short_name ?? $payable->expense_company_name ?? '') }}','{{ addslashes($payable->content ?? '') }}','{{ addslashes($payable->project?->name ?? '') }}',{{ (int)$payable->amount }})" class="text-green-600 hover:text-green-800 dark:text-green-400">出帳</button>
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                        @date($payable->payment_date)
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                        {{ $payable->responsibleUser?->name ?? '-' }}
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                        {{ $payable->project?->name ?? '-' }}
                    </td>
                    <td class="px-3 py-2 text-sm text-gray-900 dark:text-white">
                        <div class="truncate max-w-[180px]" title="{{ $payable->content }}">{{ Str::limit($payable->content ?? '-', 30) }}</div>
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap text-xs">
                        @if(in_array($payable->payee_type, ['user','member']))
                            <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">成員</span>
                        @elseif(in_array($payable->payee_type, ['vendor','company']))
                            <span class="px-2 py-0.5 rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">外包</span>
                        @elseif($payable->payee_type === 'expense')
                            <span class="px-2 py-0.5 rounded-full bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">採購</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">其他</span>
                        @endif
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                        @if(in_array($payable->payee_type, ['user','member']))
                            {{ $payable->payeeUser?->name ?? '-' }}
                        @elseif($payable->payee_type === 'expense')
                            {{ $payable->expense_company_name ?? '-' }}
                        @else
                            {{ $payable->payeeCompany?->short_name ?? $payable->payeeCompany?->name ?? '-' }}
                        @endif
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap text-center text-xs">
                        @if(!$payable->invoice_no && !$payable->invoice_date)
                            <span class="px-1.5 py-0.5 rounded bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400">未稅</span>
                        @else
                            <span class="text-gray-300">—</span>
                        @endif
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-right font-medium text-red-600 dark:text-red-400">
                        NT$ {{ fmt_num($payable->amount) }}
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap text-center">
                        @if($payable->status === 'paid')
                            <span class="px-2 py-0.5 inline-flex text-xs font-semibold rounded-full bg-green-100 text-green-800">已付</span>
                        @elseif($payable->status === 'partial')
                            <span class="px-2 py-0.5 inline-flex text-xs font-semibold rounded-full bg-blue-100 text-blue-800">部分</span>
                        @elseif($payable->status === 'overdue')
                            <span class="px-2 py-0.5 inline-flex text-xs font-semibold rounded-full bg-red-100 text-red-800">逾期</span>
                        @else
                            <span class="px-2 py-0.5 inline-flex text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">待付</span>
                        @endif
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                        {{ $payable->invoice_no ?? '—' }}
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        @date($payable->invoice_date)
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        @date($payable->paid_date)
                    </td>
                    <td class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400 max-w-[120px]">
                        <div class="truncate" title="{{ $payable->note }}">{{ $payable->note ?? '—' }}</div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="16" class="px-4 py-4 text-center text-gray-500 dark:text-gray-400 text-sm">目前沒有應付帳款資料</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- ===== 出帳管理 ===== --}}
<div id="view-disbursement" class="hidden bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th class="px-3 py-2 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="width:40px">No.</th>
                <th class="px-3 py-2 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="width:70px">操作</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="min-width:70px">負責人</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="min-width:100px">對象/供應商</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="min-width:60px">類別</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="min-width:100px">案名</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="min-width:160px">支付內容</th>
                <th class="px-3 py-2 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="min-width:90px">應付總計</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="min-width:90px">預計付款日</th>
                <th class="px-3 py-2 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="min-width:60px">狀態</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="min-width:100px">備註</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="min-width:140px">銀行/帳號</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="min-width:90px">實付日</th>
                <th class="px-3 py-2 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="min-width:70px">會計年度</th>
            </tr>
            @if($payables->total() > 0)
            <tr class="bg-blue-50 dark:bg-blue-900/30">
                <td colspan="6" class="px-4 py-2 text-right text-sm font-bold text-gray-900 dark:text-gray-100">
                    總計（{{ $payables->total() }}筆）：
                </td>
                <td class="px-3 py-2 text-right text-sm font-bold text-red-600 dark:text-red-400" colspan="2" >
                    NT$ {{ fmt_num($totalAmount) }}
                </td>
                <td colspan="6"></td>
            </tr>
            @endif
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($payables as $index => $payable)
                @php
                    $bank = null;
                    if (in_array($payable->payee_type, ['user','member'])) {
                        $bank = $payable->payeeUser?->defaultBankAccount;
                    } else {
                        $bank = $payable->payeeCompany?->defaultBankAccount;
                    }
                @endphp
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    <td class="px-3 py-2 whitespace-nowrap text-xs text-center text-gray-500 dark:text-gray-400">
                        {{ ($payables->currentPage() - 1) * $payables->perPage() + $index + 1 }}
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap text-center text-s font-medium space-x-1">
                        <a href="{{ route('tenant.payables.edit', $payable) }}" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400">編輯</a>
                        <button onclick="openQuickPayModal({{ $payable->id }},{{ (int)$payable->remaining_amount }},'{{ addslashes($payable->payment_no) }}','{{ addslashes($payable->payeeUser?->name ?? $payable->payeeCompany?->short_name ?? $payable->expense_company_name ?? '') }}','{{ addslashes($payable->content ?? '') }}','{{ addslashes($payable->project?->name ?? '') }}',{{ (int)$payable->amount }})" class="text-green-600 hover:text-green-800 dark:text-green-400">出帳</button>
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                        {{ $payable->responsibleUser?->name ?? '-' }}
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                        @if(in_array($payable->payee_type, ['user','member']))
                            {{ $payable->payeeUser?->name ?? '-' }}
                        @elseif($payable->payee_type === 'expense')
                            {{ $payable->expense_company_name ?? '-' }}
                        @else
                            {{ $payable->payeeCompany?->short_name ?? $payable->payeeCompany?->name ?? '-' }}
                        @endif
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap text-xs">
                        @if(in_array($payable->payee_type, ['user','member']))
                            <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">成員</span>
                        @elseif(in_array($payable->payee_type, ['vendor','company']))
                            <span class="px-2 py-0.5 rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">外包</span>
                        @elseif($payable->payee_type === 'expense')
                            <span class="px-2 py-0.5 rounded-full bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">採購</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">其他</span>
                        @endif
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                        {{ $payable->project?->name ?? '-' }}
                    </td>
                    <td class="px-3 py-2 text-sm text-gray-900 dark:text-white">
                        <div class="truncate max-w-[180px]" title="{{ $payable->content }}">{{ Str::limit($payable->content ?? '-', 30) }}</div>
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-right font-medium text-red-600 dark:text-red-400">
                        NT$ {{ fmt_num($payable->amount) }}
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                        @date($payable->due_date)
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap text-center">
                        @if($payable->status === 'paid')
                            <span class="px-2 py-0.5 inline-flex text-xs font-semibold rounded-full bg-green-100 text-green-800">已付</span>
                        @elseif($payable->status === 'partial')
                            <span class="px-2 py-0.5 inline-flex text-xs font-semibold rounded-full bg-blue-100 text-blue-800">部分</span>
                        @elseif($payable->status === 'overdue')
                            <span class="px-2 py-0.5 inline-flex text-xs font-semibold rounded-full bg-red-100 text-red-800">逾期</span>
                        @else
                            <span class="px-2 py-0.5 inline-flex text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">待付</span>
                        @endif
                    </td>
                    <td class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400 max-w-[120px]">
                        <div class="truncate" title="{{ $payable->note }}">{{ $payable->note ?? '—' }}</div>
                    </td>
                    <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">
                        @if($bank)
                            <div class="text-xs">{{ $bank->bank_name ?? '' }}</div>
                            <div class="text-xs text-gray-500">{{ $bank->bank_account ?? '' }}</div>
                        @else
                            <span class="text-gray-300">—</span>
                        @endif
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        @date($payable->paid_date)
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-center text-gray-700 dark:text-gray-300">
                        {{ $payable->fiscal_year ?? '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="14" class="px-4 py-4 text-center text-gray-500 dark:text-gray-400 text-sm">目前沒有應付帳款資料</td>
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

<!-- 出帳 Modal -->
<div id="quickPayModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-start justify-center pt-16 px-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-[540px] max-w-full max-h-[80vh] flex flex-col">
        <!-- Header -->
        <div class="px-5 pt-5 pb-3 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">出帳 - <span id="qp_payment_no"></span></h3>
                <button onclick="closeQuickPayModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="mt-1 text-sm text-gray-500 dark:text-gray-400 space-y-0.5">
                <div id="qp_payee_row" class="hidden">對象：<span id="qp_payee_name" class="font-medium text-gray-700 dark:text-gray-200"></span></div>
                <div id="qp_content_row" class="hidden">說明：<span id="qp_content" class="font-medium text-gray-700 dark:text-gray-200"></span></div>
                <div id="qp_project_row" class="hidden">專案：<span id="qp_project" class="font-medium text-gray-700 dark:text-gray-200"></span></div>
                <div id="qp_total_row" class="hidden">帳款金額：NT$ <span id="qp_total" class="font-medium text-gray-700 dark:text-gray-200"></span></div>
            </div>
        </div>
        <!-- Scrollable body -->
        <div class="overflow-y-auto flex-1 px-5 py-4 space-y-4">
            <!-- Add form -->
            <div id="qp_add_form">
                <form id="quickPayForm" method="POST" class="space-y-3">
                    @csrf
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">出帳日期 <span class="text-red-500">*</span></label>
                            <input type="date" name="payment_date" id="qp_date" value="{{ date('Y-m-d') }}" required
                                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">出帳金額 <span class="text-red-500">*</span></label>
                            <input type="number" name="amount" id="qp_amount" step="{{ $decimalPlaces > 0 ? '0.'.str_repeat('0',$decimalPlaces-1).'1' : '1' }}" min="0.01" required
                                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 -mt-1">剩餘應付：NT$ <span id="qp_remaining" class="font-medium text-red-500"></span></p>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">付款方式</label>
                        <select name="payment_method" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                            <option value="">請選擇</option>
                            @foreach($paymentMethods as $m)
                                <option value="{{ $m->name }}">{{ $m->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">備註</label>
                        <input type="text" name="note" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div class="flex justify-end gap-2 pt-1">
                        <button type="button" onclick="closeQuickPayModal()"
                                class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg">取消</button>
                        <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg">確認出帳</button>
                    </div>
                </form>
            </div>
            <!-- Payment history -->
            <div>
                <h4 class="text-s font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">出帳記錄</h4>
                <div id="qp_history_loading" class="text-xs text-gray-400 py-2">載入中…</div>
                <table id="qp_history_table" class="hidden w-full text-s">
                    <thead>
                        <tr class="text-xs border-b border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400">
                            <th class="py-1 text-left w-6">#</th>
                            <th class="py-1 text-left">日期</th>
                            <th class="py-1 text-left">方式</th>
                            <th class="py-1 text-right">金額</th>
                            <th class="py-1 text-left pl-2">備註</th>
                            <th class="py-1 w-8"></th>
                        </tr>
                    </thead>
                    <tbody id="qp_history_body" ></tbody>
                </table>
                <p id="qp_no_history" class="hidden text-xs text-gray-400 py-1">尚無出帳記錄</p>
            </div>
        </div>
    </div>
</div>

<script>
let _qpPayableId = null;

function openQuickPayModal(id, remaining, paymentNo, payeeName, content, projectName, totalAmount) {
    _qpPayableId = id;
    document.getElementById('qp_payment_no').textContent = paymentNo;
    document.getElementById('qp_remaining').textContent = fmtNum(remaining);
    document.getElementById('qp_amount').value = remaining > 0 ? remaining : '';
    document.getElementById('qp_amount').max = remaining > 0 ? remaining : '';
    document.getElementById('quickPayForm').action = '/payable-payments/' + id;

    const payeeRow = document.getElementById('qp_payee_row');
    if (payeeName) { document.getElementById('qp_payee_name').textContent = payeeName; payeeRow.classList.remove('hidden'); }
    else { payeeRow.classList.add('hidden'); }

    const contentRow = document.getElementById('qp_content_row');
    if (content) { document.getElementById('qp_content').textContent = content; contentRow.classList.remove('hidden'); }
    else { contentRow.classList.add('hidden'); }

    const projectRow = document.getElementById('qp_project_row');
    if (projectName) { document.getElementById('qp_project').textContent = projectName; projectRow.classList.remove('hidden'); }
    else { projectRow.classList.add('hidden'); }

    const totalRow = document.getElementById('qp_total_row');
    if (totalAmount !== undefined) { document.getElementById('qp_total').textContent = fmtNum(totalAmount); totalRow.classList.remove('hidden'); }
    else { totalRow.classList.add('hidden'); }

    // Show/hide add form
    if (remaining <= 0) {
        document.getElementById('qp_add_form').classList.add('hidden');
    } else {
        document.getElementById('qp_add_form').classList.remove('hidden');
    }

    document.getElementById('quickPayModal').classList.remove('hidden');
    loadPayHistory(id);
}

function closeQuickPayModal() {
    document.getElementById('quickPayModal').classList.add('hidden');
}

function loadPayHistory(id) {
    const loading = document.getElementById('qp_history_loading');
    const table   = document.getElementById('qp_history_table');
    const noHist  = document.getElementById('qp_no_history');
    const tbody   = document.getElementById('qp_history_body');
    loading.classList.remove('hidden');
    table.classList.add('hidden');
    noHist.classList.add('hidden');
    tbody.innerHTML = '';

    fetch('/payable-payments/' + id + '/list')
        .then(r => r.json())
        .then(data => {
            loading.classList.add('hidden');
            if (!data.length) { noHist.classList.remove('hidden'); return; }
            data.forEach((p, i) => {
                const tr = document.createElement('tr');
                tr.className = 'border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-750';
                tr.innerHTML = `
                    <td class="py-1.5 text-gray-400">${i+1}</td>
                    <td class="py-1.5 text-gray-700 dark:text-gray-300">${(p.payment_date||'').substring(0,10)}</td>
                    <td class="py-1.5 text-gray-600 dark:text-gray-400">${p.payment_method||'—'}</td>
                    <td class="py-1.5 text-right font-medium text-red-600 dark:text-red-400">NT$ ${fmtNum(p.amount)}</td>
                    <td class="py-1.5 pl-2 text-gray-500 dark:text-gray-400 max-w-[80px] truncate">${p.note||''}</td>
                    <td class="py-1.5 text-right">
                        <form method="POST" action="/payable-payments/${p.id}" onsubmit="return confirm('確認刪除此出帳記錄？')" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-red-600 text-xs">刪除</button>
                        </form>
                    </td>`;
                tbody.appendChild(tr);
            });
            table.classList.remove('hidden');
        })
        .catch(() => { loading.textContent = '載入失敗'; });
}

// Close on backdrop click
document.getElementById('quickPayModal').addEventListener('click', function(e) {
    if (e.target === this) closeQuickPayModal();
});

// ===== View mode toggle =====
function setPayableView(mode) {
    localStorage.setItem('payableViewMode', mode);
    applyPayableView(mode);
}

function applyPayableView(mode) {
    const isSummary = mode === 'summary';
    document.getElementById('view-summary').classList.toggle('hidden', !isSummary);
    document.getElementById('view-disbursement').classList.toggle('hidden', isSummary);
    const btnSummary = document.getElementById('btn-view-summary');
    const btnDisb = document.getElementById('btn-view-disbursement');
    if (isSummary) {
        btnSummary.classList.add('bg-primary', 'text-white');
        btnSummary.classList.remove('text-gray-600', 'dark:text-gray-300', 'bg-white', 'dark:bg-gray-800');
        btnDisb.classList.remove('bg-primary', 'text-white');
        btnDisb.classList.add('text-gray-600', 'dark:text-gray-300', 'bg-white', 'dark:bg-gray-800');
    } else {
        btnDisb.classList.add('bg-primary', 'text-white');
        btnDisb.classList.remove('text-gray-600', 'dark:text-gray-300', 'bg-white', 'dark:bg-gray-800');
        btnSummary.classList.remove('bg-primary', 'text-white');
        btnSummary.classList.add('text-gray-600', 'dark:text-gray-300', 'bg-white', 'dark:bg-gray-800');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const saved = localStorage.getItem('payableViewMode') || 'summary';
    applyPayableView(saved);
});

// ===== Batch payment =====
function updateBatchBar() {
    const checked = document.querySelectorAll('.payable-checkbox:checked');
    const bar = document.getElementById('batch-bar');
    document.getElementById('batch-count').textContent = checked.length;
    bar.classList.toggle('hidden', checked.length === 0);
}

function toggleAllPayables(master) {
    document.querySelectorAll('.payable-checkbox').forEach(cb => cb.checked = master.checked);
    updateBatchBar();
}

function clearBatchSelection() {
    document.querySelectorAll('.payable-checkbox').forEach(cb => cb.checked = false);
    const all = document.getElementById('select-all');
    if (all) all.checked = false;
    updateBatchBar();
}

function openBatchPayModal() {
    const ids = Array.from(document.querySelectorAll('.payable-checkbox:checked')).map(cb => cb.value);
    if (!ids.length) return;
    document.getElementById('bp-count').textContent = ids.length;
    document.getElementById('batchPayModal').classList.remove('hidden');
}

function closeBatchPayModal() {
    document.getElementById('batchPayModal').classList.add('hidden');
}

document.getElementById('batchPayModal').addEventListener('click', function(e) {
    if (e.target === this) closeBatchPayModal();
});

document.getElementById('batchPayForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const ids = Array.from(document.querySelectorAll('.payable-checkbox:checked')).map(cb => cb.value);
    const date = document.getElementById('bp_date').value;
    const method = document.getElementById('bp_method').value;
    const note = document.getElementById('bp_note').value;

    fetch('{{ route("tenant.payables.batch-pay") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ ids, payment_date: date, payment_method: method, note })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            closeBatchPayModal();
            clearBatchSelection();
            location.reload();
        } else {
            alert(data.message || '操作失敗');
        }
    });
});
</script>

<!-- 批次支付 Modal -->
<div id="batchPayModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-start justify-center pt-24 px-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-[420px] max-w-full p-6 space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">批次確認支付</h3>
            <button onclick="closeBatchPayModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <p class="text-sm text-gray-500 dark:text-gray-400">已選 <strong id="bp-count" class="text-gray-800 dark:text-gray-200">0</strong> 筆，支付全額剩餘應付金額</p>
        <form id="batchPayForm" class="space-y-3">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">支付日期 <span class="text-red-500">*</span></label>
                <input type="date" id="bp_date" value="{{ date('Y-m-d') }}" required
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">付款方式</label>
                <select id="bp_method" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                    <option value="">請選擇</option>
                    @foreach($paymentMethods as $m)
                        <option value="{{ $m->name }}">{{ $m->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">備註</label>
                <input type="text" id="bp_note" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeBatchPayModal()" class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 rounded-lg">取消</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg">確認批次支付</button>
            </div>
        </form>
    </div>
</div>
@endsection
