@extends('layouts.tenant')

@section('title', '應付帳款詳情')

@section('page-title', '應付帳款詳情')

@section('content')
<!-- 麵包屑 -->
<div class="mb-4">
    <p class="text-sm text-gray-600 dark:text-gray-400">
        <a href="{{ route('tenant.payables.index') }}" class="hover:text-primary">財務管理</a> &gt; 
        <a href="{{ route('tenant.payables.index') }}" class="hover:text-primary">應付帳款管理</a> &gt; 
        詳情
    </p>
</div>

<!-- 頁面標題 -->
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">應付帳款詳情</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">付款單號：{{ $payable->payment_no }}</p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('tenant.payables.edit', $payable) }}"
           class="bg-primary hover:bg-primary-dark text-white font-medium py-2 px-4 rounded-lg">
            編輯
        </a>
        <a href="{{ route('tenant.payables.index') }}"
           class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-medium py-2 px-4 rounded-lg">
            返回列表
        </a>
    </div>
</div>

<!-- 詳細資訊 -->
<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
    <div class="p-6">
        <!-- 基本資訊 -->
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">
            基本資訊
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div>
                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">付款單號</label>
                <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $payable->payment_no }}</p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">付款日期</label>
                <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $payable->payment_date->format('Y/m/d') }}</p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">專案</label>
                <p class="mt-1 text-gray-900 dark:text-gray-100">
                    @if($payable->project)
                        {{ $payable->project->code }} - {{ $payable->project->name }}
                    @else
                        <span class="text-gray-400">未指定</span>
                    @endif
                </p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">廠商</label>
                <p class="mt-1 text-gray-900 dark:text-gray-100">
                    @if($payable->company)
                        {{ $payable->company->name }}
                    @else
                        <span class="text-gray-400">未指定</span>
                    @endif
                </p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">負責人</label>
                <p class="mt-1 text-gray-900 dark:text-gray-100">
                    @if($payable->responsibleUser)
                        {{ $payable->responsibleUser->name }}
                    @else
                        <span class="text-gray-400">未指定</span>
                    @endif
                </p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">到期日</label>
                <p class="mt-1 text-gray-900 dark:text-gray-100">
                    {{ $payable->due_date ? $payable->due_date->format('Y/m/d') : '-' }}
                </p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">狀態</label>
                <p class="mt-1">
                    @if($payable->status === 'paid')
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">已收款</span>
                    @elseif($payable->status === 'partial')
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">部分收款</span>
                    @elseif($payable->status === 'overdue')
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">逾期</span>
                    @else
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">待收款</span>
                    @endif
                </p>
            </div>
        </div>

        <!-- 金額資訊 -->
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">
            金額資訊
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div>
                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">應收金額</label>
                <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">NT$ {{ number_format($payable->amount, 0) }}</p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">稅前金額</label>
                <p class="mt-1 text-gray-900 dark:text-gray-100">NT$ {{ number_format($payable->amount_before_tax ?? 0, 0) }}</p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">稅率</label>
                <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $payable->tax_rate ?? 0 }}%</p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">稅額</label>
                <p class="mt-1 text-gray-900 dark:text-gray-100">NT$ {{ number_format($payable->tax_amount ?? 0, 0) }}</p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">扣繳稅額</label>
                <p class="mt-1 text-gray-900 dark:text-gray-100">NT$ {{ number_format($payable->withholding_tax ?? 0, 0) }}</p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">已收金額</label>
                <p class="mt-1 text-lg font-semibold text-green-600 dark:text-green-400">NT$ {{ number_format($payable->received_amount ?? 0, 0) }}</p>
            </div>
        </div>

        <!-- 計算欄位 -->
        <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800 mb-8">
            <h3 class="text-sm font-medium text-blue-900 dark:text-blue-300 mb-3">自動計算欄位</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium text-blue-700 dark:text-blue-400">未收金額</label>
                    <p class="mt-1 text-lg font-semibold text-red-600 dark:text-red-400">
                        NT$ {{ number_format($payable->remaining_amount, 0) }}
                    </p>
                    <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">= 應收金額 - 已收金額</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-blue-700 dark:text-blue-400">實際入帳</label>
                    <p class="mt-1 text-lg font-semibold text-blue-600 dark:text-blue-400">
                        NT$ {{ number_format($payable->net_amount, 0) }}
                    </p>
                    <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">= 已收金額 - 扣繳稅額</p>
                </div>
            </div>
        </div>

        <!-- 付款資訊 -->
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">
            付款資訊
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div>
                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">付款方式</label>
                <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $payable->payment_method ?? '-' }}</p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">實際收款日</label>
                <p class="mt-1 text-gray-900 dark:text-gray-100">
                    {{ $payable->paid_date ? $payable->paid_date->format('Y/m/d') : '-' }}
                </p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">發票號碼</label>
                <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $payable->invoice_no ?? '-' }}</p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">報價單號</label>
                <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $payable->quote_no ?? '-' }}</p>
            </div>
        </div>

        <!-- 其他資訊 -->
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">
            其他資訊
        </h2>
        <div class="grid grid-cols-1 gap-6">
            <div>
                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">內容</label>
                <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $payable->content ?? '-' }}</p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">備註</label>
                <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $payable->note ?? '-' }}</p>
            </div>
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">建立時間</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $payable->created_at->format('Y/m/d H:i') }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">最後更新</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $payable->updated_at->format('Y/m/d H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
