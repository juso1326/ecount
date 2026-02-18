@extends('layouts.tenant')

@section('title', '快速收款')

@section('page-title', '快速收款 - ' . $receivable->receipt_no)

@section('content')
<!-- 麵包屑 -->
<div class="mb-4">
    <p class="text-sm text-gray-600 dark:text-gray-400">
        <a href="{{ route('tenant.receivables.index') }}" class="hover:text-primary">應收帳款</a> &gt;
        <a href="{{ route('tenant.projects.show', $receivable->project_id) }}" class="hover:text-primary">{{ $receivable->project->name ?? '專案' }}</a> &gt;
        快速收款
    </p>
</div>

<!-- 應收資訊 -->
<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
    <div class="flex justify-between items-center mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
            應收資訊
        </h2>
        @if($payments->count() > 0)
            <form action="{{ route('tenant.receivables.reset-payments', $receivable) }}" method="POST" 
                  onsubmit="return confirm('確定要重設收款資料嗎？這將清除所有入帳記錄，此操作無法復原。');">
                @csrf
                <button type="submit" class="text-sm bg-red-500 hover:bg-red-600 text-white font-medium py-1 px-3 rounded">
                    重設收款資料
                </button>
            </form>
        @endif
    </div>
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
        <div><span class="text-gray-600 dark:text-gray-400">客戶：</span><span class="font-medium">{{ $receivable->company->name ?? '-' }}</span></div>
        <div><span class="text-gray-600 dark:text-gray-400">專案：</span><span class="font-medium">{{ $receivable->project->name ?? '-' }}</span></div>
        <div><span class="text-gray-600 dark:text-gray-400">內容：</span><span class="font-medium">{{ $receivable->content ?? '-' }}</span></div>
        <div><span class="text-gray-600 dark:text-gray-400">總金額：</span><span class="font-medium text-blue-600 dark:text-blue-400">NT$ {{ number_format($receivable->amount, 0) }}</span></div>
        <div><span class="text-gray-600 dark:text-gray-400">已收款：</span><span class="font-medium text-green-600 dark:text-green-400">NT$ {{ number_format($totalReceived, 0) }}</span></div>
        <div><span class="text-gray-600 dark:text-gray-400">剩餘應收：</span><span class="font-medium text-red-600 dark:text-red-400">NT$ {{ number_format($remainingAmount, 0) }}</span></div>
    </div>
</div>

<!-- 收款記錄 -->
<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">
        收款記錄
    </h2>
    
    @if($payments->count() > 0)
        <div class="space-y-2">
            @foreach($payments as $payment)
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <div class="flex-1">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $payment->payment_date }} 
                            <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">{{ $payment->payment_method ?? '未指定方式' }}</span>
                        </div>
                        @if($payment->note)
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $payment->note }}</div>
                        @endif
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="text-right">
                            <div class="text-sm font-semibold text-gray-900 dark:text-white">NT$ {{ number_format($payment->amount, 0) }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ round($payment->amount / $receivable->amount * 100, 1) }}%</div>
                        </div>
                        <form action="{{ route('tenant.receivable-payments.destroy', $payment) }}" method="POST" 
                              onsubmit="return confirm('確定要刪除此收款記錄嗎？');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-gray-500 dark:text-gray-400 text-center py-4">尚無入帳記錄</p>
    @endif
</div>

<!-- 新增入帳 -->
<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">
        新增入帳
    </h2>
    
    <form action="{{ route('tenant.receivable-payments.store', $receivable) }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <em class="text-red-500">*</em> 給付日
                </label>
                <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    應收
                </label>
                <div class="px-4 py-2 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg">
                    NT$ {{ number_format($remainingAmount, 0) }}
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <em class="text-red-500">*</em> 實收
                </label>
                <div class="flex gap-2">
                    <span class="px-3 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-l-lg border border-r-0 border-gray-300 dark:border-gray-600">NT$</span>
                    <input type="number" name="amount" step="1" min="0" max="{{ $remainingAmount }}" required
                           class="flex-1 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-r-lg px-4 py-2">
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    收款方式
                </label>
                <select name="payment_method"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2">
                    <option value="">請選擇</option>
                    @foreach($paymentMethods as $method)
                        <option value="{{ $method->name }}">{{ $method->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    備註
                </label>
                <textarea name="note" rows="2"
                          class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2"></textarea>
            </div>
        </div>
        
        <div class="flex gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
            <button type="submit"
                    class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-6 rounded-lg">
                儲存
            </button>
            <a href="{{ route('tenant.projects.show', $receivable->project_id) }}"
               class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-medium py-2 px-6 rounded-lg">
                返回專案
            </a>
        </div>
    </form>
</div>
@endsection
