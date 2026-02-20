@extends('layouts.tenant')

@section('title', '給付記錄')

@section('page-title', '給付記錄')

@section('content')
<!-- 麵包屑 -->
<div class="mb-4">
    <p class="text-sm text-gray-600 dark:text-gray-400">
        <a href="{{ route('tenant.payables.index') }}" class="hover:text-primary">應付帳款</a> &gt;
        <a href="{{ route('tenant.projects.show', $payable->project_id) }}" class="hover:text-primary">{{ $payable->project->name ?? '專案' }}</a> &gt;
        給付記錄（薪資入帳）
    </p>
</div>

<!-- 頁面標題 -->
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">給付記錄 #{{ $payable->payment_no }}</h1>
</div>

<!-- 應付資訊 -->
<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
    <div class="flex justify-between items-center mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
            應付資訊
        </h2>
        @if($payable->payments->count() > 0)
            <form action="{{ route('tenant.payables.reset-payments', $payable) }}" method="POST">
                @csrf
                <button type="submit" class="text-sm bg-red-500 hover:bg-red-600 text-white font-medium py-1 px-3 rounded">
                    重設給付資料
                </button>
            </form>
        @endif
    </div>
    <ul class="space-y-2 text-blue-600 dark:text-blue-400 font-medium">
        <li><strong>給付對象：</strong>
            @if($payable->payee_type === 'member' && $payable->payeeUser)
                {{ $payable->payeeUser->name }}（成員）
            @elseif($payable->payee_type === 'vendor' && $payable->payeeCompany)
                {{ $payable->payeeCompany->name }}（外製）
            @else
                {{ $payable->content ?? '-' }}
            @endif
        </li>
        <li><strong>專案名稱：</strong>{{ $payable->project->name ?? '-' }}</li>
        <li><strong>內容：</strong>{{ $payable->content ?? '-' }}</li>
        <li><strong>總金額：</strong>NT$ {{ number_format($payable->amount, 0) }}</li>
        <li><strong>已給付：</strong>NT$ {{ number_format($totalPaid, 0) }}</li>
        <li><strong>剩餘應付：</strong>NT$ {{ number_format($remainingAmount, 0) }}</li>
    </ul>
</div>

<!-- 給付記錄列表 -->
<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">
        給付記錄
    </h2>
    
    @if($payable->payments->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">日期</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">金額</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">方式</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">比例</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">備註</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">操作</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($payable->payments as $payment)
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                @date($payment->payment_date)
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-900 dark:text-white">
                                NT$ {{ number_format($payment->amount, 0) }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                {{ $payment->payment_method ?? '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-600 dark:text-gray-400">
                                {{ round($payment->amount / $payable->amount * 100, 1) }}%
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                {{ $payment->note ?? '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center text-sm">
                                <form action="{{ route('tenant.payable-payments.destroy', $payment) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                        刪除
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-gray-500 dark:text-gray-400 text-center py-4">尚無給付記錄</p>
    @endif
</div>

<!-- 新增給付 -->
<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">
        新增給付
    </h2>
    
    <form action="{{ route('tenant.payable-payments.store', $payable) }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <em class="text-red-500">*</em> 給付日期
                </label>
                <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    剩餘應付
                </label>
                <div class="px-4 py-2 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg">
                    NT$ {{ number_format($remainingAmount, 0) }}
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <em class="text-red-500">*</em> 給付金額
                </label>
                <div class="flex gap-2">
                    <span class="px-3 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-l-lg border border-r-0 border-gray-300 dark:border-gray-600">NT$</span>
                    <input type="number" name="amount" step="1" min="0" max="{{ $remainingAmount }}" required
                           class="flex-1 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-r-lg px-4 py-2">
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    給付方式
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
                          class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2"
                          placeholder="輸入給付備註..."></textarea>
            </div>
        </div>
        
        <div class="flex gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
            <button type="submit"
                    class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-6 rounded-lg">
                儲存
            </button>
            <a href="{{ route('tenant.projects.show', $payable->project_id) }}"
               class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-medium py-2 px-6 rounded-lg">
                返回專案
            </a>
        </div>
    </form>
</div>
@endsection
        入帳記錄
    </h2>
    
    @if($payments->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">日期</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">金額</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">方式</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">比例</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">備註</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">操作</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($payments as $payment)
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $payment->payment_date }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-900 dark:text-white">
                                NT$ {{ number_format($payment->amount, 0) }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                {{ $payment->payment_method ?? '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-600 dark:text-gray-400">
                                {{ round($payment->amount / $receivable->amount * 100, 1) }}%
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                {{ $payment->note ?? '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center text-sm">
                                <form action="{{ route('tenant.receivable-payments.destroy', $payment) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                        刪除
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
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
