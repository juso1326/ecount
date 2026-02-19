@extends('layouts.tenant')

@section('title', '應付帳款詳情與給付記錄')

@section('page-title', '給付記錄 - ' . $payable->payment_no)

@section('content')
<!-- 麵包屑 -->
<div class="mb-4">
    <p class="text-sm text-gray-600 dark:text-gray-400">
        <a href="{{ route('tenant.payables.index') }}" class="hover:text-primary">應付帳款</a> &gt;
        @if($payable->project)
            <a href="{{ route('tenant.projects.show', $payable->project_id) }}" class="hover:text-primary">{{ $payable->project->name }}</a> &gt;
        @endif
        給付記錄
    </p>
</div>

<!-- 操作按鈕 -->
<div class="mb-4 flex gap-3">
    <a href="{{ route('tenant.payables.edit', $payable) }}"
       class="bg-primary hover:bg-primary-dark text-white font-medium py-2 px-4 rounded-lg">
        編輯應付帳款
    </a>
    <a href="{{ route('tenant.payables.index') }}"
       class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-medium py-2 px-4 rounded-lg">
        返回列表
    </a>
</div>

<!-- 應付資訊 -->
<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
    <div class="flex justify-between items-center mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
            應付資訊
        </h2>
        @if(isset($payable->payments) && $payable->payments->count() > 0)
            <form action="{{ route('tenant.payables.reset-payments', $payable) }}" method="POST" 
                  onsubmit="return confirm('確定要重設給付資料嗎？這將清除所有給付記錄，此操作無法復原。');">
                @csrf
                <button type="submit" class="text-sm bg-red-500 hover:bg-red-600 text-white font-medium py-1 px-3 rounded">
                    重設給付資料
                </button>
            </form>
        @endif
    </div>
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
        <div><span class="text-gray-600 dark:text-gray-400">付款單號：</span><span class="font-medium">{{ $payable->payment_no }}</span></div>
        <div><span class="text-gray-600 dark:text-gray-400">付款日期：</span><span class="font-medium">{{ $payable->payment_date->format('Y/m/d') }}</span></div>
        <div><span class="text-gray-600 dark:text-gray-400">到期日：</span><span class="font-medium">{{ $payable->due_date ? $payable->due_date->format('Y/m/d') : '-' }}</span></div>
        <div><span class="text-gray-600 dark:text-gray-400">專案：</span><span class="font-medium">{{ $payable->project->name ?? '-' }}</span></div>
        <div><span class="text-gray-600 dark:text-gray-400">廠商：</span><span class="font-medium">{{ $payable->company->name ?? '-' }}</span></div>
        <div><span class="text-gray-600 dark:text-gray-400">負責人：</span><span class="font-medium">{{ $payable->responsibleUser->name ?? '-' }}</span></div>
        <div><span class="text-gray-600 dark:text-gray-400">內容：</span><span class="font-medium">{{ $payable->content ?? '-' }}</span></div>
        <div><span class="text-gray-600 dark:text-gray-400">總金額：</span><span class="font-medium text-blue-600 dark:text-blue-400">NT$ {{ number_format($payable->amount, 0) }}</span></div>
        <div><span class="text-gray-600 dark:text-gray-400">已付款：</span><span class="font-medium text-green-600 dark:text-green-400">NT$ {{ number_format($payable->paid_amount ?? 0, 0) }}</span></div>
        <div><span class="text-gray-600 dark:text-gray-400">剩餘應付：</span><span class="font-medium text-red-600 dark:text-red-400">NT$ {{ number_format($payable->remaining_amount, 0) }}</span></div>
        <div><span class="text-gray-600 dark:text-gray-400">狀態：</span>
            @if($payable->status === 'paid')
                <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full bg-green-100 text-green-800">已付</span>
            @elseif($payable->status === 'partial')
                <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full bg-blue-100 text-blue-800">部分</span>
            @elseif($payable->status === 'overdue')
                <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full bg-red-100 text-red-800">逾期</span>
            @else
                <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">待付</span>
            @endif
        </div>
    </div>
</div>

<!-- 給付記錄與新增給付（左右並排） -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- 給付記錄（左側） -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">
            給付記錄
        </h2>
        
        @if(isset($payable->payments) && $payable->payments->count() > 0)
            <div class="space-y-2">
                @foreach($payable->payments as $payment)
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $payment->payment_date->format('Y/m/d') }}
                                <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">{{ $payment->payment_method ?? '未指定方式' }}</span>
                            </div>
                            @if($payment->note)
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $payment->note }}</div>
                            @endif
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="text-right">
                                <div class="text-sm font-semibold text-gray-900 dark:text-white">NT$ {{ number_format($payment->amount, 0) }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ round($payment->amount / $payable->amount * 100, 1) }}%</div>
                            </div>
                            <form action="{{ route('tenant.payable-payments.destroy', $payment) }}" method="POST" 
                                  onsubmit="return confirm('確定要刪除此給付記錄嗎？');">
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
            <p class="text-gray-500 dark:text-gray-400 text-center py-4">尚無給付記錄</p>
        @endif
    </div>

    <!-- 新增給付（右側） -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">
            新增給付
        </h2>
        
        <form action="{{ route('tenant.payable-payments.store', $payable) }}" method="POST">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <em class="text-red-500">*</em> 給付日
                    </label>
                    <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <em class="text-red-500">*</em> 給付金額
                    </label>
                    <input type="number" name="amount" value="{{ $payable->remaining_amount }}" required min="0" step="1"
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2"
                           placeholder="建議: {{ number_format($payable->remaining_amount, 0) }}">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        給付方式
                    </label>
                    <select name="payment_method" 
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2">
                        <option value="">請選擇</option>
                        <option value="現金">現金</option>
                        <option value="轉帳">轉帳</option>
                        <option value="支票">支票</option>
                        <option value="信用卡">信用卡</option>
                        <option value="其他">其他</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        備註
                    </label>
                    <input type="text" name="note" 
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2"
                           placeholder="選填">
                </div>
            </div>
            
            <div class="mt-6 flex gap-3">
                <button type="submit" class="bg-primary hover:bg-primary-dark text-white font-medium py-2 px-6 rounded-lg">
                    記錄給付
                </button>
                <a href="{{ route('tenant.payables.index') }}" 
                   class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-medium py-2 px-6 rounded-lg">
                    取消
                </a>
            </div>
        </form>
    </div>
</div>

<!-- 詳細資訊（摺疊區） -->
<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 mt-6" x-data="{ open: false }">
    <div class="p-6 cursor-pointer" @click="open = !open">
        <div class="flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                完整詳細資訊
            </h2>
            <svg class="w-5 h-5 transform transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </div>
    </div>
    
    <div x-show="open" x-collapse class="px-6 pb-6 space-y-6">
        <!-- 金額資訊 -->
        <div>
            <h3 class="text-md font-semibold text-gray-900 dark:text-white mb-3 pb-2 border-b border-gray-200 dark:border-gray-700">
                金額資訊
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                <div><span class="text-gray-600 dark:text-gray-400">應付金額：</span><span class="font-medium">NT$ {{ number_format($payable->amount, 0) }}</span></div>
                <div><span class="text-gray-600 dark:text-gray-400">稅前金額：</span><span class="font-medium">NT$ {{ number_format($payable->amount_before_tax ?? 0, 0) }}</span></div>
                <div><span class="text-gray-600 dark:text-gray-400">稅率：</span><span class="font-medium">{{ $payable->tax_rate ?? 0 }}%</span></div>
                <div><span class="text-gray-600 dark:text-gray-400">稅額：</span><span class="font-medium">NT$ {{ number_format($payable->tax_amount ?? 0, 0) }}</span></div>
                <div><span class="text-gray-600 dark:text-gray-400">扣繳稅額：</span><span class="font-medium">NT$ {{ number_format($payable->withholding_tax ?? 0, 0) }}</span></div>
                <div><span class="text-gray-600 dark:text-gray-400">實際給付：</span><span class="font-medium text-blue-600 dark:text-blue-400">NT$ {{ number_format($payable->net_amount, 0) }}</span></div>
            </div>
        </div>

        <!-- 付款資訊 -->
        <div>
            <h3 class="text-md font-semibold text-gray-900 dark:text-white mb-3 pb-2 border-b border-gray-200 dark:border-gray-700">
                付款資訊
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                <div><span class="text-gray-600 dark:text-gray-400">付款方式：</span><span class="font-medium">{{ $payable->payment_method ?? '-' }}</span></div>
                <div><span class="text-gray-600 dark:text-gray-400">實際付款日：</span><span class="font-medium">{{ $payable->paid_date ? $payable->paid_date->format('Y/m/d') : '-' }}</span></div>
                <div><span class="text-gray-600 dark:text-gray-400">發票號碼：</span><span class="font-medium">{{ $payable->invoice_no ?? '-' }}</span></div>
                <div><span class="text-gray-600 dark:text-gray-400">報價單號：</span><span class="font-medium">{{ $payable->quote_no ?? '-' }}</span></div>
            </div>
        </div>

        <!-- 其他資訊 -->
        <div>
            <h3 class="text-md font-semibold text-gray-900 dark:text-white mb-3 pb-2 border-b border-gray-200 dark:border-gray-700">
                其他資訊
            </h3>
            <div class="space-y-2 text-sm">
                <div><span class="text-gray-600 dark:text-gray-400">備註：</span><span class="font-medium">{{ $payable->note ?? '-' }}</span></div>
                <div class="grid grid-cols-2 gap-3 pt-2 border-t border-gray-100 dark:border-gray-700">
                    <div><span class="text-gray-600 dark:text-gray-400">建立時間：</span><span class="text-xs">{{ $payable->created_at->format('Y/m/d H:i') }}</span></div>
                    <div><span class="text-gray-600 dark:text-gray-400">最後更新：</span><span class="text-xs">{{ $payable->updated_at->format('Y/m/d H:i') }}</span></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
